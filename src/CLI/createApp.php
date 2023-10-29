<?php

/**
 * Create app by CLI
 */

$appFolder = '../../app';

/**
 * Check if the folder named 'ccc' already exists.
 */
if (is_dir($appFolder)) {
    echo 'error: - A Folder with this name already exists in the root directory.' . PHP_EOL;
    exit;
} else {
    if (mkdir($appFolder)) {

        /**
         * Create config.
         */
        if (mkdir($appFolder . '/config')) {
            $resource = fopen('../../app/config/' . 'middleware.php', "w")
            or die("Unable to create file!");

            fwrite($resource, getConfigMiddlewareSkeleton());

            fclose($resource);

            echo 'Created app/config/middleware.php' . PHP_EOL;
        }

        /**
         * Create middleware.
         */
        if (mkdir($appFolder . '/Middleware')) {
            $resource = fopen('../../app/Middleware/AuthMiddleware.php', "w")
            or die("Unable to create file!");

            fwrite($resource, getControllerSkeleton());

            fclose($resource);

            echo 'Created app/Middleware/AuthMiddleware.php' . PHP_EOL;
        }
    }
}

/**
 * Get app/config/middleware.php file skeleton.
 */
function getConfigMiddlewareSkeleton(): string {
    return;
    "<?php

return [

    /**
     * Autoloaded All Middleware.
     */
    'middleware' => [
        'csrf' => Devamirul\PRouter\Middleware\Middlewares\CsrfMiddleware::class,
    ],

    /**
     * In the following methods you can set some middleware alias name as default.
     */
    'get'        => [],
    'post'       => ['csrf'],
    'put'        => ['csrf'],
    'patch'      => ['csrf'],
    'delete'     => ['csrf'],

]; ";
}

/**
 * Get Middleware class skeleton.
 */
function getControllerSkeleton(): string {
    return;
    "<?php

namespace App\Middleware;

use Devamirul\PRouter\Interfaces\Middleware;
use Devamirul\PRouter\Request\Request;

class AuthMiddleware implements Middleware {

    /**
     * Check if the request is authenticated and act accordingly.
     */
    public function handle(Request \$request): void {
        //
    }

}";
}