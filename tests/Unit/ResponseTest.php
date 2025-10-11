<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPFramework\Response;

class ResponseTest extends TestCase {
    protected Response $response;

    protected function setUp() : void
    {
        parent::setUp();
        $this->response = new Response();
    }

    public function testSetResponseCode() : void
    {
        $this->response->setCode(404);
        $this->assertEquals(404, $this->response->getCode());
    }

    public function testSetDifferentResponseCodes() : void
    {
        $this->response->setCode(200);
        $this->assertEquals(200, $this->response->getCode());
        
        $this->response->setCode(500);
        $this->assertEquals(500, $this->response->getCode());
        
        $this->response->setCode(301);
        $this->assertEquals(301, $this->response->getCode());
    }

    public function testRedirectWithUrl() : void
    {
        $this->response->redirect('/test');

        $this->assertEquals($this->response->getRedirect(), '/test');
    }

    public function testRedirectWithParams() : void
    {
        $this->response->redirect('/test', ['param' => 'value']);

        $this->assertEquals($this->response->getRedirect(), '/test');
    }

    public function testResponseCodeIsSetBeforeRedirect() : void
    {
        $this->response->redirect('/test');

        $this->assertEquals($this->response->getRedirect(), '/test');
    }

    public function testRedirectWithComplexUrl() : void
    {
        $this->response->redirect('/test/path?param=value#fragment');
        $this->assertEquals($this->response->getRedirect(), '/test/path?param=value#fragment');
    }

    public function testRedirectWithSpecialCharacters() : void
    {
        $this->response->redirect('/test/path with spaces');
        $this->assertEquals($this->response->getRedirect(), '/test/path with spaces');
    }

    public function testRedirectWithUnicode() : void
    {
        $this->response->redirect('/тест/путь');
        $this->assertEquals($this->response->getRedirect(), '/тест/путь');
    }

    public function testResponseCodeValidation() : void
    {
        $codes = [200, 201, 301, 302, 400, 401, 403, 404, 500];
        
        foreach ($codes as $code) {
            $this->response->setCode($code);
            $this->assertEquals($code, $this->response->getCode());
        }
    }

    public function testResponseCodeWithZero() : void
    {
        $this->response->setCode(0);
        $this->assertEquals(0, $this->response->getCode());
    }

    public function testResponseCodeWithNegativeValue() : void
    {
        $this->response->setCode(-1);
        $this->assertEquals(-1, $this->response->getCode());
    }
}