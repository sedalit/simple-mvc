<?php

namespace PHPFramework\Middlewares;

use PHPFramework\Interfaces\MiddlewareInterface;

class AuthMiddleware implements MiddlewareInterface {
    public function handle(\PHPFramework\Request $request, \PHPFramework\Response $response, callable $next): mixed
    {
        $redirect = '/';

        if (defined('LOGIN')) {
            $redirect = LOGIN;
        }

        if (!checkAuth()) {
            redirect($redirect);
            return false;
        }

        return $next();
    }
}