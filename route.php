<?php

use App\Controllers\HomeController;
use Devamirul\PRouter\Request\Request;


// not return error.
// $router->get('/:id', function (Request $request) {
//     return $request->method();
// })->name('home');


$router->get('/', function ($request) {
    // return '/';
    return toRoute('home');
});

$router->get('/home', function () {
    // return toRoute('login', ['id'=>2]);
    return $_GET;
})->name('home');

// $router->get('/login/:id/:name', function ($request) {
//     return $request->getParam();
// })->name('login')->where(['id' => '^\d+$','name' => '^\d+$'])
// ->middleware('auth');


$router->get('/login/:id', [HomeController::class, 'index'])->name('login');

// $router->match(['get', 'put', 'delete'], '/match/:id', function () {
//     echo 'match';
// })->name('matchName')->where(['id' => '^\d+$'])
// ->middleware('auth');

// $router->any('/anypath/xx/*', function () {
//     echo 'echo any';
// })->name('anyName')
// ->middleware(['auth', 'any']);

// $router->get('/getpath/:id', function (Request $request) {
//     echo 'echo get';
// })->name('getName');

$router->fallback(function () {
    echo 'fallback';
});