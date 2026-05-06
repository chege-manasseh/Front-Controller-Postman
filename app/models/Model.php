<?php
namespace Models;
use Config\Database;
class Model{

    protected $db;
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    } 
    // This is a base model class. You can add common database methods here.

}