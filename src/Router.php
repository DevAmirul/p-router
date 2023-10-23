<?php

namespace Devamirul\PRouter;

use Devamirul\PRouter\Middleware\BaseMiddleware;
use Devamirul\PRouter\Request\Request;
use Devamirul\PRouter\Traits\Singleton;
use Exception;

class Router {
    use Singleton;

    /**
     * Set method name.
     */
    private string $method;

    /**
     * Set previous called method name.
     */
    private ?string $prevMethod = null;

    /**
     * List of match methods.
     */
    private array $matchMethods;

    /**
     * List of all methods.
     */
    private array $allMethods;

    /**
     * Store all routes.
     */
    private array $routes = [];

    /**
     * Store all route names.
     */
    private array $routeNames = [];

    /**
     * Set request instance.
     */
    public Request $request;

    private function __construct() {
        $this->allMethods = ['get', 'post', 'delete', 'put', 'patch'];

        $this->request = Request::singleton();
    }

    /**
     * Add user defined routes to the $this->routers property.
     */
    public function addRoute(string $method, string $path, array | callable $callback): Router {
        $this->method = $method;

        $path = ltrim($path, '/');

        $this->routes[$method][] = [
            'path'       => ($path === '/') ? '/' : explode('/', $path),
            'callback'   => $callback,
            'name'       => null,
            'middleware' => config('middleware', $this->method) ? config('middleware', $this->method) : null,
            'where'      => null,
        ];
        return $this;
    }

    /**
     * Store "GET" method router in $this->routers property.
     */
    public function get(string $path, array | callable $callback): static {
        $this->prevMethod = __FUNCTION__;
        return $this->addRoute('get', $path, $callback);
    }

    /**
     * Store "POST" method router in $this->routers property.
     */
    public function post(string $path, array | callable $callback): static {
        $this->prevMethod = __FUNCTION__;
        return $this->addRoute('post', $path, $callback);
    }

    /**
     * Store "PUT" method router in $this->routers property.
     */
    public function put(string $path, array | callable $callback): static {
        $this->prevMethod = __FUNCTION__;
        return $this->addRoute('put', $path, $callback);
    }

    /**
     * Store "PATCH" method router in $this->routers property.
     */
    public function patch(string $path, array | callable $callback): static {
        $this->prevMethod = __FUNCTION__;
        return $this->addRoute('patch', $path, $callback);
    }

    /**
     * Store "DELETE" method router in $this->routers property.
     */
    public function delete(string $path, array | callable $callback): static {
        $this->prevMethod = __FUNCTION__;
        return $this->addRoute('delete', $path, $callback);
    }

    /**
     * Sometimes you may need to register a route that responds to multiple HTTP verbs.
     * You may do so using the match method.
     */
    public function match(array $methods, string $path, array | callable $callback): static {
        $this->prevMethod   = __FUNCTION__;
        $this->matchMethods = $methods;

        foreach ($methods as $method) {
            $this->addRoute($method, $path, $callback);
        }
        return $this;
    }

    /**
     * Sometimes you may need to register a route that responds to multiple HTTP verbs.
     * You may do so using the match method.
     */
    public function any(string $path, array | callable $callback): static {
        $this->prevMethod = __FUNCTION__;

        $allMethods = ['get', 'post', 'delete', 'put', 'patch'];

        foreach ($allMethods as $method) {
            $this->addRoute($method, $path, $callback);
        }
        return $this;
    }

    /**
     * Set router name.
     */
    public function name(string $name): static {
        if ($this->prevMethod === 'any') {
            foreach ($this->allMethods as $method) {
                $this->helperFunctionOfName($name, $method);
            }
        } elseif ($this->prevMethod === 'match') {
            foreach ($this->matchMethods as $method) {
                $this->helperFunctionOfName($name, $method);
            }
        } else {
            $this->helperFunctionOfName($name, $this->method);
        }
        return $this;
    }

    /**
     * Set router middleware.
     */
    public function middleware(string | array $middleware): static {
        if ($this->prevMethod === 'any') {
            foreach ($this->allMethods as $method) {
                $this->helperFunctionOfMiddleware($middleware, $method);
            }
        } elseif ($this->prevMethod === 'match') {
            foreach ($this->matchMethods as $method) {
                $this->helperFunctionOfMiddleware($middleware, $method);
            }
        } else {
            $this->helperFunctionOfMiddleware($middleware, $this->method);
        }
        return $this;
    }

    /**
     * Set regular expression for dynamic params.
     */
    public function where(string | array $expression = null): static {
        if ($this->prevMethod === 'any') {
            if (is_string($expression)) {
                foreach ($this->allMethods as $method) {
                    $this->routes[$method][array_key_last($this->routes[$method])]['where'] = [$expression];
                }
            } else {
                foreach ($this->allMethods as $method) {
                    $this->routes[$method][array_key_last($this->routes[$method])]['where'] = $expression;
                }
            }
        } elseif ($this->prevMethod === 'match') {
            if (is_string($expression)) {
                foreach ($this->matchMethods as $method) {
                    $this->routes[$method][array_key_last($this->routes[$method])]['where'] = [$expression];
                }
            } else {
                foreach ($this->matchMethods as $method) {
                    $this->routes[$method][array_key_last($this->routes[$method])]['where'] = $expression;
                }
            }
        } else {
            if (is_string($expression)) {
                $this->routes[$this->method][array_key_last($this->routes[$this->method])]['where'] = [$expression];
            } else {
                $this->routes[$this->method][array_key_last($this->routes[$this->method])]['where'] = $expression;
            }
        }
        return $this;
    }

    /**
     *
     */
    public function fallback(array | callable $callback): void {
        $this->routes['fallback'] = [
            'callback' => $callback,
        ];
    }

    /**
     * This method is called from the run method, this method resolves all routers.
     * And it is decided which router will do which job.
     */
    public function resolve(): mixed {
        // dd($this->routes);

        $path = explode('/', ltrim($this->request->path(), '/'));

        if (isset($this->routes[$this->request->method()])) {
            foreach ($this->routes[$this->request->method()] as $routes) {
                $url        = '';
                $params     = [];
                $whereIndex = 0;

                if (sizeof($routes['path']) === sizeof($path)) {

                    foreach ($routes['path'] as $key => $route) {

                        if ($route === $path[$key]) {
                            $url .= '/' . $path[$key];
                        } elseif (str_starts_with($route, ':')) {

                            if ($routes['where']) {
                                if (!preg_match('/' . $routes['where'][$whereIndex] . '/', $path[$key])) {
                                    throw new Exception('Route param expression does not match', 404);
                                }
                                $whereIndex++;
                            }
                            $params[] = $path[$key];
                            $url .= '/' . $path[$key];

                        } elseif ($route === '*') {
                            // $u = '';
                            // foreach (range($key, sizeof($path) - 1) as $pathKey) {
                            //     $u .= $path[$pathKey];
                            // }
                            dd($route);

                            // $url .= '/' . $path[$key];
                        } else {
                            break;
                        }
                    }
                    // dd($path);

                    if (ltrim($this->request->path(), '/') === ltrim($url, '/')) {
                        if (!$url) {
                            throw new Exception('This route did not defined.', 404);
                        }

                        BaseMiddleware::resolve($routes['middleware'], $this->request);

                        if (is_callable($routes['callback'])) {
                            return call_user_func($routes['callback'], ...$params);
                        } elseif (is_array($routes['callback'])) {

                            if (is_string($routes['callback'][0]) && is_string($routes['callback'][1])) {
                                $controllerInstance = new $routes['callback'][0];

                                return call_user_func_array(
                                    [$controllerInstance, $routes['callback'][1]],
                                    [$this->request]
                                );
                            }
                        }
                    }
                }
            }
            if (isset($this->routes['fallback'])) {
                $fallback = $this->routes['fallback'];

                if (is_callable($fallback['callback'])) {
                    return call_user_func($fallback['callback']);
                } elseif (is_array($routes['callback'])) {
                    if (is_string($fallback['callback'][0]) && is_string($fallback['callback'][1])) {
                        $controllerInstance = new $fallback['callback'][0];

                        return call_user_func_array(
                            [$controllerInstance, $fallback['callback'][1]],
                            [$this->request]
                        );
                    }
                }
            } else {
                throw new Exception('This route did not defined.', 404);
            }
        } else {
            throw new Exception('The route method does not match.', 404);
        }
    }

    /**
     * Finds routes by route name
     */
    public function route(string $name, string | array $params = null) {
        foreach ($this->routes[$this->request->method()] as $routes) {
            if ($routes['name'] === $name) {
                if (is_string($params)) {
                    $params = array($params);
                }

                if (!$routes['where']) {
                    $url        = '';
                    $whereIndex = 0;

                    foreach ($routes['path'] as $key => $value) {
                        if (str_starts_with($value, ':') && $params) {
                            $url .= '/' . $params[$whereIndex];
                            $whereIndex++;

                        } elseif (str_starts_with($value, ':') && !$params) {
                            throw new Exception('This route\'s param missing', 404);
                        } elseif (!str_starts_with($value, ':') && $params) {
                            throw new Exception('Passed unnecessary params to route function', 404);
                        } else {
                            $url .= '/' . $value;
                        }
                    }
                    redirect($url);

                } elseif (isset($routes['where']) && isset($params) && (sizeof($params) === sizeof($routes['where']))) {
                    foreach ($routes['where'] as $key => $value) {
                        if (!preg_match('/' . $value . '/', $params[$key])) {
                            throw new Exception('Route param expression does not match', 404);
                        }
                    }

                    $url        = '';
                    $whereIndex = 0;

                    foreach ($routes['path'] as $key => $value) {
                        if (str_starts_with($value, ':')) {
                            $url .= '/' . $params[$whereIndex];
                            $whereIndex++;
                        } else {
                            $url .= '/' . $value;
                        }
                    }
                    redirect($url);
                } else {
                    throw new Exception('Route param expression does not match', 404);
                }
            }
        }
        throw new Exception('Route name did not defined', 404);
    }

    /**
     * Helper function of name.
     */
    public function helperFunctionOfName(string $name, string $method): void {
        if (!empty($this->routeNames[$method])) {
            if (in_array($name, $this->routeNames[$method])) {
                throw new Exception('Router name (' . $name . ') has been used more than once');
            }
        }
        $this->routes[$method][array_key_last($this->routes[$method])]['name'] = $name;
        $this->routeNames[$method][]                                           = $name;
    }

    /**
     * Helper function of middleware.
     */
    public function helperFunctionOfMiddleware(string | array $middleware, string $method): void {
        $this->routes[$method][array_key_last($this->routes[$method])]['middleware'][] = $middleware;
    }

}
