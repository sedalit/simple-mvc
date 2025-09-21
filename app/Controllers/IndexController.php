<?php

namespace App\Controllers;

class IndexController extends BaseController {

    public function index() : mixed
    {
        dd(db());
        return $this->render('index');
    }
}