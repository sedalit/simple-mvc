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
    protected array $routeParams = [];

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
        $callback = $this->matchRoute($method, $path);

        if (!$callback) {
            abort('Page not found');
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

    public function routeParams() : array
    {
        return $this->routeParams;
    }

    public function routeParam(string $key) : mixed
    {
        return $this->routeParams[$key] ?? null;
    }

    protected function addRoute(string $method, string $path, object|array $callback) : void
    {
        $path = trim($path, '/');
        $this->routes[$method]["/{$path}"] = $callback;
    }

    protected function matchRoute(string $method, string $path) : mixed
    {
        foreach ($this->routes[$method] as $pattern => $route) {
            if (preg_match("#^{$pattern}$#", "/{$path}", $matches)) {
                foreach ($matches as $k => $v) {
                    if (is_string($k)) {
                        $this->routeParams[$k] = $v;
                    }
                }
                return $route;
            }
        }
        return false;
    }
}