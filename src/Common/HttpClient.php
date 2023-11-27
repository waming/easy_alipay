<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay\Common;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use Honghm\EasyAlipay\Common\Contract\AppInterface;
use Honghm\EasyAlipay\Common\Contract\ApplicationInterface;
use Honghm\EasyAlipay\Common\Exception\InvalidParamException;
use Honghm\EasyAlipay\Common\Support\Utils;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface as PsrClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpClient implements PsrClientInterface
{
    protected ClientInterface $client;

    protected AppInterface $app;

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

    public function __construct(protected ApplicationInterface $application)
    {
        $config = $application->getConfig()->get('http');
        $config['headers'] = array_merge($this->defaultHeaders, $config['headers'] ?? []);
        $this->app = $this->application->getApp();
        $this->client = new Client($config);
    }

    /**
     * 适用于生成页面的接口。比如支付宝的手机网站支付
     * @throws Exception\InvalidConfigException
     */
    public function getPageResponse(string $apiName, array $data, string $method = 'GET') : ResponseInterface
    {
        if('GET' === strtoupper($method)) {
            $query = $this->getRequestBody(array_merge($data, $this->getApiParams($apiName, $data)));
            $uri = $this->app->config->get('http')['base_uri'].'?'.$query;
            return Response::create(302, ['Location' => $uri]);
        } else {
            return $this->buildHtml($apiName, $data);
        }
    }

    /**
     * @throws Exception\InvalidConfigException
     */
    protected function buildHtml(string $apiName, array $data): ResponseInterface
    {
        $gateWay = $this->application->getConfig()->get('http')['base_uri'].'?charset='.$this->sysApiParams['charset'];
        $params  = array_merge($this->getApiParams($apiName, $data), $data);

        $sHtml = "<form id='alipay_submit' name='alipay_submit' action='".$gateWay."' method='POST'>";
        foreach ($params as $key => $val) {
            $val = str_replace("'", '&apos;', (string)$val);
            $sHtml .= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }
        $sHtml .= "<input type='submit' value='ok' style='display:none;'></form>";
        $sHtml .= "<script>document.forms['alipay_submit'].submit();</script>";

        return Response::create(200, [], $sHtml);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws InvalidParamException
     * @throws Exception\InvalidConfigException
     */
    public function get(string $apiName, array $data = [], array $headers = []) : ResponseInterface
    {
        return $this->request($apiName, 'GET', $data, $headers);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws InvalidParamException
     * @throws Exception\InvalidConfigException
     */
    public function post(string $apiName, array $data = [], array $headers = []) : ResponseInterface
    {
        return $this->request($apiName, 'POST', $data, $headers);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws InvalidParamException|Exception\InvalidConfigException
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

    /**
     * @throws Exception\InvalidConfigException
     */
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
        return http_build_query($this->checkBizContent($data), '', '&', $encodingType);
    }

    protected function checkBizContent(array $data) : array
    {
        if( !empty($data['biz_content']) ) {
            $data['biz_content'] = json_encode($data['biz_content']);
        }

        return $data;
    }

    /**
     * 获取系统请求参数
     * @param string $apiName 接口名称
     * @param array $data
     * @return array
     * @throws Exception\InvalidConfigException
     */
    protected function getApiParams(string $apiName, array $data) : array
    {
        $this->sysApiParams['app_id']    = $this->app->getAppId();
        $this->sysApiParams['timestamp'] = date('Y-m-d H:i:s');

        /**
         * 公钥证书方式
         */
        if(!empty($this->app->getAppPublicCertPath()) && !empty($this->app->getAlipayRootCertPath())) {
            $this->sysApiParams['app_cert_sn']         = Utils::getAppCertSN($this->app->getAppPublicCertPath());
            $this->sysApiParams['alipay_root_cert_sn'] = Utils::getRootCertSN($this->app->getAlipayRootCertPath());
        }

        if(isset($data['biz_content'])) {
            $params = $data['biz_content'];
            $data['biz_content'] = json_encode($data['biz_content']);
        } else {
            $params = $data;
        }

        $this->sysApiParams['method']         = $apiName;
        $this->sysApiParams['notify_url']     = $this->getNotifyUrl($params);
        $this->sysApiParams['return_url']     = $this->getReturnUrl($params);
        $this->sysApiParams['app_auth_token'] = $this->getAppAuthToken($params);
        $this->sysApiParams['sign']           = $this->getSign(array_merge($this->sysApiParams, $data));
        return $this->sysApiParams;
    }

    protected function getNotifyUrl(array $params)
    {
        if (!empty($params['_notify_url'])) {
            return $params['_notify_url'];
        }

        return $this->app->config->get('notify_url', '');
    }

    protected function getReturnUrl(array $params)
    {
        if (!empty($params['return_url'])) {
            return $params['return_url'];
        }

        return $this->app->config->get('return_url', '');
    }

    protected function getAppAuthToken(array $params)
    {
        if (!empty($params['app_auth_token'])) {
            return $params['app_auth_token'];
        }

        return $this->app->config->get('app_auth_token', '');
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
            if (!empty($v) && !str_starts_with((string)$v, "@")) {

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