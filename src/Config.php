<?php

declare(strict_types = 1);

namespace App;

use ArrayAccess;

class Config implements ArrayAccess
{
    public function __construct(public readonly array $config)
    {}

    public function offsetExists($offset): bool {
        return isset($this->config[$offset]);
    }

    public function offsetGet($offset): mixed {
        return $this->config[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void {
        throw new \RuntimeException("Cannot modify configuration in this way");
    }

    public function offsetUnset($offset): void {
        throw new \RuntimeException("Cannot unset configuration");
    }
}