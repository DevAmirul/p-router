<?php

use Devamirul\PRouter\Request\Request;
use Devamirul\PRouter\Router;
// use Exception;

if (!function_exists('config')) {
    /**
     * Get config data.
     */
    function config(string $file, string $key): string | array {
        $data = require APP_ROOT . "/app/config/{$file}.php";
        if (isset($data[$key])) {
            return $data[$key];
        } else {
            throw new Exception("Key: ($key) not found in config");
        }
    }
}

if (!function_exists('dd')) {
    /**
     * View the data in details then exit the code.
     */
    function dd(mixed $value): void {
        if (is_string($value)) {
            echo $value;
        } else {
            echo '<pre>';
            print_r($value);
            echo '</pre>';
        }
        die();
    }
}

if (!function_exists('dump')) {
    /**
     * View the data in details.
     */
    function dump(mixed $value): void {
        if (is_string($value)) {
            echo $value;
        } else {
            echo '<pre>';
            print_r($value);
            echo '</pre>';
        }
    }
}

if (!function_exists('request')) {
    /**
     * Get request instance.
     */
    function request(): Request {
        return Request::singleton();
    }
}

if (!function_exists('setCsrf')) {
    /**
     * Set new CSRF value.
     */
    function setCsrf(): string {
        if (!isset($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(50));
        }
        return '<input type="hidden" name="csrf" value="' . $_SESSION['csrf'] . '">';
    }
}

if (!function_exists('isCsrfValid')) {
    /**
     * Check CSRF is valid or not.
     */
    function isCsrfValid(): bool {
        if (!isset($_SESSION['csrf']) || !isset($_POST['csrf'])) {
            return false;
        }
        if ($_SESSION['csrf'] != $_POST['csrf']) {
            return false;
        }
        return true;
    }
}

if (!function_exists('setMethod')) {
    /**
     * Set form method, like put/patch/delete.
     */
    function setMethod(string $methodName): string {
        return '<input type="hidden" name="_method" value="' . $methodName . '">';
    }
}

if (!function_exists('redirect')) {
    /**
     * Get an instance of the redirect.
     */
    function redirect(string $redirectLink): void {
        header('Location: ' . $redirectLink);
    }
}

if (!function_exists('back')) {
    /**
     * Create a new redirect response to the previous location.
     */
    function back(): void {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
}

if (!function_exists('route')) {
    /**
     * Finds routes by route name
     */
    function route(string $name, array | string $params = null): void {
        Router::singleton()->route($name, $params);
    }
}
