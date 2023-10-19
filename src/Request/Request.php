<?php

namespace Devamirul\PRouter\Request;

use Devamirul\PRouter\Traits\Singleton;

class Request {
    use Singleton;
    
    private function __construct() {}

    /**
     * Get requested url.
     */
    public function path(): string {
        $url = parse_url($_SERVER['REQUEST_URI']);
        return trim($url['path']) ?? null;
    }

    /**
     * Get requested query params.
     */
    public function query(string $param = ''): string | array | null {
        $url   = parse_url($_SERVER['REQUEST_URI']);
        $query = $url['query'] ?? null;
        parse_str($query, $params);

        if ($param) {
            return $params[$param] ?? null;
        }
        return $params;
    }

    /**
     * Get requested method.
     */
    public function method(): string {
        $method = isset($_POST['_method']) ? $_POST['_method'] : $_SERVER['REQUEST_METHOD'];
        return strtolower($method);
    }

    /**
     * Checks and returns boolean whether the method is GET.
     */
    public function isGet(): string {
        return $this->method() === 'get';
    }

    /**
     * Checks and returns boolean whether the method is POST.
     */
    public function isPost(): string {
        return $this->method() === 'post';
    }

    /**
     * Checks and returns boolean whether the method is DELETE.
     */
    public function isDelete(): string {
        return $this->method() === 'delete';
    }

    /**
     * Checks and returns boolean whether the method is PUT.
     */
    public function isPut(): string {
        return $this->method() === 'put';
    }

    /**
     * Checks and returns boolean whether the method is PATCH.
     */
    public function isPatch(): string {
        return $this->method() === 'patch';
    }

    /**
     * Retrieve all requested data., you can also get single data from here.
     * If no data is found, the default data will be returned.
     */
    public function input(string $key = '', mixed $default = null): mixed {
        if ($key) {
            if (isset($_REQUEST[$key])) {
                return $_REQUEST[$key];
            }
        } else {
            $input = [];

            foreach ($_REQUEST as $index => $value) {
                $input[$index] = $value;
            }
            return $input;
        }
        return $default;
    }

    /**
     * Retrieve all requested data.
     */
    public function all(): mixed {
        $all = [];

        foreach ($_REQUEST as $key => $value) {
            $all[$key] = $value;
        }
        return $all;
    }

    /**
     * Retrieve some specific data from all data requested.
     */
    public function only(): mixed {
        $only = [];
        $args = func_get_args();

        foreach ($args as $key) {
            if (isset($_REQUEST[$key])) {
                $only[$key] = $_REQUEST[$key];
            }
        }
        return $only;
    }
}
