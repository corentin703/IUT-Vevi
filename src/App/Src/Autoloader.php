<?php

namespace App\Src;

class Autoloader
{
    /**
     *  Met en place les différents autoloader de l'App php
     */
    public static function register()
    {
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    public static function autoload($class)
    {
        $nameSpace = explode('\\', $class);
        $class = implode('/', $nameSpace);
        require_once $_SERVER['DOCUMENT_ROOT'] . '/' . $class . '.php';
    }


//    public static function autoload(string $class)
//    {
//        $namespace = explode('\\', $class);
//        $namespace = array_map('strtolower', $namespace);
//        $i = count($namespace) -1;
//        $namespace[$i] = ucfirst($namespace[$i]);
//        $class = implode('/', $namespace);
//        require_once '..' . '/' . $class . '.php';
//    }

}