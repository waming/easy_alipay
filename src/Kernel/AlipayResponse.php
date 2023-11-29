<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay\Kernel;

use Honghm\EasyAlipay\Kernel\Contract\AlipayResponseInterface;
use Honghm\EasyAlipay\Kernel\Contract\ApplicationInterface;
use Honghm\EasyAlipay\Kernel\Exception\InvalidResponseException;
use Honghm\EasyAlipay\Kernel\Exception\InvalidResponseJsonException;
use Psr\Http\Message\ResponseInterface;
use function Honghm\EasyAlipay\Kernel\Support\getPublicCert;

/**
 * 返回请求，处理了验签、响应内容的封装
 */
class AlipayResponse implements AlipayResponseInterface
{
    protected array $resposeData;

    protected string $rawContent;

    /**
     * @throws InvalidResponseJsonException
     * @throws InvalidResponseException
     */
    public function __construct(protected ApplicationInterface $application,
                                public string $apiName,
                                public ResponseInterface $response)
    {
        $this->setResponseData();
        $this->verifySign();
    }

    /**
     * @throws InvalidResponseJsonException
     */
    protected function setResponseData() : void
    {
        $this->rawContent = $this->response->getBody()->getContents();
        $data = json_decode($this->rawContent, true);
        if (!is_array($data)) {
            throw new InvalidResponseJsonException($this->rawContent, json_last_error());
        }
        $this->resposeData = $data;
    }

    /**
     * 验证支付宝返回的请求数据，防止篡改
     * @return void
     * @throws InvalidResponseJsonException
     * @throws InvalidResponseException
     */
    protected function verifySign() : void
    {
        $response_key = $this->getResultKey();
        $content      = $this->resposeData[$response_key] ?? $this->resposeData['error_response'];
        if (empty($this->resposeData['sign']) || empty($content)) {
            throw new InvalidResponseJsonException($this->rawContent, 500);
        }

        $public      = $this->application->getConfig()->get('alipayPublicCertPath');
        $signContent = json_encode($content, JSON_UNESCAPED_UNICODE);

        $sign = base64_decode($this->resposeData['sign'], true);
        $result = (openssl_verify($signContent, $sign, getPublicCert($public), OPENSSL_ALGO_SHA256) === 1);

        if (!$result) {

            //特殊字符串
            if (strpos($signContent, "\\/") > 0) {
                $signContent = str_replace("\\/", "/", $signContent);
                $result = (openssl_verify($signContent, $sign, getPublicCert($public), OPENSSL_ALGO_SHA256) === 1);
            }
        }

        if (!$result) {
            throw new InvalidResponseException('Fail sign' . $this->rawContent, 501);
        }
    }

    protected function getResultKey() : string
    {
        return str_replace('.', '_', $this->apiName).'_response';
    }

    public function getResponse() : ResponseInterface
    {
        return $this->response;
    }

    public function getData() : array
    {
        return $this->resposeData['error_response'] ?? $this->resposeData[$this->getResultKey()];
    }

    public function getRawData() : array
    {
        return $this->resposeData;
    }
}