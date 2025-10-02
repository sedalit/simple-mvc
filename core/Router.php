<?php

namespace PHPFramework;

use PHPFramework\Interfaces\MiddlewareInterface;
use PHPFramework\Routing\Route;
use PHPFramework\Routing\RouteGroup;

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
        
        $callback = $route->getCallback();
        $middlewares = $route->getMiddlewares();

        $next = function() use ($callback) {
            if (is_array($callback)) {
                $callback[0] = new $callback[0];
            }

            return call_user_func($callback, $this->request, $this->response);
        };

        foreach (array_reverse($middlewares) as $middleware) {
            /**
             * @var MiddlewareInterface $middleware
             */
            $next = function() use ($middleware, $next) {
                return (new $middleware)->handle($this->request, $this->response, $next);
            };
        }

        return $next();
    }

    public function get(string $path, object|array $callback, array $middlewares = []) : Route
    {
        return $this->addRoute(self::GET, $path, $callback, $middlewares);
    }

    public function post(string $path, object|array $callback, array $middlewares = []) : Route
    {
        return $this->addRoute(self::POST, $path, $callback, $middlewares);
    }

    public function put(string $path, object|array $callback, array $middlewares = []) : Route
    {
        return $this->addRoute(self::PUT, $path, $callback, $middlewares);

    }

    public function delete(string $path, object|array $callback, array $middlewares = []) : Route
    {
        return $this->addRoute(self::DELETE, $path, $callback, $middlewares);
    }

    public function routeParams() : array
    {
        return $this->routeParams;
    }

    public function routeParam(string $key) : mixed
    {
        return $this->routeParams[$key] ?? null;
    }

    protected function addRoute(string $method, string $path, object|array $callback, array $middlewares = []) : Route
    {
        $path = trim($path, '/');
        $route = new Route($path, $method, $callback, $middlewares);
        $this->routes[] = $route;

        return $route;
    }

    protected function matchRoute(string $method, string $path) : mixed
    {
        foreach ($this->routes as $route) {
            /** @var Route $route */
            $matchPath = preg_match("#^{$route->getPath()}$#", $path, $matches);
            $matchMethod = strtoupper($method) === strtoupper($route->getMethod());

            if ($matchPath && $matchMethod) {
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

    public function group(string $groupName, array $routes) : RouteGroup
    {
        $groupName = trim($groupName, '/');
        $group = new RouteGroup($groupName);
        foreach ($routes as $route) {
            $route->group = $groupName;
            $group->addRoute($route);
        }

        return $group;
    }
}