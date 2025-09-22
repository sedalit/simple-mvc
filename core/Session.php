<?php

namespace PHPFramework;

class Session {

    public function __construct()
    {
        session_start();
    }

    public function set(string $key, mixed $value) : void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key, mixed $default = null) : mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function forget(string $key) : void
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public function setFlash(string $key, mixed $value) : void
    {
        $_SESSION['flash'][$key] = $value;
    }

    public function getFlash(string $key)
    {
        if (isset($_SESSION['flash'][$key])) {
            $value = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
        }

        return $value ?? null;
    }
}