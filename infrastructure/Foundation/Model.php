<?php

namespace Infrastructure;

use PDO;

abstract class Model
{
    /**
     * Persistent database connection
     *
     * @var mixed
     */
    protected static $connection = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        if (is_null(static::$connection)) {
            static::connect();
        }
    }

    /**
     * Create a new database connection instance
     *
     * @return void
     */
    protected static function connect()
    {
        $username = getenv('MYSQL_USERNAME');
        $database = getenv('MYSQL_DATABASE');
        $password = getenv('MYSQL_PASSWORD');
        $server = getenv('MYSQL_SERVER');
        $dbh = new PDO('mysql:host=' . $server . ';dbname=' .$database, $username, $password);

        static::$connection = $dbh;
    }

    /**
     * Run a given query via PDO
     *
     * @param string $query_string Query to run
     * @param array $bindings Array of values to bind to the PDO query
     * @param boolean $transform Toggle transformation of the query result
     * @return mixed
     */
    protected function run(string $query_string, array $bindings = [], bool $transform = true)
    {
        $query = static::$connection->prepare($query_string);

        if (!empty($bindings)) {
            foreach ($bindings as $binding) {
                $binding['value'] = $this->escape($binding['value']);
                $type = array_key_exists('type', $binding) ? $binding['type'] : null;

                $query->bindValue($binding['name'], $binding['value'], $type);
            }
        }

        $query->execute();

        $result = [];

        while ($data = $query->fetch()) {
            if ($transform) {
                $data = $this->transform($data);
            }

            array_push($result, (object) $data);
        }

        return count($result) <= 1 && !empty($result) ? $result[0] : $result;
    }

    /**
     * Remove any potentially risky special characters
     * from the given string
     *
     * @param mixed $value Value to strip characters from
     * @return mixed
     */
    protected function escape($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        $replace_chars = ['<', '>', '!', '#', '\'', '`', '@', '(', ')', '%', '"', '{', '}'];

        return str_replace($replace_chars, '', $value);
    }

    /**
     * Transformer function called to transform query results 
     * of the model into a specific format
     *
     * @param array $data Query result to transform
     * @return mixed
     */
    protected function transform(array $data)
    {
        return $data;
    }
}