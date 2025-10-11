<?php

namespace Tests\Unit;

use PHPFramework\Router;
use PHPUnit\Framework\TestCase;
use PHPFramework\Request;
use PHPFramework\Response;
use PHPFramework\Routing\Route;
use PHPFramework\Routing\RouteGroup;
use PHPFramework\Middlewares\CsrfMiddleware;

class RouterTest extends TestCase {
    protected Router $router;
    protected Request $request;
    protected Response $response;

    protected function setUp() : void
    {
        parent::setUp();
        $this->request = new Request('/test');
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
    }

    public function testAddGetRoute() : void
    {
        $callback = function() { return 'test'; };
        $route = $this->router->get('/test', $callback);

        $this->assertInstanceOf(Route::class, $route);
        $this->assertEquals('GET', $route->getMethod());
        $this->assertEquals('test', $route->getPath());
        $this->assertEquals($callback, $route->getCallback());
    }

    public function testAddPostRoute() : void
    {
        $callback = function() { return 'test'; };
        $route = $this->router->post('/test', $callback);

        $this->assertInstanceOf(Route::class, $route);
        $this->assertEquals('POST', $route->getMethod());
        $this->assertEquals('test', $route->getPath());
        $this->assertContains(CsrfMiddleware::class, $route->getMiddlewares());
    }

    public function testAddPutRoute() : void
    {
        $callback = function() { return 'test'; };
        $route = $this->router->put('/test', $callback);

        $this->assertInstanceOf(Route::class, $route);
        $this->assertEquals('PUT', $route->getMethod());
        $this->assertContains(CsrfMiddleware::class, $route->getMiddlewares());
    }

    public function testAddDeleteRoute() : void
    {
        $callback = function() { return 'test'; };
        $route = $this->router->delete('/test', $callback);

        $this->assertInstanceOf(Route::class, $route);
        $this->assertEquals('DELETE', $route->getMethod());
        $this->assertContains(CsrfMiddleware::class, $route->getMiddlewares());
    }

    public function testAddRouteWithMiddleware() : void
    {
        $callback = function() { return 'test'; };
        $middleware = ['TestMiddleware'];
        
        $route = $this->router->get('/test', $callback, $middleware);

        $this->assertContains('TestMiddleware', $route->getMiddlewares());
    }

    public function testCreateRouteGroup() : void
    {
        $routes = [
            $this->router->get('/create', function() { return 'create'; }),
            $this->router->post('/store', function() { return 'store'; })
        ];

        $group = $this->router->group('/posts', $routes);

        $this->assertInstanceOf(RouteGroup::class, $group);
        $this->assertEquals('posts', $group->getName());
    }

    public function testMatchRoute() : void
    {
        $callback = function() { return 'matched'; };
        $this->router->get('/test', $callback);

        $this->setServerData([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/test'
        ]);

        $result = $this->router->dispatch();
        $this->assertEquals('matched', $result);
    }

    public function testMatchRouteWithParameters() : void
    {
        $this->request = new Request('/test/123');
        $this->router = new Router($this->request, $this->response);
        
        $callback = function() { return 'matched'; };
        $this->router->get('/test/?(?P<id>\d+)', $callback);

        $this->setServerData([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/test/123'
        ]);

        $result = $this->router->dispatch();
        $this->assertEquals('matched', $result);
        $this->assertEquals('123', $this->router->routeParam('id'));
    }

    public function testReturns404ForNonExistentRoute() : void
    {
        $this->setServerData([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/non-existent'
        ]);

        $this->expectException(\Exception::class);
        $this->router->dispatch();
    }

    public function testGetAllRoutes() : void
    {
        $this->router->get('/test1', function() { return 'test1'; });
        $this->router->post('/test2', function() { return 'test2'; });

        $routes = $this->router->getRoutes();
        $this->assertCount(2, $routes);
    }

    public function testRouteParamsAreEmptyInitially() : void
    {
        $params = $this->router->routeParams();
        $this->assertEmpty($params);
    }

    private function setServerData(array $data) : void
    {
        $_SERVER = $data;
    }
}