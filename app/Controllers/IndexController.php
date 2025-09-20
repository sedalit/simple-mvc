<?php

namespace App\Controllers;

use PHPFramework\Application;

class IndexController {

    public function index() : mixed
    {
        return Application::view()->render('index');
    }
}