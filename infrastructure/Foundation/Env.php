<?php

namespace Infrastructure;

class Env
{
    /**
     * Load a local .env file into ENV variables that can
     * then be accessed throughout the application
     *
     * @return void
     */
    public static function load()
    {
        $env = file('.env');

        foreach ($env as $env_var) {
            $env_var = trim($env_var);

            if (empty($env_var)) {
                continue;
            }

            putenv($env_var);
        }
    }
}