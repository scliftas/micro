<?php

namespace Infrastructure;

use Infrastructure\Router;

class App
{
    public static function boot(): void
    {
        require_once __DIR__ . '/../../app/Routes/routes.php';

        Router::run();
    }
}