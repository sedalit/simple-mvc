<?php

/** @var PHPFramework\Application $app */

use App\Controllers\ContactController;

$app->router()->get('/', function () {
    return 'Main page';
});

$app->router()->get('/contact', [ContactController::class, 'index']);

$app->router()->post('/contact', [ContactController::class, 'store']);