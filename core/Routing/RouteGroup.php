<?php 

namespace PHPFramework\Routing;

use PHPFramework\Interfaces\MiddlewareInterface;

class RouteGroup {
    protected string $name;
    protected array $routes;

    public function __construct(string $name, array $routes = [])
    {
        $this->name = $name;
        $this->routes = $routes;
    }

    public function addRoute(Route $route) : self
    {
        $this->routes[] = $route;

        return $this;
    }

    public function middleware(array|string $middleware) : self
    {
        foreach ($this->routes as $route) {
            $route->addMiddleware($middleware);
        }

        return $this;
    }

    public function getName() : string
    {
        return $this->name;
    }
}