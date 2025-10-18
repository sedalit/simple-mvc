<?php

namespace PHPFramework;

class Container {
    protected array $container = [];

    public function set(string $key, mixed $value) : void
    {
        $this->container[$key] = $value;
    }

    public function get(string $key, mixed $default = null) : mixed
    {
        return $this->container[$key] ?? $default;
    }

    public function has(string $key) : bool
    {
        return array_key_exists($key, $this->container);
    }
}