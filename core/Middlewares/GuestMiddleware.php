<?php

namespace PHPFramework\Middlewares;

use PHPFramework\Interfaces\MiddlewareInterface;

class GuestMiddleware implements MiddlewareInterface {
    public function handle(\PHPFramework\Request $request, \PHPFramework\Response $response, callable $next): mixed
    {
        if (checkAuth()) {
            redirect('/');
            return false;
        }
        
        return $next();
    }
}