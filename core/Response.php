<?php

namespace PHPFramework;

class Response {
    public function setCode(int $code) : void
    {
        http_response_code($code);
    }
}