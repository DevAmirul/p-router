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
        // Define all http verbs.
        $this->allMethods = ['get', 'post', 'delete', 'put', 'patch'];

        // Get request singleton instance.
        $this->request = Request::singleton();
    }

    /**
     * Add user defined routes to the $this->routers property.
     */
    public function addRoute(string $method, string $path, array | callable $callback): static
    {
        // Set the Declare router method.
        $this->method = $method;

        $path = ltrim($path, '/');

        // Add route to '$this->router'.
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
    public function get(string $path, array | callable $callback): static
    {
        // Set the name of the called function.
        $this->prevMethod = __FUNCTION__;

        // Call 'addRoute' function.
        return $this->addRoute('get', $path, $callback);
    }

    /**
     * Store "POST" method router in $this->routers property.
     */
    public function post(string $path, array | callable $callback): static
    {
        // Set the name of the called function.
        $this->prevMethod = __FUNCTION__;

        // Call 'addRoute' function.
        return $this->addRoute('post', $path, $callback);
    }

    /**
     * Store "PUT" method router in $this->routers property.
     */
    public function put(string $path, array | callable $callback): static
    {
        // Set the name of the called function.
        $this->prevMethod = __FUNCTION__;

        // Call 'addRoute' function.
        return $this->addRoute('put', $path, $callback);
    }

    /**
     * Store "PATCH" method router in $this->routers property.
     */
    public function patch(string $path, array | callable $callback): static
    {
        // Set the name of the called function.
        $this->prevMethod = __FUNCTION__;

        // Call 'addRoute' function.
        return $this->addRoute('patch', $path, $callback);
    }

    /**
     * Store "DELETE" method router in $this->routers property.
     */
    public function delete(string $path, array | callable $callback): static
    {
        // Set the name of the called function.
        $this->prevMethod = __FUNCTION__;

        // Call 'addRoute' function.
        return $this->addRoute('delete', $path, $callback);
    }

    /**
     * Sometimes you may need to register a route that responds to multiple HTTP verbs.
     * You may do so using the match method.
     */
    public function match(array $methods, string $path, array | callable $callback): static
    {
        // Set the name of the called function.
        $this->prevMethod = __FUNCTION__;

        // Set match methods.
        $this->matchMethods = $methods;

        // Call 'addRoute' function.
        foreach ($methods as $method) {
            $this->addRoute($method, $path, $callback);
        }
        return $this;
    }

    /**
     * Sometimes you may need to register a route that responds to multiple HTTP verbs.
     * You may do so using the match method.
     */
    public function any(string $path, array | callable $callback): static
    {
        // Set the name of the called function.
        $this->prevMethod = __FUNCTION__;

        // Call 'addRoute' function.
        foreach ($this->allMethods as $method) {
            $this->addRoute($method, $path, $callback);
        }
        return $this;
    }

    /**
     * Set router name.
     */
    public function name(string $name): static
    {
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
    public function middleware(string | array $middleware): static
    {
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
    public function where(string | array $expression = null): static
    {
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
     * Set fallback route.
     */
    public function fallback(array | callable $callback): void {
        $this->routes['fallback'] = [
            'callback' => $callback,
        ];
    }

    /**
     * This method is called from the '$this->run()' method, this method resolves all routers.
     * And it is decided which router will do which job.
     */
    public function resolve(): mixed {
        $path = explode('/', ltrim($this->request->path(), '/'));

        // Check if the requested method is set.
        if (isset($this->routes[$this->request->method()])) {

            // Loop all route.
            foreach ($this->routes[$this->request->method()] as $routes) {
                $implodeRoutePath = implode('/', $routes['path']);
                $this->request->emptyParam();
                $params = [];
                $url    = '';

                // Check if requested path size and route path size equal
                // or check if "*" finds it in the route path
                // or check if "?" finds it in the route path.
                if (
                    (sizeof($routes['path']) === sizeof($path)) ||
                    in_array('*', $routes['path']) ||
                    str_ends_with($implodeRoutePath, '?')
                ) {
                    // Loop route path array.
                    foreach ($routes['path'] as $key => $route) {
                        // Check if requested path and route path equal.
                        if (isset($path[$key]) && $route === $path[$key]) {
                            $url .= '/' . $path[$key];
                        }
                        // check if ":" finds it in the route path dynamic param.
                        elseif (str_starts_with($route, ':')) {
                            // check if "?" finds it in the dynamic param.
                            if (str_ends_with($route, '?')) {
                                if (!str_ends_with($implodeRoutePath, $route)) {
                                    throw new Exception('The optional parameter must be used as the last parameter in the URL', 404);
                                } elseif (str_ends_with($implodeRoutePath, $route) && isset($path[$key])) {
                                    $url .= '/' . $path[$key];
                                    $optionalParam = ltrim(rtrim($route, '?'), ':');

                                    $this->request->setParam($optionalParam, $path[$key]);

                                    // Check if the dynamic param matches the regular expression.
                                    // Throw exception if no match.
                                    if ($routes['where'] && isset($routes['where'][$optionalParam])) {
                                        if (!preg_match('/' . $routes['where'][$optionalParam] . '/', $path[$key])) {
                                            throw new Exception('Route param expression does not match', 404);
                                        }
                                    }

                                    goto url;
                                } elseif (str_ends_with($implodeRoutePath, $route) && !isset($path[$key])) {
                                    goto url;
                                }
                            }
                            // Check if the dynamic param matches the regular expression.
                            // Throw exception if no match.
                            if ($routes['where'] && isset($routes['where'][ltrim($route, ':')])) {
                                if (!preg_match('/' . $routes['where'][ltrim($route, ':')] . '/', $path[$key])) {
                                    throw new Exception('Route param expression does not match', 404);
                                }
                            }

                            $this->request->setParam(ltrim($route, ':'), $path[$key]);
                            $params[] = $path[$key];
                            $url .= '/' . $path[$key];
                        }
                        // check if "*" finds it in the route path.
                        elseif ($route === '*') {
                            if ($key > sizeof($path) - 1) {
                                return $this->fallbackHandling();
                            }

                            foreach (range($key, sizeof($path) - 1) as $pathKey) {
                                $url .= '/' . $path[$pathKey];
                            }
                        } else {
                            goto continueLoop;
                        }
                    }

                    url:

                    // Check if requested path size and $url size equal.
                    if (ltrim($this->request->path(), '/') === ltrim($url, '/')) {
                        // If the url is empty, throw an exception
                        // or execute the fallback route if it is declared.
                        if (!$url) {
                            return $this->fallbackHandling();
                        }

                        // Resolve middleware.
                        BaseMiddleware::resolve($routes['middleware'], $this->request);

                        // Call callback method.
                        if (is_callable($routes['callback'])) {

                            return call_user_func($routes['callback'], $this->request);

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
                continueLoop:
                continue;
            }
            // If route did not defined or not found,
            // throw an exception or execute the fallback route if it is declared.
            return $this->fallbackHandling();
        } else {
            // If route method does not match,
            // throw an exception.
            throw new Exception('The route method does not match.', 404);
        }
    }

    /**
     * Run Application.
     */
    public function run(): void {
        try {
            $content = $this->resolve();

            if (is_array($content) || is_object($content)) {
                echo var_export($content, true);
            } else {
                echo $content;
            }
        } catch (\Exception $error) {
            http_response_code($error->getCode());
            echo $error->getMessage();
        }
    }

    /**
     * Finds route by route name and redirect this route.
     */
    public function toRoute(string $name, array $params = null) {
        // Iterate over all routes which defined with the Requested method.
        foreach ($this->routes[$this->request->method()] as $routes) {

            // Check if requested name and route name equal.
            if ($routes['name'] === $name) {
                $implodeRoutePath = implode('/', $routes['path']);
                $url              = '';

                foreach ($routes['path'] as $key => $route) {
                    if (str_starts_with($route, ':')) {
                        if (str_ends_with($route, '?')) {
                            $optionalParam = ltrim(rtrim($route, '?'), ':');

                            if (!str_ends_with($implodeRoutePath, $route)) {
                                throw new Exception('The optional parameter must be used as the last parameter in the URL', 404);
                            } elseif (str_ends_with($route, '?') && isset($params[$optionalParam])) {
                                if (isset($routes['where'][$optionalParam]) && !preg_match('/' . $routes['where'][$optionalParam] . '/', $params[$optionalParam])) {
                                    throw new Exception('Route param expression does not match', 404);
                                }

                                $url .= '/' . $params[$optionalParam];
                                unset($params[$optionalParam]);

                                if (sizeof($routes['path']) - 1 === $key && $params) {
                                    $url .= '?' . http_build_query($params);
                                }
                            } elseif (str_ends_with($route, '?') && !isset($params[$optionalParam])) {
                                if (sizeof($routes['path']) - 1 === $key && $params) {
                                    $url .= '?' . http_build_query($params);
                                }
                            }
                        } elseif (isset($params[ltrim($route, ':')])) {
                            if (isset($routes['where'][ltrim($route, ':')]) && !preg_match('/' . $routes['where'][ltrim($route, ':')] . '/', $params[ltrim($route, ':')])) {
                                throw new Exception('Route param expression does not match', 404);
                            }

                            $url .= '/' . $params[ltrim($route, ':')];
                            unset($params[ltrim($route, ':')]);

                            if (sizeof($routes['path']) - 1 === $key && $params) {
                                $url .= '?' . http_build_query($params);
                            }
                        } else {
                            throw new Exception('The route parameters are missing.', 404);
                        }
                    } elseif ($route === '*') {
                        throw new Exception('The name root cannot be used when you use the asterisk (*) symbol in the root.', 404);
                    } else {
                        $url .= '/' . $route;

                        if (sizeof($routes['path']) - 1 === $key && $params) {
                            $url .= '?' . http_build_query($params);
                        }
                    }
                }
                redirect($url);
            }
        }
        throw new Exception('Route name did not defined', 404);
    }

    /**
     * Helper function of name.
     */
    public function fallbackHandling(): mixed {
        if (isset($this->routes['fallback'])) {
            $fallback = $this->routes['fallback'];

            if (is_callable($fallback['callback'])) {
                return call_user_func($fallback['callback']);
            } elseif (is_array($fallback['callback'])) {
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
    }

    /**
     * Helper function of name.
     */
    public function helperFunctionOfName(string $name, string $method): void {
        // Check if root name is used once, Throw exception if name is used.
        if (!empty($this->routeNames[$method])) {
            if (in_array($name, $this->routeNames[$method])) {
                throw new Exception('Router name (' . $name . ') has been used more than once');
            }
        }
        // Set name to root
        $this->routes[$method][array_key_last($this->routes[$method])]['name'] = $name;

        $this->routeNames[$method][] = $name;
    }

    /**
     * Helper function of middleware.
     */
    public function helperFunctionOfMiddleware(string | array $middleware, string $method): void {
        // Set middleware to root
        if (is_array($middleware)) {
            if (is_array($this->routes[$method][array_key_last($this->routes[$method])]['middleware'])) {
                $array_merge                                                                 = array_merge($this->routes[$method][array_key_last($this->routes[$method])]['middleware'], $middleware);
                $this->routes[$method][array_key_last($this->routes[$method])]['middleware'] = $array_merge;
            } else {
                $this->routes[$method][array_key_last($this->routes[$method])]['middleware'] = $middleware;
            }
        } else {
            $this->routes[$method][array_key_last($this->routes[$method])]['middleware'][] = $middleware;
        }
    }
}
