<?php


// use App\Controllers\HomeController;
// use Devamirul\PRouter\Request\Request;


// not return error.
// $router->get('/:id', function (Request $request) {
//     return $request->method();
// })->name('home');


$router->get('/home', function ($request) {
    return toRoute('login', ['id'=>2]);
});

$router->get('/login/:id', function ($request) {
    return $request->getParam();
})->name('login')->where(['id' => '^\d+$']);


// use App\Controllers\HomeController;

// $router->get('/login/:id', [HomeController::class, 'index']);

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