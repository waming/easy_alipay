<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay\Common;

use Honghm\EasyAlipay\Common\Contract\ConfigInterface;
use Honghm\EasyAlipay\Common\Contract\AppInterface;
use Honghm\EasyAlipay\Common\Exception\InvalidConfigException;

class App implements AppInterface
{
    public function __construct(public ConfigInterface $config)
    {}

    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    public function getAppId(): string
    {
        if (null === $this->config->get('appId')) {
            throw new InvalidConfigException('empty appid.');
        }

        return $this->config->get('appId');
    }

    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    public function getAppPrivateKey(): string
    {
        if (null === $this->config->get('privateKey')) {
            throw new InvalidConfigException('empty privateKey.');
        }

        return $this->config->get('privateKey');
    }

    /**
     * @inheritDoc
     */
    public function getAlipayPublicKey(): string
    {
        return $this->config->get('alipayPublicKey');
    }

    /**
     * @inheritDoc
     */
    public function getAppPublicCertPath(): string
    {
        return $this->config->get('appPublicCertPath');
    }

    /**
     * @inheritDoc
     */
    public function getAlipayPublicCertPath(): string
    {
        return $this->config->get('alipayPublicCertPath');
    }

    /**
     * @inheritDoc
     */
    public function getAlipayRootCertPath(): string
    {
        return $this->config->get('alipayRootCertPath');
    }
}