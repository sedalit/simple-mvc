<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use PHPFramework\Application;
use PHPFramework\ServiceContainer;
use PHPFramework\Services\CoreService;
use PHPFramework\Services\Mail\MailService;

class ApplicationTest extends TestCase
{
    protected Application $app;

    public function __construct()
    {
        parent::__construct();
        $_SERVER = ['REQUEST_URI' => '/test', 'REQUEST_METHOD' => 'GET'];
        $this->app = new Application('/test', [
            CoreService::class,
            MailService::class
        ]);
    }

    public function testCanGetRouter(): void
    {
        $router = Application::router();
        $this->assertInstanceOf(\PHPFramework\Router::class, $router);
    }

    public function testCanGetRequest(): void
    {
        $request = Application::request();
        $this->assertInstanceOf(\PHPFramework\Request::class, $request);
    }

    public function testCanGetResponse(): void
    {
        $response = Application::response();
        $this->assertInstanceOf(\PHPFramework\Response::class, $response);
    }

    public function testCanGetView(): void
    {
        $view = Application::view();
        $this->assertInstanceOf(\PHPFramework\View::class, $view);
    }

    public function testCanGetValidator(): void
    {
        $validator = Application::validator();
        $this->assertInstanceOf(\PHPFramework\Validation\Validator::class, $validator);
    }

    public function testCanGetCache(): void
    {
        $cache = Application::cache();
        $this->assertInstanceOf(\PHPFramework\Cache::class, $cache);
    }

    public function testApplicationIsSingleton(): void
    {
        $app1 = Application::$instance;
        $app2 = Application::$instance;
        
        $this->assertSame($app1, $app2);
    }

    public function testCanGetServiceFromContainer(): void
    {
        $service = $this->app->get('request');
        $this->assertInstanceOf(\PHPFramework\Request::class, $service);
    }

    public function testThrowsExceptionForNonExistentService(): void
    {
        $this->expectException(\Exception::class);
        $this->app->get('NonExistentService');
    }

    public function testCanRunApplication(): void
    {
        $this->app->router()->get('/test', function() {
            return 'Test Response';
        });
        
        ob_start();
        $this->app->run();
        $output = ob_get_clean();
        
        $this->assertEquals('Test Response', $output);
    }

    public function testApplicationRegistersServiceProviders(): void
    {
        $this->assertInstanceOf(\PHPFramework\Router::class, $this->app->get('router'));
        $this->assertInstanceOf(\PHPFramework\Request::class, $this->app->get('request'));
        $this->assertInstanceOf(\PHPFramework\Response::class, $this->app->get('response'));
    }

    public function testCanGetContainerService(): void
    {
        $container = $this->app->get('container');
        $this->assertInstanceOf(\PHPFramework\Container::class, $container);

        $container = Application::container();
        $this->assertInstanceOf(\PHPFramework\Container::class, $container);
    }
}