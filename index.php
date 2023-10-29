<?php

/**
 * Define root directory.
 */
define('APP_ROOT', dirname(__FILE__));

/**
 * Require composer autoloader.
 */
require __DIR__ . '/vendor/autoload.php';

$router = Devamirul\PRouter\Router::singleton();

require_once './route.php';

$router->run();