<?php
declare(strict_types=1);
namespace Honghm\EasyAlipay\Kernel;

use Honghm\EasyAlipay\Kernel\Contract\ApplicationInterface;
use Honghm\EasyAlipay\Kernel\Exception\InvalidConfigException;
use Honghm\EasyAlipay\Kernel\Support\Utils;
use Honghm\EasyAlipay\Kernel\Contract\AppInterface;

class Pay
{
    protected AppInterface $app;

    public function __construct(protected ApplicationInterface $application)
    {
        $this->app = $this->application->getApp();
    }

    protected array $sysApiParams = [
        'app_id'    => '',
        'method'    => '',
        'format'    => 'JSON',
        'charset'   => 'utf-8',
        'sign_type' => 'RSA2',
        'timestamp' => '',
        'version'   => '1.0',
    ];


    /**
     * v3版本不支持的获取wab,web获取支付网页地址
     * @param string $apiName
     * @param array $data
     * @param string $method
     * @return string
     * @throws InvalidConfigException
     */
    public function pageExecute(string $apiName, array $data, string $method = 'GET'): string
    {
        if('GET' === strtoupper($method)) {
            $query = $this->getRequestBody(array_merge($data, $this->getApiParams($apiName, $data)));
            return $this->app->config->get('http')['base_uri'].'/gateway.do?'.$query;
        } else {
            return $this->buildHtml($apiName, $data);
        }
    }

    /**
     * @throws InvalidConfigException
     */
    protected function buildHtml(string $apiName, array $data): string
    {
        $gateWay = $this->app->config->get('http')['base_uri'].'/gateway.do?charset=';
        $params  = array_merge($this->getApiParams($apiName, $data), $data);

        $sHtml = "<form id='alipay_submit' name='alipay_submit' action='".$gateWay."' method='POST'>";
        foreach ($params as $key => $val) {
            $val = str_replace("'", '&apos;', (string)$val);
            $sHtml .= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }
        $sHtml .= "<input type='submit' value='ok' style='display:none;'></form>";
        $sHtml .= "<script>document.forms['alipay_submit'].submit();</script>";
        return $sHtml;
    }

    protected function getRequestBody(array $data, int $encodingType = PHP_QUERY_RFC1738) : string
    {
        return http_build_query($this->checkBizContent($data), '', '&', $encodingType);
    }

    protected function checkBizContent(array $data) : array
    {
        if (!empty($data['biz_content'])) {
            $data['biz_content'] = json_encode($data['biz_content']);
        }

        return $data;
    }

    /**
     * @throws InvalidConfigException
     */
    protected function getApiParams(string $apiName, array $data) : array
    {
        $this->sysApiParams['app_id']    = $this->app->getAppId();
        $this->sysApiParams['timestamp'] = date('Y-m-d H:i:s');

        if (!empty($this->app->getAppPublicCertPath()) && !empty($this->app->getAlipayRootCertPath())) {
            $this->sysApiParams['app_cert_sn']         = Utils::getAppCertSN($this->app->getAppPublicCertPath());
            $this->sysApiParams['alipay_root_cert_sn'] = Utils::getRootCertSN($this->app->getAlipayRootCertPath());
        }

        if (isset($data['biz_content'])) {
            $params              = $data['biz_content'];
            $data['biz_content'] = json_encode($data['biz_content']);
        } else {
            $params = $data;
        }

        $this->sysApiParams['method']         = $apiName;
        $this->sysApiParams['notify_url']     = $params['notify_url'] ?? '';
        $this->sysApiParams['return_url']     = $params['return_url'] ?? '';
        $this->sysApiParams['app_auth_token'] = $params['app_auth_token'] ?? '';
        $this->sysApiParams['sign']           = $this->getSign(array_merge($this->sysApiParams, $data));
        return $this->sysApiParams;
    }

    protected function getSign(array $params): string
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
