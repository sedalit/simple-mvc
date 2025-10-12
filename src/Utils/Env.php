<?php

namespace PHPFramework\Utils;
use Symfony\Component\Dotenv\Dotenv;

class Env {
    protected static bool $isLoaded = false;

    public static function load(string $path = ENV_PATH) : void
    {
        $dotenv = new Dotenv();
        $dotenv->load($path);
        self::$isLoaded = true;
    }

    public static function get(string $key, mixed $default = '') : mixed
    {
        if (!self::$isLoaded) self::load();
        
        return $_ENV[$key] ?? $default;
    }
}