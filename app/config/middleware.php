<?php

use App\Middleware\AuthMiddleware;

return [

    /**
     * Autoloaded All Middleware.
     */
    'middleware' => [
        'csrf' => Devamirul\PRouter\Middleware\Middlewares\CsrfMiddleware::class,
        'auth' => App\Middleware\AuthMiddleware::class,
    ],

    /**
     * In the following methods you can set some middleware alias name as default.
     */
    'get'        => [],
    'post'       => ['csrf'],
    'put'        => ['csrf'],
    'patch'      => ['csrf'],
    'delete'     => ['csrf'],

];