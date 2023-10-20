<?php

use Devamirul\PRouter\Middleware\Middlewares\CsrfMiddleware;

return [

    /**
     * Autoloaded All Middleware.
     */
    'middleware' => [
        'csrf'  => Devamirul\PRouter\Middleware\Middlewares\CsrfMiddleware::class,
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