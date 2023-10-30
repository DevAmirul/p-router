<?php

/**
 * Create controller by CLI
 */
controller:

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
$controller = (string) readline('Enter a controller name: ');

if ($controller) {

    /**
     * Check if there is already a file with this middleware name.
     */
    if (file_exists('../../app/Controllers/' . ucfirst($controller) . 'Controller.php')) {
        echo 'error: - A file with this name already exists in the (app/Controller) folder, please try another name.' . PHP_EOL;

        goto controller;
    } else {
        $resource = fopen('../../app/Controllers/' . ucfirst($controller) . 'Controller.php', "w")
        or die("Unable to create file!");

        fwrite($resource, getControllerSkeleton(ucfirst($controller)));

        fclose($resource);

        echo 'Created app/Controllers/' . ucfirst($controller) . 'Controller.php' . PHP_EOL;
    }
}

/**
 * Get controller class skeleton.
 */
function getControllerSkeleton(string $controllerName): string {
    return sprintf(
    "<?php

namespace App\Controllers;

use Devamirul\PRouter\Request\Request;
use Devamirul\PRouter\Controller\BaseController;

class %sController extends BaseController {

    /**
     * Dummy method
     */
    public function index(Request \$request){

    }
}", $controllerName);
}