<?php

namespace Infrastructure;

use Closure;
use Infrastructure\View;
use Infrastructure\Request;

class Router
{
    /**
     * Multi-dimensional array of routes, split up by method and uri
     *
     * @var array
     */
    protected static $routes = [];

    /**
     * Root URL of the application
     *
     * @var string
     */
    protected static $root_url;

    /**
     * Indicates if routes are currently being loaded
     * inside of a closure function
     *
     * @var boolean
     */
    protected static $in_closure = false;

    /**
     * Temporary store for routes loaded inside of a
     * closure function
     *
     * @var array
     */
    protected static $closure_routes = [];

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

    public static function __callStatic($name, $arguments)
    {
        $methods = [
            'get',
            'post',
            'put',
            'patch',
            'delete'
        ];

        // If a method is being called that matches the available request methods, add a new route
        if (in_array($name, $methods)) {
            self::addRoute(strtoupper($name), $arguments[0], $arguments[1]);
        }
    }

    /**
     * Run the router to resolve and execute the current request
     *
     * @return void
     */
    public static function run()
    {
        self::$root_url = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';

        self::getRoute();
        self::runRoute();
    }

    /**
     * Add a new GET route that will immediately render a given view
     *
     * @param string $uri Server URI for route
     * @param string $view View file to render
     * @param array $vars Variables to include in the view
     * @return void
     */
    public static function view(string $uri, string $view, array $vars = [])
    {
        $action = function () use ($view, $vars) {
            View::render($view, $vars);
        };

        self::addRoute('GET', $uri, $action);
    }

    /**
     * Exit the application and return a 404
     *
     * @return void
     */
    public static function show404()
    {
        exit(http_response_code(404));
    }

    /**
     * Run the specified middleware before carrying out any further
     * routes included in the givene handler
     *
     * @param mixed $action Action to call as middleware
     * @param Closure $handler Function including further routes to run after middleware
     * @return void
     */
    public static function middleware($action, Closure $handler)
    {
        if (!is_callable($action)) {
            $middleware = self::loadClass('App\Middleware\\', ucfirst($action));

            $method = 'run';

            if (!method_exists($middleware, $method)) {
                self::show404();
            }

            $action = function () use ($middleware, $method) {
                $middleware->{$method}();
            };
        }

        self::$in_closure = true;

        $handler();

        $routes = self::$closure_routes;
        self::$closure_routes = [];

        $mapped_routes = [];

        // Load routes inside closure into a temporary array to then be merged with the main app routes
        foreach ($routes as $method => $uris)
        {
            foreach ($uris as $uri => $route) {
                if (is_callable($action)) {
                    $route['middleware'][] = $action;
                }
                
                $mapped_routes[$method][$uri] = $route;
            }
        }

        self::$routes = array_merge_recursive(self::$routes, $mapped_routes);

        self::$in_closure = false;
    }

    /**
     * Redirect request to the specified URI
     *
     * @param string $uri URI to redirect to
     * @return void
     */
    public static function redirect(string $uri = '')
    {
        header('Location: ' . self::generateLink($uri));
        die();
    }

    /**
     * Generate a full URL to the specified URI
     *
     * @param string $uri URI to generate link for
     * @return string
     */
    public static function generateLink(string $uri = '')
    {
        return getenv('SITE_URL') . $uri;
    }

    /**
     * Add a new route
     *
     * @param string $method Server request method of route
     * @param string $uri Server URI of route
     * @param mixed $action Action to execute for route
     * @return void
     */
    protected static function addRoute(string $method, string $uri, $action)
    {   
        if (self::$in_closure) {
            self::$closure_routes[$method][$uri]['action'] = $action;
        } else {
            self::$routes[$method][$uri]['action'] = $action;
        }
    }

    /**
     * Get the corresponding route for the current request
     *
     * @return void
     */
    protected static function getRoute()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = strtok($_SERVER['REQUEST_URI'], '?');

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
    protected static function runRoute()
    {
        $request = Request::getCurrent();

        if (array_key_exists('middleware', self::$current_route)) {
            foreach (self::$current_route['middleware'] as $middleware) {
                $middleware();
            }
        }

        if (is_callable(self::$current_route['action'])) {
            $action = self::$current_route['action'];
            exit($action($request));
        }
        
        $action = explode('@', self::$current_route['action']);

        if (!array_key_exists(0, $action) || !array_key_exists(1, $action)) {
            self::show404();
        }

        self::loadController($action[0]);
        $method = $action[1];

        if (!method_exists(self::$current_controller, $method)) {
            self::show404();
        }

        exit(self::$current_controller->{$method}($request));
    }

    /**
     * Load a controller instance from the given string name
     *
     * @param string $controller String name of the controller to load
     * @return void
     */
    protected static function loadController(string $controller)
    {
        $controller = self::loadClass('App\Controllers\\', $controller);

        self::$current_controller = new $controller;
    }

    /**
     * Load a new class instance from the given string namespace and name
     *
     * @param string $namespace Namespace containing class to load
     * @param string $class Name of class to load
     * @return mixed
     */
    protected static function loadClass(string $namespace, string $class)
    {
        $class = $namespace . $class;

        if (!class_exists($class)) {
            self::show404();
        }

        return new $class;
    }
}
