<?php

namespace PHPFramework\Interfaces;

use PHPFramework\Request;
use PHPFramework\Response;

interface MiddlewareInterface {
    public function handle(Request $request, Response $response, callable $next) : mixed;
}