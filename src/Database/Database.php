<?php

namespace App\Database;

use PDO;
use PDOException;

class Database {
    private $host = 'localhost';
    private $dbname = 'ecommerce';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbname, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            error_log('Connection error: ' . $exception->getMessage());
            throw new \Exception("Connection error: " . $exception->getMessage());
        }

        return $this->conn;
    }
}