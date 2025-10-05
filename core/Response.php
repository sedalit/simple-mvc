<?php

namespace PHPFramework;

class Response {
    protected int $code = 200;
    protected array $headers = [];
    protected ?string $redirect = null;
    protected array $params = [];

    public function setCode(int $code) : self
    {
        $this->code = $code;

        return $this;
    }

    public function send(?int $code = null) : void
    {
        http_response_code($code === null ? $this->code : $code);

        extract($this->params);
        foreach ($this->headers as $key => $value) {
            header("{$key}: {$value}");
        }

        if ($this->redirect) {
            die;
        }
    }

    public function redirect(string $url = '', array $params = []) : self
    {
        $redirect = $url ?: ($_SERVER['HTTP_REFERER'] ?? baseUrl());
        $this->redirect = $redirect;

        $this->setCode(302);
        $this->headers['Location'] = $redirect;
        $this->params = $params;

        return $this;
    }

    public function getCode() : int
    {
        return $this->code;
    }

    public function getHeaders() : array
    {
        return $this->headers;
    }

    public function getRedirect() : ?string
    {
        return $this->redirect;
    }

    public function addHeader(string $name, string $value) : void
    {
        $this->headers[$name] = $value;
    }
}