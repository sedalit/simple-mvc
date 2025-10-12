<?php

namespace PHPFramework\Services;

use PHPFramework\Application;
use PHPFramework\Cache;
use PHPFramework\Database;
use PHPFramework\Factory;
use PHPFramework\Interfaces\ServiceProviderInterface;
use PHPFramework\Request;
use PHPFramework\Response;
use PHPFramework\Router;
use PHPFramework\ServiceContainer;
use PHPFramework\Session;
use PHPFramework\Validation\Validator;
use PHPFramework\View;

class CoreService implements ServiceProviderInterface {
    public function register(ServiceContainer $c) : void
    {
        $c->setSingleton('request',  new Factory(Request::class, [Request::requestUrl()]));
        $c->setSingleton('response', new Factory(Response::class));

        $request = $c->get('request');
        $response = $c->get('response');
        $c->setSingleton('router', new Factory(Router::class, [$request, $response]));
        $c->setSingleton('view', new Factory(View::class));
        $c->setSingleton('validator', new Factory(Validator::class));
        $c->setSingleton('session', new Factory(Session::class));
        $c->setSingleton('database', new Factory(Database::class));
        $c->setSingleton('cache', new Factory(Cache::class));
    }
}