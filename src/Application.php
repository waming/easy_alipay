<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay;

use Honghm\EasyAlipay\Common\App;
use Honghm\EasyAlipay\Common\Config;
use Honghm\EasyAlipay\Common\Contract\AppInterface;
use Honghm\EasyAlipay\Common\Contract\ApplicationInterface;
use Honghm\EasyAlipay\Common\Contract\ConfigInterface;
use Honghm\EasyAlipay\Common\HttpClient;
use JetBrains\PhpStorm\Pure;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

class Application implements ApplicationInterface
{
    protected ?AppInterface $app = null;

    protected ?RequestInterface $request = null;

    protected ?ClientInterface $client = null;

    public function __construct(public array $config = [])
    {}

    public function getApp(): AppInterface
    {
        if ( empty($this->app) ) {
            $this->app = new App($this->getConfig());
        }

        return $this->app;
    }

    #[Pure]
    public function getConfig(): ConfigInterface
    {
        return new Config($this->config);
    }

    public function getRequest(): RequestInterface
    {
        if( empty($this->request) ) {
            return $this->createDefaultRequest();
        }

        return $this->request;
    }

    protected function setRequest(RequestInterface $request): static
    {
        $this->request = $request;
        return $this;
    }

    /**
     * 创建默认request
     * @return RequestInterface
     */
    protected function createDefaultRequest() : RequestInterface
    {
        $psr17Factory = new Psr17Factory();
        $creator = new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        return $creator->fromGlobals();
    }

    public function getHttpClient(): ClientInterface
    {
        if (empty($this->client)) {
            return $this->createDefaultHttpClient();
        }
        return $this->client;
    }

    /**
     * 使用guzzlehttp客户端
     * @return ClientInterface
     */
    protected function createDefaultHttpClient() : ClientInterface
    {
        return new HttpClient($this->getApp());
    }
}