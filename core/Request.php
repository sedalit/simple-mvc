<?php

namespace PHPFramework;

class Request {
    protected string $uri;

    public function __construct(string $uri)
    {
        $this->uri = trim(urldecode($uri), '/');
    }
}