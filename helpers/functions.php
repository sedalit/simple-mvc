<?php

use PHPFramework\Application;
use PHPFramework\Request;
use PHPFramework\Response;
use PHPFramework\Validation\Validator;
use PHPFramework\View;

if (!function_exists('app')) {
    function app() : Application
    {
        return Application::$instance;
    }
}

if (!function_exists('view')) {
    function view(string $view = '', array $data = [], string $layout = '') : string|View
    {
        if ($view) {
            return app()->view()->render($view, $data, $layout);
        } 

        return app()->view();
    }
}

if (!function_exists('response')) {
    function response() : Response
    {
        return app()->response();
    }
}

if (!function_exists('request')) {
    function request() : Request
    {
        return app()->request();
    }
}

if (!function_exists('validator')) {
    function validator() : Validator
    {
        return app()->validator();
    }
}

if (!function_exists('validate')) {
    function validate(array $data, array $rules) : bool
    {
        return app()->validator()->validate($data, $rules);
    }
}

if (!function_exists('baseUrl')) {
    function baseUrl($path = '') : string
    {
        return PATH . $path;
    }
}