<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay\Kernel\Contract;

use Psr\Http\Message\ResponseInterface;

interface AlipayResponseInterface
{
    public function getRawContent(): string;

    /**
     * 只返回业务字段
     * @return array
     */
    public function getData() : array;

    /**
     * 数据原样返回
     * @return array
     */
    public function getRawData() : array;

    public function getStatusCode() : int;

    public function isSuccess() : bool;
}
