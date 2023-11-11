<?php

namespace Devamirul\PRouter\Middleware;

use Devamirul\PRouter\Request\Request;

class BaseMiddleware {

    /**
     * Resolve middleware.
     */
    public static function resolve($middlewareNames, Request $request): void {
        if (empty($middlewareNames)) {
            return;
        }

        $configMiddlewares = config('middleware', 'middleware') ?? null;

        if (empty($configMiddlewares)) {
            throw new \Exception('Middleware config is empty', 404);
        }

        foreach ($middlewareNames as $middleware) {
            if (!isset($configMiddlewares[$middleware])) {
                throw new \Exception('No matching middleware found for key ' . $middleware);
            }

            (new $configMiddlewares[$middleware]())->handle($request);
        }
    }
}
