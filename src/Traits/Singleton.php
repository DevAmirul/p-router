<?php

namespace Devamirul\PRouter\Traits;

trait Singleton {

    /**
     * Store object instance.
     */
    private static $instance = null;

    /**
     * Helps to create a singleton class
     */
    public static function singleton(): Object {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    private function __clone() {}

}
