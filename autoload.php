<?php

spl_autoload_register(function ($class) {
    $classPath = __DIR__ . '/' . $class . '.php';
    if (file_exists($classPath)) {
        require_once $classPath;
    }
});
