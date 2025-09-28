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
        $route = $this->matchRoute($method, $path);

        if (!$route) {
            abort('Page not found');
        } 
        
        $callback = $route['callback'];
        $middlewares = $route['middlewares'];

        $next = function() use ($callback) {
            if (is_array($callback)) {
                $callback[0] = new $callback[0];
            }

            return call_user_func($callback, $this->request, $this->response);
        };

        foreach (array_reverse($middlewares) as $middleware) {
            $next = function() use ($middleware, $next) {
                return (new $middleware)->handle($this->request, $this->response, $next);
            };
        }

        return $next();
    }

    public function get(string $path, object|array $callback, array $middlewares = []) : void
    {
        $this->addRoute(self::GET, $path, $callback, $middlewares);
    }

    public function post(string $path, object|array $callback, array $middlewares = []) : void
    {
        $this->addRoute(self::POST, $path, $callback, $middlewares);
    }

    public function put(string $path, object|array $callback, array $middlewares = []) : void
    {
        $this->addRoute(self::PUT, $path, $callback, $middlewares);

    }

    public function delete(string $path, object|array $callback, array $middlewares = []) : void
    {
        $this->addRoute(self::DELETE, $path, $callback, $middlewares);
    }

    public function routeParams() : array
    {
        return $this->routeParams;
    }

    public function routeParam(string $key) : mixed
    {
        return $this->routeParams[$key] ?? null;
    }

    protected function addRoute(string $method, string $path, object|array $callback, array $middlewares = []) : void
    {
        $path = trim($path, '/');
        $this->routes[$method]["/{$path}"] = [
            'callback' => $callback,
            'middlewares' => $middlewares
        ];
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