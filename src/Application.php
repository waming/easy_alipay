<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay;

use Honghm\EasyAlipay\Kernel\App;
use Honghm\EasyAlipay\Kernel\Config;
use Honghm\EasyAlipay\Kernel\Contract\AppInterface;
use Honghm\EasyAlipay\Kernel\Contract\ApplicationInterface;
use Honghm\EasyAlipay\Kernel\Contract\ConfigInterface;
use Honghm\EasyAlipay\Kernel\Contract\HttpClientInterface;
use Honghm\EasyAlipay\Kernel\HttpClient;
use JetBrains\PhpStorm\Pure;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Application implements ApplicationInterface
{
    protected ?AppInterface $app = null;

    protected ?ServerRequestInterface $request = null;

    protected ?HttpClientInterface $client = null;

    protected ?ResponseInterface $response = null;

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

    public function getRequest(): ServerRequestInterface
    {
        if( empty($this->request) ) {
            return $this->createDefaultRequest();
        }

        return $this->request;
    }

    public function setRequest(ServerRequestInterface $request): static
    {
        $this->request = $request;
        return $this;
    }

    /**
     * 创建默认request
     * @return ServerRequestInterface
     */
    protected function createDefaultRequest() : ServerRequestInterface
    {
        $psr17Factory = new Psr17Factory();
        $creator = new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        return $creator->fromGlobals();
    }

    public function getHttpClient() : HttpClientInterface
    {
        if (empty($this->client)) {
            return $this->createDefaultHttpClient();
        }
        return $this->client;
    }

    /**
     * 使用guzzlehttp客户端
     * @return HttpClientInterface
     */
    protected function createDefaultHttpClient(): HttpClientInterface
    {
        return new HttpClient($this);
    }
}