<?php


namespace microfw\core;


trait TraitSingleton
{
    private static $instance;

    /**
     * @return object
     */
    public static function instance(): self
    {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}