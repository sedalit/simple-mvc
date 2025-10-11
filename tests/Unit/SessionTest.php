<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPFramework\Session;

class SessionTest extends TestCase {
    protected const KEY = 'key';
    protected Session $session;
    public function __construct() {
        parent::__construct();
        $this->session = new Session();
    }

    public function tearDown() : void
    {
        parent::tearDown();
        $this->session->forget(self::KEY);
    }

    public function testGetDefault() : void
    {
        $result = $this->session->get(self::KEY, 'default');

        $this->assertEquals('default', $result);
    }

    public function testSetAndGet() : void
    {
        $this->session->set(self::KEY, 'value');
        $result = $this->session->get(self::KEY);

        $this->assertEquals('value', $result);
    }

    public function testSetAndGetArray() : void
    {
        $array = ['foo' => 'bar'];
        $this->session->set('key', $array);
        $result = $this->session->get('key');

        $this->assertEquals($array, $result);
    }

    public function testSetAndGetObject() : void
    {
        $user = new \stdClass();
        $user->name = 'Name';
        $user->age = 30;

        $this->session->set('key', $user);
        $result = $this->session->get('key');

        $this->assertEquals($user, $result);
    }

    public function testForget() : void
    {
        $this->session->set(self::KEY, 'value');
        $this->session->forget(self::KEY);

        $this->assertNull($this->session->get(self::KEY));
    }

    public function testForgetUnknown() : void
    {
        $this->session->forget(self::KEY);

        $this->assertNull($this->session->get(self::KEY));
    }

    public function testHas() : void
    {
        $user = new \stdClass();
        $user->name = 'Name';

        $this->session->set(self::KEY, $user);
        
        $this->assertTrue($this->session->has(self::KEY));
    }

    public function testSetAndGetFlash() : void
    {
        $this->session->setFlash(self::KEY, 'value');
        $result = $this->session->getFlash(self::KEY);

        $this->assertEquals('value', $result);
    }

    public function testSetAndGetEmptyFlash() : void
    {
        $this->session->setFlash(self::KEY, 'value');
        $this->session->getFlash(self::KEY);

        $result = $this->session->getFlash(self::KEY);
        $this->assertNull($result);
    }
}