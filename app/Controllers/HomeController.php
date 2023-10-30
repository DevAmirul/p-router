<?php

namespace App\Controllers;

use Devamirul\PRouter\Request\Request;
use Devamirul\PRouter\Controller\BaseController;

class HomeController extends BaseController {

    /**
     * Dummy method
     */
    public function index(int $id){
        return $id;
    }
}