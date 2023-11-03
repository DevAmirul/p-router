<?php

namespace App\Middlewares;

use Devamirul\PRouter\Interfaces\Middleware;
use Devamirul\PRouter\Request\Request;

class AuthMiddleware implements Middleware {

    /**
     * Check if the request is authenticated and act accordingly.
     */
    public function handle(Request $request): void {
        // if (!$_SESSION['user']) {
        //     redirect('login');
        // }
        // return;
    }

}