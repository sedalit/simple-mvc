<?php

namespace PHPFramework;

use PHPFramework\Validation\Validator;

class Application {
    public static Application $instance;

    protected string $uri;
    protected Request $request;
    protected Response $response;
    protected Router $router;
    protected View $view;
    protected Validator $validator;
    protected Database $database;
    protected Session $session;

    public function __construct()
    {
        self::$instance = $this;

        $this->uri = $_SERVER['REQUEST_URI'];
        $this->request = new Request($this->uri);
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        $this->view = new View(LAYOUT);
        $this->validator = new Validator();
        $this->database = new Database();
        $this->session = new Session();
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

    public static function validator() : Validator
    {
        return self::$instance->validator;
    }

    public static function database() : Database
    {
        return self::$instance->database;
    }

    public static function session() : Session
    {
        return self::$instance->session;
    }

    public function run() : void
    {
        echo $this->router->dispatch();
    }
}