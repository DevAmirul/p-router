<?php

/**
 * Define root directory.
 */
define('APP_ROOT', dirname(__DIR__));

/**
 * Require composer autoloader.
 */
require __DIR__ . '/vendor/autoload.php';

$router = Devamirul\PRouter\Router::singleton();

$router->get('/', function(){
    echo 'home';
});






$router->resolve();