<?php

/**
 * Create middleware by CLI
 */
middleware:

/**
 * First check if 'app' folder exists.
 */
if (!is_dir('../../app')) {
    echo 'Folder "app" not found. Please run "composer app"';
    exit;
}

/**
 * get middleware name.
 */
$middleware = (string) readline('Enter a middleware name: ');

if ($middleware) {

    /**
     * Check if there is already a file with this middleware name.
     */
    if (file_exists('../../app/Middlewares/' . ucfirst($middleware) . 'Middleware.php')) {
        echo 'error: - A file with this name already exists in the (app/Middleware) folder, please try another name.' . PHP_EOL;

        goto middleware;
    } else {
        $resource = fopen('../../app/Middlewares/' . ucfirst($middleware) . 'Middleware.php', "w")
        or die("Unable to create file!");

        fwrite($resource, getControllerSkeleton(ucfirst($middleware)));

        fclose($resource);

        echo 'Created app/Middlewares/' . ucfirst($middleware) . 'Middleware.php' . PHP_EOL;
    }
}

/**
 * Get middleware class skeleton.
 */
function getControllerSkeleton(string $middlewareName): string {
    return sprintf(
        "<?php

    namespace App\Middlewares;

    use Devamirul\PRouter\Interfaces\Middleware;
    use Devamirul\PRouter\Request\Request;

    class %sMiddleware implements Middleware {

        /**
         * Handle an incoming request.
         */
        public function handle(Request \$request): void {
            //
        }

    }", $middlewareName);
}