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
        'app_auth_token' => '',
    ];

    protected array $defaultHeaders = [
        'Content-Type' => 'application/json; charset=utf-8',
        'Accept'       => 'application/json'
    ];

    public function __construct(protected AppInterface $app)
    {
        $config = $app->config->get('http');
        $config['headers'] = array_merge($this->defaultHeaders, $config['headers']?? []);
        $this->client = new Client($config);
        $this->createDefaultSysApiParams();
    }

    protected function createDefaultSysApiParams() : void
    {
        $this->sysApiParams['app_id']    = $this->app->getAppId();
        $this->sysApiParams['timestamp'] = date('Y-m-d H:i:s');

        /**
         * 公钥证书方式
         */
        if(!empty($this->app->getPublicCertPath()) && !empty($this->app->getAlipayRootCertPath())) {
            $this->sysApiParams['app_cert_sn']         = Utils::getCertSN($this->app->getPublicCertPath());
            $this->sysApiParams['alipay_root_cert_sn'] = Utils::getRootCertSN($this->app->getAlipayRootCertPath());
        }
    }

    /**
     * @throws ClientExceptionInterface
     * @throws InvalidParamException
     */
    public function get(string $uri, array $data = [], array $headers = []) : ResponseInterface
    {
        return $this->request($uri, 'GET', $data, $headers);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws InvalidParamException
     */
    public function post(string $uri, array $data = [], array $headers = []) : ResponseInterface
    {
        return $this->request($uri, 'POST', $data, $headers);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws InvalidParamException
     */
    protected function request(string $uri, string $method, array $data = [], array $headers = []): ResponseInterface
    {
        if(empty($data['method'])) {
            throw new InvalidParamException('Please check method param.');
        }
        $uri .= '?'.http_build_query($this->getApiParams($data));
        $request = new Request($method, $uri, $headers, $data);
        return $this->sendRequest($request);
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->client->send($request);
    }

    /**
     * 获取系统请求参数
     * @param array $data
     * @return array
     */
    protected function getApiParams(array $data) : array
    {
        $this->sysApiParams['method'] = $data['method'];

        if(!empty($data['app_auth_token'])) {
            $this->sysApiParams['app_auth_token'] = $data['app_auth_token'];
        }
        unset($data['method'], $data['app_auth_token']);
        $this->sysApiParams['biz_content'] = json_encode($data);
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