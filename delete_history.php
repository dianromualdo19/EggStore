<?php
require_once 'db.php';

$database = new Database();
$db = $database->getConnection();

// This deletes everything from your stock_history table
$query = "DELETE FROM stock_history";
$stmt = $db->prepare($query);

if($stmt->execute()) {
    echo "success";
} else {
    echo "error";
}
?>