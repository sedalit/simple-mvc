<?php

namespace PHPFramework;

class Application {
    protected string $uri;
    protected Request $request;
    protected Response $response;
    protected Router $router;

    public function __construct()
    {
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->request = new Request($this->uri);
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
    }

    public function router() : Router
    {
        return $this->router;
    }

    public function run() : void
    {
        echo $this->router->dispatch();
    }
}