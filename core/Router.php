<?php

namespace PHPFramework;

class Router {
    protected const GET = 'GET';
    protected const POST = 'POST';
    protected const PUT = 'PUT';
    protected const DELETE = 'DELETE';

    protected Request $request;
    protected Response $response;
    protected array $routes = [];

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function getRoutes() : array
    {
        return $this->routes;
    }

    public function get(string $path, object $callback) : void
    {
        $this->addRoute(self::GET, $path, $callback);
    }

    public function post(string $path, object $callback) : void
    {
        $this->addRoute(self::POST, $path, $callback);
    }

    public function put(string $path, object $callback) : void
    {
        $this->addRoute(self::PUT, $path, $callback);

    }

    public function delete(string $path, object $callback) : void
    {
        $this->addRoute(self::DELETE, $path, $callback);
    }

    protected function addRoute(string $method, string $path, object $callback) : void
    {
        $this->routes[$method][$path] = $callback;
    }
}