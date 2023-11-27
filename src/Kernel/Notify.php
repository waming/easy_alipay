<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay\Kernel;

use Honghm\EasyAlipay\Application;
use Honghm\EasyAlipay\Kernel\Exception\InvalidConfigException;
use Honghm\EasyAlipay\Kernel\Exception\InvalidParamException;
use Honghm\EasyAlipay\Kernel\Exception\InvalidResponseException;
use Honghm\EasyAlipay\Kernel\Support\Arr;
use Psr\Http\Message\ResponseInterface;
use function Honghm\EasyAlipay\Kernel\Support\getPublicCert;

/**
 * 异步通知类
 */
class Notify
{
    /**
     * @throws InvalidResponseException
     * @throws InvalidParamException
     * @throws InvalidConfigException
     */
    public function __construct(public Application $application)
    {
        $this->sign();
    }

    /**
     * 验证签名
     * @throws InvalidParamException
     * @throws InvalidConfigException
     * @throws InvalidResponseException
     */
    protected function sign() : void
    {
        $data = $this->application->getRequest()->getParsedBody();

        if(empty($data) || !is_array($data)) {
            throw new InvalidParamException("Error notify Data, please check.", 500);
        }

        $sign     = $data['sign'];
        $signType = $data['sign_type'];
        unset($data['sign']);
        unset($data['sign_type']);
        $this->verify($this->getCheckSignContent($data), base64_decode($sign), $signType);
    }

    /**
     * @throws InvalidConfigException
     * @throws InvalidResponseException
     */
    protected function verify(string $signContent, string $sign, string $signType) : void
    {
        $public = $this->application->getConfig()->get('alipayPublicCertPath');

        if(empty($public)) {
            throw new InvalidConfigException('Missing Alipay Config[alipayPublicCertPath].', 400);
        }

        $result = (openssl_verify($signContent, $sign, getPublicCert($public),OPENSSL_ALGO_SHA256) === 1);

        if (!$result) {
            throw new InvalidResponseException('Fail sign', 501);
        }
    }

    protected function getCheckSignContent(array $params) : string
    {
        ksort($params);
        return urldecode(Arr::toString($params));
    }

    /**
     *  获取通知类参数
     *  @return array
     */
    public function callback() : array
    {
        return $this->application->getRequest()->getParsedBody();
    }

    /**
     * 成功处理
     * @return ResponseInterface
     */
    public function success() : ResponseInterface
    {
        return Response::create(200, [], 'success');
    }
}