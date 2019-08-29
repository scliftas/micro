<?php

namespace Infrastructure;

use Infrastructure\Request;
use Infrastructure\Router;

class App
{
    public static function boot(): void
    {
        require_once __DIR__ . '/../../app/Routes/routes.php';

        Request::setCurrent();

        Router::run();
    }
}