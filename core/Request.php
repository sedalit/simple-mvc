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
        return $this->removeQueryString();
    }

    public function getMethod() : string
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if (isset($_POST[self::METHOD_HIDDEN_KEY])) {
            $method = $_POST[self::METHOD_HIDDEN_KEY];
        }

        return strtoupper($method);
    }

    public function removeQueryString() : string
    {
        if ($this->uri) {
            $separators = ['&', '?'];

            foreach ($separators as $separator) {
                $params = explode($separator, $this->uri);
                
                if (!str_contains($params[0], '=')) {
                    return trim($params[0], '/');
                }
            }
        }

        return "";
    }

    public function get(string $name = '', mixed $default = null) : mixed
    {
        if ($name) {
            return $_GET[$name] ?? $default;
        }
        
        return $_GET;
    }

    public function post(string $name = '', mixed $default = null) : mixed
    {
        if ($name) {
            return $_POST[$name] ?? $default;
        }
        
        return $_POST;
    }

    public function isGet() : bool
    {
        return $this->getMethod() == 'GET';
    }

    public function isPost() : bool
    {
        return $this->getMethod() == 'POST';
    }

    public function getData() : array
    {
        $data = [];

        $requestData = $this->isGet() ? $_GET : $_POST;

        foreach ($requestData as $key => $value) {
            $data[$key] = trim($value);
        }

        return $data;
    }

    public function file(string $name, mixed $default = []) : mixed
    {
        return $_FILES[$name] ?? $default;
    }
}