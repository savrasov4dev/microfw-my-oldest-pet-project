<?php

namespace libs\components;

class Debug
{
    public static function print($var)
    {
        echo "<pre>";
        print_r($var);
        echo "</pre>";
    }

    public static function dump($var)
    {
        echo "<pre>";
        print_r($var);
        echo "</pre>";
    }
}