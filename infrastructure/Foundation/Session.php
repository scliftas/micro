<?php

namespace Infrastructure;

class Session
{
    /**
     * Start a new session
     *
     * @return void
     */
    public static function start()
    {
        session_start();
    }

    /**
     * Destroy an existing session
     *
     * @return void
     */
    public static function destroy()
    {
        session_destroy();
    }

    /**
     * Set a session value
     *
     * @param string $name Name of value to set
     * @param mixed $value Value to set
     * @return void
     */
    public static function set(string $name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * Get a session value, if no value provided then the whole
     * session will be returned
     *
     * @param string $name Name of value to retrieve
     * @return void
     */
    public static function get(string $name = null)
    {
        return !is_null($name) ? $_SESSION[$name] : $_SESSION;
    }

    /**
     * Check whether or not the current session contains the 
     * specified value
     *
     * @param string $name Name of value
     * @return boolean
     */
    public static function has(string $name)
    {
        return array_key_exists($name, $_SESSION) && !empty($_SESSION[$name]);
    }
}