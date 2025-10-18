<?php

namespace Tests\Unit;

use PHPFramework\Container;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase {
    protected Container $container;
    protected function setUp() : void
    {
        parent::setUp();
        $this->container = new Container();
    }
    public function testSetAndGet() : void
    {
        $this->container->set('key1', 'value1');
        $this->assertEquals('value1', $this->container->get('key1'));
    }
    public function testGetWithDefault() : void
    {
        $this->assertNull($this->container->get('non_existing_key'));
        $this->assertEquals('foo', $this->container->get('non_existing_key', 'foo'));
    }
}