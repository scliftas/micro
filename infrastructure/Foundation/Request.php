<?php

namespace Infrastructure;

class Request
{
    /**
     * URI of the request
     *
     * @var string
     */
    protected $uri;

    /**
     * Array of given request parameters
     *
     * @var array
     */
    protected $params = [];

    /**
     * Object of given request data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Array of given server headers
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Current request instance
     *
     * @var \Infrastructure\Request
     */
    protected static $current;

    /**
     * Create a new request instance
     */
    public function __construct()
    {
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->params = $_GET;
        
        $json = file_get_contents('php://input');
        $this->data = json_decode($json);

        $this->headers = getallheaders();
    }

    /**
     * Set the current request instace
     *
     * @param \Infrastructure\Request $request
     * @return void
     */
    public static function setCurrent($request = null): void
    {
        self::$current = $request ?: new Request();
    }

    /**
     * Get the current request instance
     *
     * @return \Infrastructure\Request
     */
    public static function getCurrent(): Request
    {
        return self::$current;
    }

    /**
     * Get the request parameters
     *
     * @return array
     */
    public function params(): array
    {
        return $this->params;
    }

    /**
     * Get the request data
     *
     * @return object
     */
    public function data()
    {
        return $this->data;
    }
}