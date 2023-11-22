<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay;

use Honghm\EasyAlipay\Common\App;
use Honghm\EasyAlipay\Common\Config;
use Honghm\EasyAlipay\Common\Contract\AppInterface;
use Honghm\EasyAlipay\Common\Contract\ApplicationInterface;
use Honghm\EasyAlipay\Common\Contract\ConfigInterface;
use JetBrains\PhpStorm\Pure;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Application implements ApplicationInterface
{
    protected ?AppInterface $app = null;

    protected ?RequestInterface $request = null;

    public function __construct(public array $config = [])
    {}

    public function getApp(): AppInterface
    {
        if (!$this->app) {
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

    protected function createDefaultRequest() : RequestInterface
    {
        $psr17Factory = new Psr17Factory();
        $creator = new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        return $creator->fromGlobals();
    }

    public function getResponse(): responseInterface
    {
        // TODO: Implement getResponse() method.
    }

    public function getHttpClient(): ClientInterface
    {
        // TODO: Implement getHttpClient() method.
    }
}