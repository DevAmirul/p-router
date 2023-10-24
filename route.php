<?php

$router->get('/', function () {
    echo 'home';
})->name('home');

$router->match(['post', 'put', 'delete'], '/match', function () {
    echo 'user';
})->name('matchName');

$router->any('/anypath/xx/*', function () {
    echo 'echo any';
})->name('anyName');

$router->get('/getpath/:id?', function () {
    echo 'echo get';
})->name('getName');

$router->fallback(function () {
    echo 'fallback';
});

// ->middleware('auth');