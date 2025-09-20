<?php

namespace PHPFramework;

class Application {
    protected string $uri;
    protected Request $request;
    protected Response $response;
    protected Router $router;
    protected View $view;

    public function __construct()
    {
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->request = new Request($this->uri);
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        $this->view = new View(LAYOUT);
    }

    public function router() : Router
    {
        return $this->router;
    }

    public function request() : Request
    {
        return $this->request;
    }

    public function view() : View
    {
        return $this->view;
    }

    public function run() : void
    {
        echo $this->router->dispatch();
    }
}