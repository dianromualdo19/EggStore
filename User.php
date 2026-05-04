<?php
class User {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

   public function login($username, $password) {
    // 1. Clean the input
    $user = trim($username);
    
    // 2. Use a direct query to see exactly what the DB sees
    $sql = "SELECT * FROM users WHERE username = '$user' LIMIT 1";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // 3. Temporary Plain Text Check (Matches your 'admin123' SQL insert)
        if ($password === $row['password']) {
            return $row;
        }
    }
    return false;
}
    }
?>