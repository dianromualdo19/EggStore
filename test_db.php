<?php
require_once 'db.php';
$database = new Database();
$db = $database->getConnection();

$stmt = $db->query("SELECT * FROM users WHERE username = 'Admin'");
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "<h1>ERROR: User 'Admin' not found in the table!</h1>";
    
    // Let's see what IS in the table
    echo "<h3>Current users in table:</h3>";
    $all = $db->query("SELECT username FROM users")->fetchAll(PDO::FETCH_COLUMN);
    if (empty($all)) {
        echo "The table is empty.";
    } else {
        echo implode(", ", $all);
    }
} else {
    echo "<h1>SUCCESS: User found!</h1>";
    echo "Username: " . $user['username'] . "<br>";
    echo "Password in DB: " . $user['password'];
}
?>