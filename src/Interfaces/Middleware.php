<?php

namespace Devamirul\PRouter\Interfaces;

use Devamirul\PRouter\Request\Request;

interface Middleware {

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request): void;

}
