<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay\Kernel\Contract;

/**
 * 支付宝应用接口
 */
interface AppInterface
{
    /**
     * 应用appid
     * @return string
     */
    public function getAppId(): string;

    /**
     * 应用私钥
     * @return string
     */
    public function getAppPrivateKey(): string;

    /**
     * 支付宝公钥
     * @return string
     */
    public function getAlipayPublicKey(): string;

    /**
     * 应用公钥证书路径
     * @return string
     */
    public function getAppPublicCertPath(): string;

    /**
     * 支付宝公钥证书路径
     */
    public function getAlipayPublicCertPath(): string;

    /**
     * 支付宝根证书路径
     * @return string
     */
     public function getAlipayRootCertPath(): string;
}