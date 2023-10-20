<?php

namespace App\Middleware;

use Devamirul\PRouter\Interfaces\Middleware;
use Devamirul\PRouter\Request\Request;

class AuthMiddleware implements Middleware {

    /**
     * Check if the request is authenticated and act accordingly.
     */
    public function handle(Request $request): void {
        return;
    }

}