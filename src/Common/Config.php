<?php

declare(strict_types=1);

namespace Honghm\EasyAlipay\Common;
use ArrayAccess;
use Honghm\EasyAlipay\Common\Contract\ConfigInterface;
use Honghm\EasyAlipay\Common\Support\Arr;

class Config implements ArrayAccess, ConfigInterface
{
    /**
     * Config constructor.
     *
     * @param  array<string,mixed>  $config
     */
    public function __construct(protected array $config = [])
    {}

    public function all(): array
    {
        return $this->config;
    }

    public function offsetExists(mixed $key): bool
    {
        return $this->has(strval($key));
    }

    public function offsetGet(mixed $key): mixed
    {
        return $this->get(strval($key));
    }

    public function offsetSet(mixed $key, mixed $value): void
    {
        $this->set(strval($key), $value);
    }

    public function offsetUnset(mixed $key): void
    {
        $this->set(strval($key), null);
    }

    public function has(string $key): bool
    {
        return Arr::has($this->config, $key);
    }

    public function set(string $key, mixed $value = null): void
    {
        Arr::set($this->config, $key, $value);
    }

    public function get(array|string $key, mixed $default = null): mixed
    {
        return Arr::get($this->config, $key, $default);
    }
}