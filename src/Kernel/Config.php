<?php

declare(strict_types=1);

namespace Honghm\EasyAlipay\Kernel;
use ArrayAccess;
use Honghm\EasyAlipay\Kernel\Contract\ConfigInterface;
use Honghm\EasyAlipay\Kernel\Support\Arr;

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

    public function offsetExists(mixed $offset): bool
    {
        return $this->has(strval($offset));
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get(strval($offset));
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set(strval($offset), $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->set(strval($offset), null);
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
