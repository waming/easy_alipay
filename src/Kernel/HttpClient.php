<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay\Kernel;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Honghm\EasyAlipay\Kernel\Contract\AlipayResponseInterface;
use Honghm\EasyAlipay\Kernel\Contract\AppInterface;
use Honghm\EasyAlipay\Kernel\Contract\ApplicationInterface;
use Honghm\EasyAlipay\Kernel\Contract\HttpClientInterface;
use Honghm\EasyAlipay\Kernel\Exception\InvalidConfigException;
use Honghm\EasyAlipay\Kernel\Exception\InvalidParamException;
use Honghm\EasyAlipay\Kernel\Support\Utils;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface as PsrClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @method AlipayResponseInterface get(string $apiName, array $data = [])
 * @method AlipayResponseInterface post(string $apiName, array $options = [])
 */
class HttpClient implements HttpClientInterface
{
    protected PsrClientInterface $client;

    protected AppInterface $app;

    /*
     * 系统参数，传参
     */
    protected array $sysHeaders = [
        'authorization'          => '',
        'content-type'           => 'application/json;charset=utf-8',
        'alipay-request-id'      => '',
        'alipay-encrypt-type'    => 'AES',
        'alipay-root-cert-sn'    => '',
        'alipay-app-auth-token'  => '',
    ];

    public function __construct(protected ApplicationInterface $application)
    {
        $config = $application->getConfig()->get('http');
        $this->app = $this->application->getApp();
        $this->client = new Client($config);
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return AlipayResponseInterface
     * @throws ClientExceptionInterface
     * @throws InvalidParamException
     * @throws Exception
     */
    public function __call(string $method, array $arguments) : AlipayResponseInterface
    {
        return $this->request(strtoupper($method), ...$arguments);
    }

    /**
     * @param string $apiName
     * @param string $method
     * @param array $data
     * @param array $headers
     * @return AlipayResponseInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     * @throws InvalidParamException
     * @throws InvalidConfigException
     */
    protected function request(string $method, string $apiName, array $data = [], array $headers = []): AlipayResponseInterface
    {
        if(empty($apiName)) {
            throw new InvalidParamException('Please check method param.');
        }
        $headers = $this->setHeaders($method, $apiName, $data, $headers);
        $request = new Request($method, $this->getUri($apiName), $headers, $this->getRequestBody($data));
        $response = $this->sendRequest($request);
        return new AlipayResponse($this->application, $response);
    }

    /**
     * @throws InvalidConfigException
     */
    protected function setHeaders(string $method, string $apiName, array $data, array $headers): array
    {
        $this->getSysHeaders($method, $apiName, $data);

        if(empty($this->sysHeaders['alipay-app-auth-token'])) {
            unset($this->sysHeaders['alipay-app-auth-token']);
        }
        return array_merge($this->sysHeaders, $headers);
    }

    /**
     * 获取系统header参数
     * @param string $method
     * @param string $apiName
     * @param array $data
     * @return void
     * @throws InvalidConfigException
     */
    protected function getSysHeaders(string $method, string $apiName, array $data) : void
    {
        $this->sysHeaders['alipay-request-id'] = $this->getSysAlipayRequestId();

        //是否对请求参数加密
        if($this->isEncrypt() && !empty($data)) {
            $this->sysHeaders['content-type'] = 'text/plain;charset=utf-8';
        } else {
            $this->sysHeaders['content-type'] = 'application/json;charset=utf-8';
            unset($this->sysHeaders['alipay-encrypt-type']);
        }

        /**
         * 公钥证书方式
         */
        if(!empty($this->app->getAppPublicCertPath()) && !empty($this->app->getAlipayRootCertPath())) {
            $this->sysHeaders['alipay-root-cert-sn'] = Utils::getRootCertSN($this->app->getAlipayRootCertPath());
        }

        $authToken = $this->getAppAuthToken($data);
        if(!empty($authToken)) {
            $this->sysHeaders['alipay-app-auth-token'] = $authToken;
        }

        $this->sysHeaders['authorization'] = $this->sign($method, $apiName, $data);
    }

    /**
     * @throws InvalidConfigException
     */
    protected function sign(string $method, string $apiName, array $data) : string
    {
        $app_cert_no = Utils::getAppCertSN($this->app->getAppPublicCertPath());
        $nonce     = $this->createUuid();
        $timestamp = $this->getCurrentMilis();
        $httpRequestBody = $this->getRequestBody($data);
        $authString = 'app_id=' . $this->app->getAppId()
            . ($this->checkEmpty($app_cert_no) ? '' : ',app_cert_sn=' . $app_cert_no)
            . ',nonce=' . $nonce
            . ',timestamp=' . $timestamp;
        $content = $authString . "\n"
            . $method . "\n"
            . $apiName . "\n"
            . ($httpRequestBody) . "\n"
            . ($this->checkEmpty($this->sysHeaders['alipay-app-auth-token']) ? '' : $this->sysHeaders['alipay-app-auth-token'] . "\n");
        $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
        wordwrap($this->app->getAppPrivateKey(), 64, "\n", true) .
        "\n-----END RSA PRIVATE KEY-----";
        openssl_sign($content, $sign, $res, OPENSSL_ALGO_SHA256);
        return 'ALIPAY-SHA256withRSA' . ' ' . $authString . ',sign='. base64_encode($sign);
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->client->sendRequest($request);
    }

    protected function getUri(string $apiName) : string
    {
        return $apiName;
    }

    public function checkEmpty($value) : bool
    {
        if (!isset($value)) {
            return true;
        }
        if (trim($value) === "") {
            return true;
        }
        if(is_array($value) && count($value) == 0) {
            return true;
        }
        return false;
    }

    /**
     * 是否加密
     * @return bool
     */
    public function isEncrypt() : bool
    {
        return !$this->checkEmpty($this->app->getAppSecret());
    }

    protected function getRequestBody(array $data) : string
    {
        if(empty($data)) {
            return '';
        }

        $app_secret = $this->app->getAppSecret();
        $content = json_encode($data, JSON_UNESCAPED_UNICODE);
        if(empty($app_secret)) {
            return $content;
        }

        $screct_key = base64_decode($app_secret);
        $str = Utils::addPKCS7Padding($content);
        $iv = str_repeat("\0", 16);
        $encrypt_str = openssl_encrypt($str, 'aes-128-cbc', $screct_key, OPENSSL_NO_PADDING, $iv);
        return base64_encode($encrypt_str);
    }

    protected function getAppAuthToken(array $params)
    {
        if (!empty($params['_app_auth_token'])) {
            return $params['_app_auth_token'];
        }

        return $this->app->config->get('app_auth_token', '');
    }

    public function getSysAlipayRequestId(): string
    {
        return time().$this->application->getApp()->getAppId();
    }

    /**
     * 获取时间戳（毫秒）
     *
     * @return string
     */
    public function getCurrentMilis(): string
    {
        $timeInfo = explode(' ', microtime());
        return sprintf('%d%03d', $timeInfo[1], $timeInfo[0] * 1000);
    }

    public function createUuid() : string
    {
        $chars = md5(uniqid((string)mt_rand(), true));
        return substr($chars, 0, 8) . '-'
            . substr($chars, 8, 4) . '-'
            . substr($chars, 12, 4) . '-'
            . substr($chars, 16, 4) . '-'
            . substr($chars, 20, 12);
    }
}
