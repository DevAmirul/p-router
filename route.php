<?php

$router->get('/', function () {
    echo 'home';
});

$router->get('/user', function () {
    echo 'user';
});
    // ->middleware('auth');