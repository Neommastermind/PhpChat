<?php
/**
 * Created by PhpStorm.
 * User: Matthew Jensen
 * Date: 12/3/2017
 * Time: 2:13 AM
 */

/*
 * Class created by IAmCaptainCode
 */
class DatabaseConnection
{
    private static $instance = null;
    private static $host = "localhost";
    private static $dbname = "W01234023";
    private static $user = "W01234023";
    private static $pass = "Matthewcs!";

    private function __construct()
    {
    }

    public static function getInstance(): \PDO
    {
        if (!static::$instance === null) {
            return static::$instance;
        } else {
            try {
                $connectionString = "mysql:host=".static::$host.";dbname=".static::$dbname;
                static::$instance = new \PDO($connectionString, static::$user, static::$pass);
                static::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                return static::$instance;
            } catch (PDOException $e) {
                echo "Unable to connect to the database: " . $e->getMessage();
                die();
            }
        }
    }
}