<?php
namespace Models;
use Config\Database;
class Model{

    protected $db = null;

    protected function getDb()
    {
        if ($this->db === null) {
            $database = new Database();
            $this->db = $database->connect();
        }
        return $this->db;
    }
    // This is a base model class. You can add common database methods here.

}