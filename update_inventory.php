<?php
include 'db.php';

if (isset($_POST['id']) && isset($_POST['qty']) && isset($_POST['status'])) {
    $id = $_POST['id'];
    $qty = $_POST['qty'];
    $status = $_POST['status'];


    $sql = "UPDATE inventory SET total_trays = ?, manual_status = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$qty, $status, $id])) {
        echo "Success";
    } else {
        echo "Error updating record";
    }
}
?>