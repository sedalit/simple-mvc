<?php

if (PHP_MAJOR_VERSION < 8) {
    die("Require PHP version >= 8");
}

require_once __DIR__ . '/../config/init.php';

echo 'Hello';