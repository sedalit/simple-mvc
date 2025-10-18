<?php

// Загружаем конфигурацию для тестов
define('ROOT', dirname(__DIR__));
define('CONFIG', ROOT . '/tests/configs');
define('CACHE', ROOT . '/tests/cache');
define('UPLOADS', ROOT . '/uploads');
define('VIEWS', ROOT . '/tests/Views');
define('VALIDATION_RULES', ROOT . '/app/Validation/Rules');
define('ENV_PATH', ROOT . '/.env.test');
define('APP_NAME', 'Simple Tests');
define('PATH', 'http://test.local');
define('DEBUG', false);

require_once ROOT . '/vendor/autoload.php';
require_once ROOT . '/helpers/functions.php';