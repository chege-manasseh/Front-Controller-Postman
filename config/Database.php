<?php
namespace Config;

use PDO;
use PDOException;

class Database
{
    private static $instance = null; 

    public function connect()
    {
        if (self::$instance === null) {
            $host = $_ENV['DB_HOST'];
            $dbname = $_ENV['DB_NAME'];
            $user = $_ENV['DB_USER'];
            $pass = $_ENV['DB_PASS'];

            try {
                $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
                self::$instance = new \PDO($dsn, $user, $pass);
                self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
