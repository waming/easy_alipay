<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay\Common;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response as BaseResponse;

class Response
{
    public static function create(int $status = 200,
                                  array $headers = [], $body = null,
                                  string $version = '1.1',
                                  string $reason = null) : ResponseInterface
    {
        return new BaseResponse($status, $headers, $body, $version, $reason);
    }
}