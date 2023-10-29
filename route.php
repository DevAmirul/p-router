<?php

use Devamirul\PRouter\Request\Request;

// not return error.
// $router->get('/:id', function (Request $request) {
//     return $request->method();
// })->name('home');


$router->get('/', function (Request $request) {
    return $request->method();
})->name('home');

// $router->match(['post', 'put', 'delete'], '/match', function () {
//     echo 'user';
// })->name('matchName');

// $router->any('/anypath/xx/*', function () {
//     echo 'echo any';
// })->name('anyName');

// $router->get('/getpath/:id', function (Request $request) {
//     echo 'echo get';
// })->name('getName');

// $router->fallback(function () {
//     echo 'fallback';
// });

// ->middleware('auth');