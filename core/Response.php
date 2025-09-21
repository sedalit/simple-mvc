<?php

namespace PHPFramework;

class Response {
    public function setCode(int $code) : void
    {
        http_response_code($code);
    }

    public function redirect(string $url = '') : never
    {
        $redirect = $url;

        if (!$redirect) {
            $redirect = $_SERVER['HTTP_REFERER'] ?? baseUrl();
        }

        header("Location: {$redirect}");
        die;
    }
}