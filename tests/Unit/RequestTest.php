<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPFramework\Request;

class RequestTest extends TestCase {
    protected function tearDown(): void
    {
        $this->setGetData([]);
        $this->setPostData([]);
        $this->setServerData([]);
    }

    public function testGetPath(): void
    {
        $request = new Request('/test/path');
        $this->assertEquals('test/path', $request->getPath());
    }

    public function testGetPathWithQueryString(): void
    {
        $request = new Request('/test/path?param=value');
        $this->assertEquals('test/path', $request->getPath());
    }

    public function testGetPathWithMultipleQueryParams(): void
    {
        $request = new Request('/test/path?param1=value1&param2=value2');
        $this->assertEquals('test/path', $request->getPath());
    }

    public function testGetMethod(): void
    {
        $this->setServerData(['REQUEST_METHOD' => 'GET']);
        $request = new Request('/test');
        $this->assertEquals('GET', $request->getMethod());
    }

    public function testGetMethodFromHiddenField(): void
    {
        $this->setServerData(['REQUEST_METHOD' => 'POST']);
        $this->setPostData(['_method' => 'PUT']);
        $request = new Request('/test');
        $this->assertEquals('PUT', $request->getMethod());
    }

    public function testGetSpecificGetData(): void
    {
        $this->setGetData(['name' => 'John', 'age' => '30']);
        $request = new Request('/test');
        
        $this->assertEquals('John', $request->get('name'));
        $this->assertEquals('30', $request->get('age'));
        $this->assertEquals('default', $request->get('non-existent', 'default'));
    }

    public function testGetAllGetData(): void
    {
        $this->setGetData(['name' => 'John', 'age' => '30']);
        $request = new Request('/test');
        
        $data = $request->get();
        $this->assertEquals(['name' => 'John', 'age' => '30'], $data);
    }

    public function testGetPostData(): void
    {
        $this->setPostData(['name' => 'John', 'email' => 'john@example.com']);
        $request = new Request('/test');
        
        $this->assertEquals('John', $request->post('name'));
        $this->assertEquals('john@example.com', $request->post('email'));
        $this->assertEquals('default', $request->post('non-existent', 'default'));
    }

    public function testGetAllPostData(): void
    {
        $this->setPostData(['name' => 'John', 'email' => 'john@example.com']);
        $request = new Request('/test');
        
        $data = $request->post();
        $this->assertEquals(['name' => 'John', 'email' => 'john@example.com'], $data);
    }

    public function testCheckIfGet(): void
    {
        $this->setServerData(['REQUEST_METHOD' => 'GET']);
        $request = new Request('/test');
        
        $this->assertTrue($request->isGet());
        $this->assertFalse($request->isPost());
    }

    public function testCheckIfPost(): void
    {
        $this->setServerData(['REQUEST_METHOD' => 'POST']);
        $request = new Request('/test');
        
        $this->assertTrue($request->isPost());
        $this->assertFalse($request->isGet());
    }

    public function testGetData(): void
    {
        $this->setServerData(['REQUEST_METHOD' => 'GET']);
        $this->setGetData(['name' => ' John ', 'age' => ' 30 ']);
        $request = new Request('/test');
        
        $data = $request->getData();
        
        $this->assertEquals(['name' => 'John', 'age' => '30'], $data);
    }

    public function testGetDataFromPost(): void
    {
        $this->setServerData(['REQUEST_METHOD' => 'POST']);
        $this->setPostData(['name' => ' John ', 'email' => ' john@example.com ']);
        $request = new Request('/test');
        
        $data = $request->getData();
        $this->assertEquals(['name' => 'John', 'email' => 'john@example.com'], $data);
    }

    public function testGetFile(): void
    {
        $_FILES['upload'] = [
            'name' => 'test.jpg',
            'type' => 'image/jpeg',
            'size' => 1024,
            'tmp_name' => '/tmp/test',
            'error' => 0
        ];
        
        $request = new Request('/test');
        $file = $request->file('upload');
        
        $this->assertEquals('test.jpg', $file['name']);
        $this->assertEquals('image/jpeg', $file['type']);
    }

    public function testGetFileWithDefault(): void
    {
        $request = new Request('/test');
        $file = $request->file('non-existent', 'default');
        
        $this->assertEquals('default', $file);
    }

    public function testGetRequestUrl(): void
    {
        $this->setServerData(['REQUEST_URI' => '/test/path?param=value']);
        $request = new Request('/test');
        
        $this->assertEquals('/test/path?param=value', Request::requestUrl());
    }

    public function testGetHeaders(): void
    {
        $this->setServerData([
            'HTTP_USER_AGENT' => 'Mozilla/5.0',
            'HTTP_ACCEPT' => 'text/html',
            'CONTENT_TYPE' => 'application/json'
        ]);
        
        $request = new Request('/test');
        $headers = $request->headers();
        
        $this->assertArrayHasKey('User-Agent', $headers);
        $this->assertArrayHasKey('Accept', $headers);
        $this->assertArrayHasKey('Content-Type', $headers);
        $this->assertEquals('Mozilla/5.0', $headers['User-Agent']);
    }

    public function testGetSpecificHeader(): void
    {
        $this->setServerData(['HTTP_USER_AGENT' => 'Mozilla/5.0']);
        
        $request = new Request('/test');
        $userAgent = $request->header('User-Agent');
        
        $this->assertEquals('Mozilla/5.0', $userAgent);
    }

    public function testGetHeaderCaseInsensitive(): void
    {
        $this->setServerData(['HTTP_USER_AGENT' => 'Mozilla/5.0']);
        
        $request = new Request('/test');
        $userAgent = $request->header('user-agent');
        
        $this->assertEquals('Mozilla/5.0', $userAgent);
    }

    public function testReturnsNullForNonExistentHeader(): void
    {
        $request = new Request('/test');
        $header = $request->header('Non-Existent');
        
        $this->assertNull($header);
    }

    public function testTrimsUri(): void
    {
        $request = new Request('  /test/path  ');
        $this->assertEquals('test/path', $request->getPath());
    }

    public function testDecodesUri(): void
    {
        $request = new Request('/test%20path');
        $this->assertEquals('test path', $request->getPath());
    }

    public function testHandlesEmptyUri(): void
    {
        $request = new Request('');
        $this->assertEquals('', $request->getPath());
    }

    public function testHandlesRootUri(): void
    {
        $request = new Request('/');
        $this->assertEquals('', $request->getPath());
    }

    private function setServerData(array $data) : void
    {
        $_SERVER = $data;
    }

    private function setPostData(array $data) : void
    {
        $_POST = $data;
    }

    private function setGetData(array $data) : void
    {
        $_GET = $data;
    }
}