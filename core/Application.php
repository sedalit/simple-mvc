<?php

namespace PHPFramework;

class Application {
    protected string $uri;
    protected Request $request;
    protected Response $response;

    public function __construct()
    {
        $this->uri = $_SERVER['QUERY_STRING'];
        $this->request = new Request($this->uri);
        $this->response = new Response();
    }
}