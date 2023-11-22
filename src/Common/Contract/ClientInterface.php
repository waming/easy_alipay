<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay\Common\Contract;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface ClientInterface
{
    public function getApp(): AppInterface;

    public function getConfig(): ConfigInterface;

    public function getRequest(): RequestInterface;

    public function getResponse(): responseInterface;
}