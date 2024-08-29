<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay\Kernel;

use Honghm\EasyAlipay\Kernel\Contract\AlipayResponseInterface;
use Honghm\EasyAlipay\Kernel\Contract\ApplicationInterface;
use Honghm\EasyAlipay\Kernel\Exception\InvalidResponseException;
use Honghm\EasyAlipay\Kernel\Exception\InvalidResponseJsonException;
use Honghm\EasyAlipay\Kernel\Support\Utils;
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
                                public ResponseInterface $response)
    {
        $this->rawContent = $this->response->getBody()->getContents();
        if($this->isSuccess()) {
            $this->verifyResponse();
            $this->decrypt();
        } else {
            $this->resposeData = json_decode($this->rawContent, true);
        }
    }

    /**
     * 验签
     * @throws InvalidResponseException
     */
    public function verifyResponse() : bool
    {
        $sign = $this->response->getHeaderLine('alipay-signature');
        if (empty($sign)) {
            return true;
        };

        $alipayCertSN = $this->response->getHeaderLine('alipay-sn');
        $timestamp    = $this->response->getHeaderLine('alipay-timestamp');
        $nonce        = $this->response->getHeaderLine('alipay-nonce');
        $content      = $this->rawContent;

        //读取公钥文件
        $pubKey = file_get_contents($this->application->getApp()->getAlipayPublicCertPath());
        //转换为openssl格式密钥
        $res = openssl_get_publickey($pubKey);

        $content = $timestamp . "\n"
                    . $nonce . "\n"
                    . $content . "\n";

        //调用openssl内置方法验签，返回bool值
        $verify = (openssl_verify($content, base64_decode($sign), $res, OPENSSL_ALGO_SHA256) === 1);
        if(!$verify) {
            throw new InvalidResponseException('Fail sign' . $this->rawContent, 501);
        }

        return true;
    }

    /**
     * 解密参数
     * @return void
     * @throws InvalidResponseJsonException
     */
    public function decrypt() : void
    {
        $encryptKey = $this->application->getApp()->getAppSecret();
        if(empty($encryptKey)) { //不需要解密
            $data = json_decode($this->rawContent, true);
            if (!is_array($data)) {
                throw new InvalidResponseJsonException($this->rawContent, json_last_error());
            }
            $this->resposeData = $data;
            return;
        }

        $str        = base64_decode($this->rawContent);
        $screct_key = base64_decode($encryptKey);
        $iv = str_repeat("\0", 16);
        $decrypt_str = openssl_decrypt($str, 'aes-128-cbc', $screct_key, OPENSSL_NO_PADDING, $iv);
        $content = Utils::stripPKSC7Padding($decrypt_str);
        $data = json_decode($content, true);
        if (!is_array($data)) {
            throw new InvalidResponseJsonException($this->rawContent, json_last_error());
        }
        $this->resposeData = $data;
    }

    public function getRawContent(): string
    {
        return $this->rawContent;
    }

    public function getData() : array
    {
        return $this->resposeData;
    }

    public function getRawData() : array
    {
        return $this->resposeData;
    }

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function isSuccess(): bool
    {
        return $this->getStatusCode() == 200;
    }
}
