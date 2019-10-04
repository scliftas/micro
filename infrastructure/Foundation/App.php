<?php

namespace Infrastructure;

use Infrastructure\Session;
use Infrastructure\Env;
use Infrastructure\Request;
use Infrastructure\Router;

class App
{
    /**
     * Start a new instance of the whole application
     *
     * @return void
     */
    public static function boot()
    {
        Session::start();

        Env::load();
        
        require_once __DIR__ . '/../../app/Routes/routes.php';

        require_once __DIR__ . '/globals.php';

        Request::setCurrent();

        Router::run();
    }
}