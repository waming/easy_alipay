<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay\Kernel\Contract;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface ApplicationInterface
{
    /**
     * 获取app配置
     * @return AppInterface
     */
    public function getApp(): AppInterface;

    /**
     * 获取所有的配置
     * @return ConfigInterface
     */
    public function getConfig(): ConfigInterface;

    /**
     * 获取request
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface;

    /**
     * 获取http client
     * @return ClientInterface
     */
    public function getHttpClient(): ClientInterface;
}