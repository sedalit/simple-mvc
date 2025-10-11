<?php

namespace PHPFramework\Routing;

use PHPFramework\Interfaces\MiddlewareInterface;

class Route {
    protected string $path;
    protected object|array $callback;
    protected string $method;
    protected array $middlewares;
    public string $group = '';

    public function __construct(string $path, string $method, object|array $callback, array $middlewares = [], string $group = '')
    {
        $this->path = $path;
        $this->method = $method;
        $this->callback = $callback;
        $this->middlewares = $middlewares;
        $this->group = $group;
    }

    public function getPath() : string
    {
        if (str_contains($this->path, '/')) {
            return $this->group ? "{$this->group}{$this->path}" : $this->path;
        }
        
        return $this->group ? "{$this->group}/{$this->path}" : $this->path;
    }

    public function getCallback() : object|array
    {
        return $this->callback;
    }

    public function getMethod() : string
    {
        return $this->method;
    }

    public function getMiddlewares() : array
    {
        return $this->middlewares;
    }

    public function addMiddleware(string|array $middleware) : self
    {
        if (gettype($middleware) == 'array') {
            $this->middlewares = array_merge($this->middlewares, $middleware);
        } else {
            $this->middlewares[] = $middleware;
        }

        return $this;
    }
}