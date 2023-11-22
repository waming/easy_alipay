<?php
declare(strict_types=1);

namespace Honghm\EasyAlipay\Common\Contract;
use ArrayAccess;

/**
 * 配置接口
 */
interface ConfigInterface extends ArrayAccess
{
    /**
     * @return array<string,mixed>
     */
    public function all(): array;

    public function has(string $key): bool;

    public function set(string $key, mixed $value = null): void;

    /**
     * @param  array<string>|string  $key
     */
    public function get(array|string $key, mixed $default = null): mixed;
}