<?php

spl_autoload_register(function($class) {

    $autoload = [
        'App\\' => '/../app/',
        'Infrastructure\\' => '/Foundation/',
    ];

    foreach ($autoload as $prefix => $path) {
        $length = strlen($prefix);
        $path = __DIR__ . $path;

        if (strncmp($prefix, $class, $length) !== 0) {
            continue;
        }
    
        $relative_class = substr($class, $length); 
        $file = $path . str_replace ('\\', '/', $relative_class) . '.php';
    
        if(file_exists($file)) {
            require $file;
        }
    }
});