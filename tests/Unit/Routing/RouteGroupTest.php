<?php

namespace Tests\Unit;

use PHPFramework\Routing\Route;
use PHPFramework\Routing\RouteGroup;
use PHPUnit\Framework\TestCase;

class RouteGroupTest extends TestCase
{
    public function testCanCreateRouteGroup(): void
    {
        $group = new RouteGroup('admin');
        
        $this->assertEquals('admin', $group->getName());
        $this->assertEquals([], $group->getRoutes());
    }

    public function testCanCreateRouteGroupWithRoutes(): void
    {
        $routes = [
            new Route('/users', 'GET', function() { return 'users'; }),
            new Route('/posts', 'GET', function() { return 'posts'; })
        ];
        
        $group = new RouteGroup('admin', $routes);
        
        $this->assertEquals('admin', $group->getName());
        $this->assertEquals($routes, $group->getRoutes());
    }

    public function testCanAddRouteToGroup(): void
    {
        $group = new RouteGroup('admin');
        $route = new Route('/users', 'GET', function() { return 'users'; });
        
        $group->addRoute($route);
        
        $this->assertEquals([$route], $group->getRoutes());
    }

    public function testCanAddMultipleRoutesToGroup(): void
    {
        $group = new RouteGroup('admin');
        $route1 = new Route('/users', 'GET', function() { return 'users'; });
        $route2 = new Route('/posts', 'GET', function() { return 'posts'; });
        
        $group->addRoute($route1);
        $group->addRoute($route2);
        
        $this->assertEquals([$route1, $route2], $group->getRoutes());
    }

    public function testCanAddMiddlewareToGroup(): void
    {
        $group = new RouteGroup('admin');
        $route1 = new Route('/users', 'GET', function() { return 'users'; });
        $route2 = new Route('/posts', 'GET', function() { return 'posts'; });
        
        $group->addRoute($route1);
        $group->addRoute($route2);
        
        $group->middleware('AuthMiddleware');
        
        $this->assertEquals(['AuthMiddleware'], $route1->getMiddlewares());
        $this->assertEquals(['AuthMiddleware'], $route2->getMiddlewares());
    }

    public function testCanAddMultipleMiddlewaresToGroup(): void
    {
        $group = new RouteGroup('admin');
        $route1 = new Route('/users', 'GET', function() { return 'users'; });
        $route2 = new Route('/posts', 'GET', function() { return 'posts'; });
        
        $group->addRoute($route1);
        $group->addRoute($route2);
        
        $group->middleware(['AuthMiddleware', 'CsrfMiddleware']);
        
        $this->assertEquals(['AuthMiddleware', 'CsrfMiddleware'], $route1->getMiddlewares());
        $this->assertEquals(['AuthMiddleware', 'CsrfMiddleware'], $route2->getMiddlewares());
    }

    public function testCanAddMiddlewareToGroupWithExistingMiddlewares(): void
    {
        $group = new RouteGroup('admin');
        $route1 = new Route('/users', 'GET', function() { return 'users'; }, ['GuestMiddleware']);
        $route2 = new Route('/posts', 'GET', function() { return 'posts'; }, ['GuestMiddleware']);
        
        $group->addRoute($route1);
        $group->addRoute($route2);
        
        $group->middleware('AuthMiddleware');
        
        $this->assertEquals(['GuestMiddleware', 'AuthMiddleware'], $route1->getMiddlewares());
        $this->assertEquals(['GuestMiddleware', 'AuthMiddleware'], $route2->getMiddlewares());
    }

    public function testCanAddMiddlewareToEmptyGroup(): void
    {
        $group = new RouteGroup('admin');
        
        $group->middleware('AuthMiddleware');
        
        $this->assertEquals([], $group->getRoutes());
    }

    public function testCanCreateRouteGroupWithEmptyName(): void
    {
        $group = new RouteGroup('');
        
        $this->assertEquals('', $group->getName());
    }

    public function testCanCreateRouteGroupWithSlashName(): void
    {
        $group = new RouteGroup('/');
        
        $this->assertEquals('/', $group->getName());
    }

    public function testCanCreateRouteGroupWithComplexName(): void
    {
        $group = new RouteGroup('api/v1');
        
        $this->assertEquals('api/v1', $group->getName());
    }

    public function testCanCreateRouteGroupWithSpecialCharacters(): void
    {
        $group = new RouteGroup('admin-panel');
        
        $this->assertEquals('admin-panel', $group->getName());
    }

    public function testCanCreateRouteGroupWithUnicodeName(): void
    {
        $group = new RouteGroup('админ');
        
        $this->assertEquals('админ', $group->getName());
    }

    public function testCanCreateRouteGroupWithNumericName(): void
    {
        $group = new RouteGroup('123');
        
        $this->assertEquals('123', $group->getName());
    }

    public function testCanCreateRouteGroupWithMixedName(): void
    {
        $group = new RouteGroup('admin123-panel');
        
        $this->assertEquals('admin123-panel', $group->getName());
    }

    public function testCanCreateRouteGroupWithLongName(): void
    {
        $group = new RouteGroup('very-long-group-name-with-many-characters');
        
        $this->assertEquals('very-long-group-name-with-many-characters', $group->getName());
    }

    public function testCanCreateRouteGroupWithSpacesInName(): void
    {
        $group = new RouteGroup('admin panel');
        
        $this->assertEquals('admin panel', $group->getName());
    }

    public function testCanCreateRouteGroupWithSpecialCharactersInName(): void
    {
        $group = new RouteGroup('admin@panel#123');
        
        $this->assertEquals('admin@panel#123', $group->getName());
    }

    public function testCanCreateRouteGroupWithUnicodeSpecialCharacters(): void
    {
        $group = new RouteGroup('админ-панель');
        
        $this->assertEquals('админ-панель', $group->getName());
    }

    public function testCanCreateRouteGroupWithEmptyRoutes(): void
    {
        $group = new RouteGroup('admin', []);
        
        $this->assertEquals([], $group->getRoutes());
    }
}