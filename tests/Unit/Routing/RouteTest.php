<?php

namespace Tests\Unit;

use PHPFramework\Routing\Route;
use PHPFramework\Routing\RouteGroup;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
    public function testCanCreateRoute(): void
    {
        $callback = function() { return 'test'; };
        $route = new Route('/test', 'GET', $callback);
        
        $this->assertEquals('/test', $route->getPath());
        $this->assertEquals('GET', $route->getMethod());
        $this->assertEquals($callback, $route->getCallback());
        $this->assertEquals([], $route->getMiddlewares());
    }

    public function testCanCreateRouteWithMiddlewares(): void
    {
        $callback = function() { return 'test'; };
        $middlewares = ['AuthMiddleware', 'CsrfMiddleware'];
        $route = new Route('/test', 'GET', $callback, $middlewares);
        
        $this->assertEquals($middlewares, $route->getMiddlewares());
    }

    public function testCanCreateRouteWithGroup(): void
    {
        $callback = function() { return 'test'; };
        $route = new Route('/test', 'GET', $callback, [], 'admin');
        
        $this->assertEquals('admin/test', $route->getPath());
        $this->assertEquals('admin', $route->group);
    }

    public function testCanAddMiddleware(): void
    {
        $callback = function() { return 'test'; };
        $route = new Route('/test', 'GET', $callback);
        
        $route->addMiddleware('AuthMiddleware');
        $this->assertEquals(['AuthMiddleware'], $route->getMiddlewares());
    }

    public function testCanAddMultipleMiddlewares(): void
    {
        $callback = function() { return 'test'; };
        $route = new Route('/test', 'GET', $callback);
        
        $route->addMiddleware(['AuthMiddleware', 'CsrfMiddleware']);
        $this->assertEquals(['AuthMiddleware', 'CsrfMiddleware'], $route->getMiddlewares());
    }

    public function testCanAddMiddlewareToExistingRoute(): void
    {
        $callback = function() { return 'test'; };
        $route = new Route('/test', 'GET', $callback, ['AuthMiddleware']);
        
        $route->addMiddleware('CsrfMiddleware');
        $this->assertEquals(['AuthMiddleware', 'CsrfMiddleware'], $route->getMiddlewares());
    }

    public function testCanAddMultipleMiddlewaresToExistingRoute(): void
    {
        $callback = function() { return 'test'; };
        $route = new Route('/test', 'GET', $callback, ['AuthMiddleware']);
        
        $route->addMiddleware(['CsrfMiddleware', 'GuestMiddleware']);
        $this->assertEquals(['AuthMiddleware', 'CsrfMiddleware', 'GuestMiddleware'], $route->getMiddlewares());
    }

    public function testCanCreateRouteWithArrayCallback(): void
    {
        $callback = ['TestController', 'index'];
        $route = new Route('/test', 'GET', $callback);
        
        $this->assertEquals($callback, $route->getCallback());
    }

    public function testCanCreateRouteWithObjectCallback(): void
    {
        $callback = new \stdClass();
        $route = new Route('/test', 'GET', $callback);
        
        $this->assertEquals($callback, $route->getCallback());
    }

    public function testCanCreateRouteWithDifferentMethods(): void
    {
        $callback = function() { return 'test'; };
        
        $getRoute = new Route('/test', 'GET', $callback);
        $postRoute = new Route('/test', 'POST', $callback);
        $putRoute = new Route('/test', 'PUT', $callback);
        $deleteRoute = new Route('/test', 'DELETE', $callback);
        
        $this->assertEquals('GET', $getRoute->getMethod());
        $this->assertEquals('POST', $postRoute->getMethod());
        $this->assertEquals('PUT', $putRoute->getMethod());
        $this->assertEquals('DELETE', $deleteRoute->getMethod());
    }

    public function testCanCreateRouteWithEmptyPath(): void
    {
        $callback = function() { return 'test'; };
        $route = new Route('', 'GET', $callback);
        
        $this->assertEquals('', $route->getPath());
    }

    public function testCanCreateRouteWithSlashPath(): void
    {
        $callback = function() { return 'test'; };
        $route = new Route('/', 'GET', $callback);
        
        $this->assertEquals('/', $route->getPath());
    }

    public function testCanCreateRouteWithComplexPath(): void
    {
        $callback = function() { return 'test'; };
        $route = new Route('/api/v1/users', 'GET', $callback);
        
        $this->assertEquals('/api/v1/users', $route->getPath());
    }

    public function testCanCreateRouteWithGroupAndComplexPath(): void
    {
        $callback = function() { return 'test'; };
        $route = new Route('/users', 'GET', $callback, [], 'api/v1');
        
        $this->assertEquals('api/v1/users', $route->getPath());
    }

    public function testCanCreateRouteWithEmptyGroup(): void
    {
        $callback = function() { return 'test'; };
        $route = new Route('/test', 'GET', $callback, [], '');
        
        $this->assertEquals('/test', $route->getPath());
    }

    public function testCanCreateRouteWithSpecialCharacters(): void
    {
        $callback = function() { return 'test'; };
        $route = new Route('/test-path', 'GET', $callback);
        
        $this->assertEquals('/test-path', $route->getPath());
    }

    public function testCanCreateRouteWithNumericPath(): void
    {
        $callback = function() { return 'test'; };
        $route = new Route('/123', 'GET', $callback);
        
        $this->assertEquals('/123', $route->getPath());
    }

    public function testCanCreateRouteWithMixedPath(): void
    {
        $callback = function() { return 'test'; };
        $route = new Route('/test123-path', 'GET', $callback);
        
        $this->assertEquals('/test123-path', $route->getPath());
    }
}