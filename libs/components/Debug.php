<?php

namespace microfw\libs\components;

class Debug
{
    public static function dbg($var)
    {
        echo "<pre>";
        print_r($var);
        echo "</pre>";
    }

    public static function dump($var)
    {
        echo "<pre>";
        var_dump($var);
        echo "</pre>";
    }
}