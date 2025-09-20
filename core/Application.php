<?php

namespace PHPFramework;

class Application {
    public static Application $instance;

    protected string $uri;
    protected Request $request;
    protected Response $response;
    protected Router $router;
    protected View $view;

    public function __construct()
    {
        self::$instance = $this;

        $this->uri = $_SERVER['REQUEST_URI'];
        $this->request = new Request($this->uri);
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        $this->view = new View(LAYOUT);
    }

    public static function router() : Router
    {
        return self::$instance->router;
    }

    public static function request() : Request
    {
        return self::$instance->request;
    }

    public static function view() : View
    {
        return self::$instance->view;
    }

    public static function response() : Response
    {
        return self::$instance->response;
    }

    public function run() : void
    {
        echo $this->router->dispatch();
    }
}