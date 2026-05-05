<?php
namespace Config;

use PDO;
use PDOException;

class Database{
    private $conn;

    public function connect(){
        $host=$_ENV['DB_HOST'];
        $dbname=$_ENV['DB_NAME'];
        $user=$_ENV['DB_USER'];
        $pass=$_ENV['DB_PASS'];

        $dsn="mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        try{
            $this->conn=new PDO($dsn,$user,$pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }
}