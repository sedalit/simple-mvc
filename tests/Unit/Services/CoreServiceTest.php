<?php

namespace Tests\Services;

use PHPFramework\Request;
use PHPFramework\Router;
use PHPUnit\Framework\TestCase;
use PHPFramework\Services\CoreService;
use PHPFramework\ServiceContainer;

class CoreServiceTest extends TestCase {
    public function testRegister() : void
    {
        $c = new ServiceContainer();
        $_SERVER = ['REQUEST_URI' => '/'];

        $coreService = new CoreService();
        $coreService->register($c);

        $this->assertInstanceOf(Request::class, $c->get('request'));
        $this->assertInstanceOf(Router::class, $c->get('router'));
    }
}