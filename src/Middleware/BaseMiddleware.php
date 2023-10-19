<?php

namespace Devamirul\PRouter\Middleware;

use Devamirul\PRouter\Request\Request;

class BaseMiddleware {

    /**
     * Resolve middleware.
     */
    public static function resolve($middlewareNames, Request $request) {
        $arguments = [];

        if (empty($middlewareNames)) {
            return;
        }

        foreach ($middlewareNames as $middleware) {
            if (strpos($middleware, ":")) {

                $explodeMiddleware = explode(':', $middleware);

                $name = $explodeMiddleware[0];

                $arguments = explode(',', $explodeMiddleware[1]);
            } else {
                $name = $middleware;
            }

            $middleware = config('middleware', 'middleware')[$name] ?? null;

            if (!$middleware) {
                throw new \Exception('No matching middleware found for key' . $name);
            }

            (new $middleware())->handle($request, $arguments);
        }
    }

}
