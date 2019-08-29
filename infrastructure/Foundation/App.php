<?php

namespace Infrastructure;

use Infrastructure\Request;
use Infrastructure\Router;

class App
{
    /**
     * Start a new instance of the whole application
     *
     * @return void
     */
    public static function boot(): void
    {
        require_once __DIR__ . '/../../app/Routes/routes.php';

        Request::setCurrent();

        Router::run();
    }
}