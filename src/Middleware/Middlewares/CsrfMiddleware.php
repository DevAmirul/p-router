<?php

namespace Devamirul\PRouter\Middleware\Middlewares;

use Devamirul\PRouter\Interfaces\Middleware;
use Devamirul\PRouter\Request\Request;
use Exception;

class CsrfMiddleware implements Middleware {

    /**
     * CSRF token will be checked. Exception will be thrown if not found
     */
    public function handle(Request $request): void {
        if (in_array($request->method(), ['post', 'delete', 'put', 'patch'])) {
            if (!isCsrfValid()) {
                throw new Exception('You do not have any csrf token to access this action', 419);
            }
            return;
        }
    }

}
