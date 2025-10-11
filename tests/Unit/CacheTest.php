<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPFramework\Cache;

class CacheTest extends TestCase {
    protected const TEST_KEY = 'test_key';
    protected Cache $cache;

    protected function setUp() : void
    {
        parent::setUp();
        $this->cache = new Cache();

        $this->cache->forget(self::TEST_KEY);
    }

    protected function tearDown() : void
    {
        parent::tearDown();
        $this->cache->forget(self::TEST_KEY);
    }

    public function testSetAndGet() : void
    {
        $this->cache->set(self::TEST_KEY, 'test_value');
        $this->assertEquals('test_value', $this->cache->get(self::TEST_KEY));
    }
    
    public function testSetAndGetArray() : void
    {
        $this->cache->set(self::TEST_KEY, ['key' => 'test_value']);
        $this->assertEquals(['key' => 'test_value'], $this->cache->get(self::TEST_KEY));
    }

    public function testSetAndGetObject() : void
    {
        $user = new \stdClass();
        $user->name = 'name';
        $user->age = 20;

        $this->cache->set(self::TEST_KEY, $user);
        $this->assertEquals($user, $this->cache->get(self::TEST_KEY));
    }

    public function testGetWithDefault() : void
    {
        $this->assertNull($this->cache->get(self::TEST_KEY));
        $this->assertEquals('default_value', $this->cache->get(self::TEST_KEY, 'default_value'));
    }

    public function testForget() : void
    {
        $this->cache->set(self::TEST_KEY, 'test_value');
        $this->cache->forget(self::TEST_KEY);
        $this->assertNull($this->cache->get(self::TEST_KEY));
    }

    public function testExpired() : void
    {
        $this->cache->set(self::TEST_KEY, 'test_value', 1);
        $this->assertEquals('test_value', $this->cache->get(self::TEST_KEY));
        sleep(2);
        $this->assertNull($this->cache->get(self::TEST_KEY));
    }
}