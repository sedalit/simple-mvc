<?php

use PHPFramework\Application;
use PHPFramework\Cache;
use PHPFramework\Container;
use PHPFramework\Database;
use PHPFramework\Request;
use PHPFramework\Response;
use PHPFramework\Router;
use PHPFramework\Validation\Validator;
use PHPFramework\View;
use PHPFramework\Session;
use PHPFramework\Utils\Env;

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

if (!function_exists('redirect')) {
    function redirect(string $url = '', array $params = [])
    {
        return response()->redirect($url, $params)->send();
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
    function validate(array|\ArrayAccess $data, array $rules) : bool
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

if (!function_exists('h')) {
    function h(string $string) : string
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5);
    }
}

if (!function_exists('old')) {
    function old(string $fieldName) : string
    {
        return isset($_POST[$fieldName]) ? h($_POST[$fieldName]) : '';
    }
}

if (!function_exists('formErrors')) {
    function formErrors(string $fieldName, array $errors = []) : string
    {
        $output = '';

        if (isset($errors[$fieldName])) {
            $output .= '<div class="invalid-feedback d-block"><ul class="list-unstyled">';
            foreach ($errors[$fieldName] as $error) {
                $output .= "<li>{$error}</li>";
            }
            $output .= '</ul></div>';
        }

        return $output;
    }
}

if (!function_exists('abort')) {
    function abort(string $error = '', int $code = 404) : View
    {
        try {
            response()->setCode($code)->send();
            if (DEBUG || $code === 404) {
                echo view('error', ['code' => $code, 'message' => $error], false);
            }
            die;
        } catch (\Throwable $th) {
            throw new Exception($error, $code);
        }
    }
}

if (!function_exists('db')) {
    function db() : Database
    {
        return app()->database();
    }
}

if (!function_exists('session')) {
    function session() : Session
    {
        return app()->session();
    }
}

if (!function_exists('getAlerts')) {
    function getAlerts()
    {
        if (!empty($_SESSION['flash'])) {
            foreach ($_SESSION['flash'] as $key => $value) {
                view()->renderPartial('includes/alert', [$key => session()->getFlash($key)]);
            }
        }   
    }
}

if (!function_exists('router')) {
    function router() : Router
    {
        return app()->router();
    }
}

if (!function_exists('checkAuth')) {
    function checkAuth() : bool
    {
        return session()->has('user');
    }
}

if (!function_exists('cache')) {
    function cache() : Cache
    {
        return app()->cache();
    }
}

if (!function_exists('env')) {
    function env(string $key, mixed $default = '') : mixed
    {
        return Env::get($key, $default);
    }
}

if (!function_exists('container')) {
    function container() : Container
    {
        return app()->container();
    }
}

use PHPFramework\Security\CsrfToken;

function csrf() : string
{
    return '<input type="hidden" name="' . CsrfToken::INPUT_FIELD . '" value="' . h(CsrfToken::get()) . '">';
}