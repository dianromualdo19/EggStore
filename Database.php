<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

class Database
{
    private $host = "localhost";
    private $db   = "todolist";   // ✅ YOUR REAL DATABASE
    private $user = "root";
    private $pass = "";
    private $charset = "utf8mb4";

    public function connect()
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";
            $pdo = new PDO($dsn, $this->user, $this->pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("DATABASE ERROR: " . $e->getMessage());
        }
    }
}
