<?php

namespace App\Controllers;

use PHPFramework\Controller;

class IndexController extends Controller {

    public function index() : mixed
    {
        return $this->render('index');
    }
}