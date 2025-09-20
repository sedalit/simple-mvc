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

    public function dispatch() : mixed
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method]["/{$path}"] ?? null;

        if (!$callback) {
            $this->response->setCode(404);
            return null;
        } else if (is_array($callback)) {
            $callback[0] = new $callback[0];
        }
        
        return call_user_func($callback);
    }

    public function get(string $path, object|array $callback) : void
    {
        $this->addRoute(self::GET, $path, $callback);
    }

    public function post(string $path, object|array $callback) : void
    {
        $this->addRoute(self::POST, $path, $callback);
    }

    public function put(string $path, object|array $callback) : void
    {
        $this->addRoute(self::PUT, $path, $callback);

    }

    public function delete(string $path, object|array $callback) : void
    {
        $this->addRoute(self::DELETE, $path, $callback);
    }

    protected function addRoute(string $method, string $path, object|array $callback) : void
    {
        $path = trim($path, '/');
        $this->routes[$method]["/{$path}"] = $callback;
    }
}