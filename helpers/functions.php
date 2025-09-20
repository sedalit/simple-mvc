<?php

use PHPFramework\Application;
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
