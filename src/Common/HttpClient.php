<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay\Common;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use Honghm\EasyAlipay\Common\Contract\AppInterface;
use Honghm\EasyAlipay\Common\Exception\InvalidParamException;
use Honghm\EasyAlipay\Common\Support\Utils;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface as PsrClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpClient implements PsrClientInterface
{
    protected ClientInterface $client;

    /*
     * 系统请求参数，生成的链接中会自动添加这些参数
     */
    protected array $sysApiParams = [
        'app_id'    => '',
        'method'    => '',
        'format'    => 'JSON',
        'charset'   => 'utf-8',
        'sign_type' => 'RSA2',
        'timestamp' => '',
        'version'   => '1.0',
    ];

    protected array $defaultHeaders = [
        'Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8',
        'Accept'       => 'application/json'
    ];

    /**
     * @throws Exception\InvalidConfigException
     */
    public function __construct(protected AppInterface $app)
    {
        $config = $app->config->get('http');
        $config['headers'] = array_merge($this->defaultHeaders, $config['headers'] ?? []);
        $this->client = new Client($config);
        $this->createDefaultSysApiParams();
    }

    /**
     * @throws Exception\InvalidConfigException
     */
    protected function createDefaultSysApiParams() : void
    {
        $this->sysApiParams['app_id']    = $this->app->getAppId();
        $this->sysApiParams['timestamp'] = date('Y-m-d H:i:s');
        $this->sysApiParams['notify_url'] = $this->app->config->get('notify_url');

        /**
         * 公钥证书方式
         */
        if(!empty($this->app->getAppPublicCertPath()) && !empty($this->app->getAlipayRootCertPath())) {
            $this->sysApiParams['app_cert_sn']         = Utils::getAppCertSN($this->app->getAppPublicCertPath());
            $this->sysApiParams['alipay_root_cert_sn'] = Utils::getRootCertSN($this->app->getAlipayRootCertPath());
        }
    }

    /**
     * @throws ClientExceptionInterface
     * @throws InvalidParamException
     */
    public function get(string $apiName, array $data = [], array $headers = []) : ResponseInterface
    {
        return $this->request($apiName, 'GET', $data, $headers);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws InvalidParamException
     */
    public function post(string $apiName, array $data = [], array $headers = []) : ResponseInterface
    {
        return $this->request($apiName, 'POST', $data, $headers);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws InvalidParamException
     */
    protected function request(string $apiName, string $method, array $data = [], array $headers = []): ResponseInterface
    {
        if(empty($apiName)) {
            throw new InvalidParamException('Please check method param.');
        }

        $request = new Request($method, $this->getUri($apiName, $data), $headers, $this->getRequestBody($data));
        return $this->sendRequest($request);
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->client->send($request);
    }

    protected function getUri(string $apiName, array $data) : string
    {
        $uri = '?';
        $params = $this->getApiParams($apiName, $data);
        foreach ($params as $sysParamKey => $sysParamValue) {
            $tempParm = $sysParamValue ? urlencode($sysParamValue): '';
            $uri .= "$sysParamKey=" . $tempParm . "&";
        }
        return substr($uri, 0, -1);
    }

    protected function getRequestBody(array $data, int $encodingType = PHP_QUERY_RFC1738) : string
    {
        return http_build_query($data, '', '&', $encodingType);
    }

    /**
     * 获取系统请求参数
     * @param string $apiName 接口名称
     * @param array $data
     * @return array
     */
    protected function getApiParams(string $apiName, array $data) : array
    {
        $this->sysApiParams['method']      = $apiName;
        $this->sysApiParams['sign']        = $this->getSign(array_merge($this->sysApiParams, $data));
        return $this->sysApiParams;
    }

    /**
     * 获取签名字符
     * @param array $params 需要签名的数据
     * @return string
     */
    private function getSign(array $params) : string
    {
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (!empty($v) && !str_starts_with($v, "@")) {

                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }
        unset ($k, $v);

        $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($this->app->getAppPrivateKey(), 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";

        openssl_sign($stringToBeSigned, $sign, $res, OPENSSL_ALGO_SHA256);

        return base64_encode($sign);
    }
}