<?php

namespace PHPFramework;

use PHPFramework\Interfaces\ServiceProviderInterface;
use PHPFramework\Validation\Validator;
use PHPFramework\Utils\Env;

class Application {
    public static Application $instance;

    protected string $uri;
    protected ServiceContainer $serviceContainer;

    public function __construct(string $uri, array $providers)
    {
        self::$instance = $this;

        Env::load();
        require_once __DIR__ . '/../../../../config/db.php';

        $this->uri = $uri;
       
        $this->serviceContainer = new ServiceContainer();
        
        foreach ($providers as $provider) {
            /** @var ServiceProviderInterface $provider */
            (new $provider())->register($this->serviceContainer);
        }
    }

    public static function router() : Router
    {
        return self::$instance->serviceContainer->get(Router::class);
    }

    public static function request() : Request
    {
        return self::$instance->serviceContainer->get(Request::class);
    }

    public static function view() : View
    {
        return self::$instance->serviceContainer->get(View::class);
    }

    public static function response() : Response
    {
        return self::$instance->serviceContainer->get(Response::class);
    }

    public static function validator() : Validator
    {
        return self::$instance->serviceContainer->get(Validator::class);
    }

    public static function database() : Database
    {
        return self::$instance->serviceContainer->get(Database::class);
    }

    public static function session() : Session
    {
        return self::$instance->serviceContainer->get(Session::class);
    }

    public static function cache() : Cache
    {
        return self::$instance->serviceContainer->get(Cache::class);
    }

    public function run() : void
    {
        echo $this->router()->dispatch();
    }

    public function get(string $id) : mixed
    {
        return $this->serviceContainer->get($id);
    }

    public static function __callStatic($name, $arguments)
    {
        if ($service = self::$instance->serviceContainer->get($name)) {
            return $service;
        }
    }

    public function getUri() : string
    {
        return $this->uri;
    }
}