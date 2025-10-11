<?php

namespace Tests\Unit;

use PHPFramework\ServiceContainer;
use PHPUnit\Framework\TestCase;
use PHPFramework\Request;
use PHPFramework\Response;

class ServiceContainerTest extends TestCase {
    protected ServiceContainer $container;
    protected function setUp() : void
    {
        parent::setUp();
        $this->container = new ServiceContainer();
    }

    public function testRegisterFactory() : void
    {
        $factory = fn() => new \stdClass();
        $this->container->setFactory('test', $factory);

        $result = $this->container->get('test');
        $this->assertInstanceOf(\stdClass::class, $result);
    }

    public function testRegisterSingleton() : void
    {
        $factory = fn() => new \stdClass();
        $this->container->setSingleton('singleton', $factory);

        $result1 = $this->container->get('singleton');
        $result2 = $this->container->get('singleton');

        $this->assertInstanceOf(\stdClass::class, $result1);
        $this->assertSame($result1, $result2);
    }

    public function testFactoryCreatesNewInstanceEachTime() : void
    {
        $factory = fn() => new \stdClass();
        $this->container->setFactory('factory', $factory);

        $result1 = $this->container->get('factory');
        $result2 = $this->container->get('factory');

        $this->assertNotSame($result1, $result2);
    }

    public function testThrowsExceptionForNonExistentService() : void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Service NonExistentService not found');
        
        $this->container->get('NonExistentService');
    }

    public function testResolveServiceByClassName() : void
    {
        $factory = fn() => new Request('/');
        $this->container->setFactory('request', $factory);

        $result = $this->container->get(Request::class);
        $this->assertInstanceOf(Request::class, $result);
    }

    public function testResolveServiceByLowercaseClassName() : void
    {
        $factory = fn() => new Response();
        $this->container->setFactory('response', $factory);

        $result = $this->container->get('response');
        $this->assertInstanceOf(Response::class, $result);
    }

    public function testSingletonMaintainsState() : void
    {
        $counter = 0;
        $factory = function() use (&$counter) {
            return ++$counter;
        };
        
        $this->container->setSingleton('counter', $factory);

        $result1 = $this->container->get('counter');
        $result2 = $this->container->get('counter');

        $this->assertEquals(1, $result1);
        $this->assertEquals(1, $result2);
    }

    public function testFactoryCreatesNewInstanceWithState() : void
    {
        $counter = 0;
        $factory = function() use (&$counter) {
            return ++$counter;
        };
        
        $this->container->setFactory('counter', $factory);

        $result1 = $this->container->get('counter');
        $result2 = $this->container->get('counter');

        $this->assertEquals(1, $result1);
        $this->assertEquals(2, $result2);
    }
}