<?php

namespace Infrastructure;

use Infrastructure\View;

class Router
{
    /**
     * Multi-dimensional array of routes, split up by method and uri
     *
     * @var array
     */
    protected static $routes = [];

    /**
     * Corresponding route for the current request
     *
     * @var mixed
     */
    protected static $current_route = null;

    /**
     * Corresponding controller for the current route
     *
     * @var mixed
     */
    protected static $current_controller = null;

    /**
     * Run the router to resolve and execute the current request
     *
     * @return void
     */
    public static function run(): void
    {
        self::getRoute();
        self::runRoute();
    }

    /**
     * Add a new GET route
     *
     * @param string $uri Server URI for route
     * @param mixed $action Action to execute for route
     * @return void
     */
    public static function get(string $uri, $action): void
    {
        self::addRoute('GET', $uri, $action);
    }

    /**
     * Add a new GET route that will immediately render a given view
     *
     * @param string $uri Server URI for route
     * @param string $view View file to render
     * @param array $vars Variables to include in the view
     * @return void
     */
    public static function view(string $uri, string $view, array $vars = []): void
    {
        $action = function () use ($view, $vars) {
            View::render($view, $vars);
        };

        self::addRoute('GET', $uri, $action);
    }

    /**
     * Add a new route
     *
     * @param string $method Server request method of route
     * @param string $uri Server URI of route
     * @param mixed $action Action to execute for route
     * @return void
     */
    protected static function addRoute(string $method, string $uri, $action): void
    {
        self::$routes[$method][$uri] = $action;
    }

    /**
     * Get the corresponding route for the current request
     *
     * @return void
     */
    protected static function getRoute(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        if (!array_key_exists($method, self::$routes) || !array_key_exists($uri, self::$routes[$method])) {
            self::show404();
        }

        self::$current_route = self::$routes[$method][$uri];
    }

    /**
     * Execute the current route
     *
     * @return void
     */
    protected static function runRoute(): void
    {
        if (is_callable(self::$current_route)) {
            $action = self::$current_route;
            exit($action());
        }
        
        $action = explode('@', self::$current_route);

        if (!array_key_exists(0, $action) || !array_key_exists(1, $action)) {
            self::show404();
        }

        self::loadController($action[0]);
        $method = $action[1];

        if (!method_exists(self::$current_controller, $method)) {
            self::show404();
        }

        exit(self::$current_controller->{$method}());
    }

    /**
     * Load a controller instance from the given string name
     *
     * @param string $controller String name of the controller to load
     * @return void
     */
    protected static function loadController(string $controller): void
    {
        $controller = 'App\Controllers\\' . $controller;

        if (!class_exists($controller)) {
            self::show404();
        }

        self::$current_controller = new $controller;
    }

    /**
     * Exit the application and return a 404
     *
     * @return void
     */
    protected static function show404()
    {
        exit(http_response_code(404));
    }
}