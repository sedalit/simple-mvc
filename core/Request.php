<?php

namespace PHPFramework;

class Request {
    protected const METHOD_HIDDEN_KEY = '_method';
    protected string $uri;

    public function __construct(string $uri)
    {
        $this->uri = trim(urldecode($uri), '/');
    }

    public function getPath() : string
    {
        return $this->uri;
    }

    public function getMethod() : string
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if (isset($_POST[self::METHOD_HIDDEN_KEY])) {
            $method = $_POST[self::METHOD_HIDDEN_KEY];
        }

        return strtoupper($method);
    }
}