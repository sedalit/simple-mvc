<?php

namespace PHPFramework\Middlewares;

use PHPFramework\Interfaces\MiddlewareInterface;
use PHPFramework\Security\CsrfToken;

class CsrfMiddleware implements MiddlewareInterface {
    public function handle(\PHPFramework\Request $request, \PHPFramework\Response $response, callable $next): mixed
    {
        $method = $request->getMethod();
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $token = $request->post(CsrfToken::INPUT_FIELD) ?? $request->header(CsrfToken::HEADER_NAME);
            if (!CsrfToken::validate($token)) {
                abort('CSRF token mismatch', 403);
            }
        }

        return $next();
    }
}