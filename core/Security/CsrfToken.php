<?php

namespace PHPFramework\Security;

class CsrfToken {
    public const INPUT_FIELD = '_csrf';
    public const HEADER_NAME = 'X-CSRF-TOKEN';
    protected const SESSION_KEY = '_csrf_token';

    protected static function generate() : string
    {
        if (!$token = session()->get(self::SESSION_KEY)) {
            $token = bin2hex(random_bytes(32));
            session()->set(self::SESSION_KEY, $token);
        }

        return $token;
    }

    public static function get() : string
    {
        return session()->get(self::SESSION_KEY) ?? self::generate();
    }

    public static function validate(?string $token) : bool
    {
        if (!$token) return false;
        
        return hash_equals(self::get(), $token);
    }
}