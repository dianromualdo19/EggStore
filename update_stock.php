<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

session_start();
require_once 'db.php';

if (isset($_GET['id']) && isset($_GET['trays'])) {
    $id = $_GET['id'];
    $new_trays = (int)$_GET['trays'];
    $status = ($new_trays <= 10) ? 'LOW' : 'GOOD';

    $database = new Database();
    $db = $database->getConnection();

    // 1. Update DB
    $query = "UPDATE eggs SET trays = :trays, status = :status WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->execute([':trays' => $new_trays, ':status' => $status, ':id' => $id]);

    // 2. Notification Logic (Insert this block here)
    $type_query = $db->prepare("SELECT type FROM eggs WHERE id = :id");
    $type_query->execute(['id' => $id]);
    $egg_data = $type_query->fetch(PDO::FETCH_ASSOC);
    $egg_type = $egg_data['type'];

    $customers = $db->query("SELECT email FROM customers WHERE receive_updates = 1")->fetchAll(PDO::FETCH_ASSOC);
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP(); $mail->Host = 'smtp.gmail.com'; $mail->SMTPAuth = true;
        $mail->Username = 'mceyy1902@gmail.com'; $mail->Password = 'ghwe gptn fced wjga';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; $mail->Port = 587;
        $mail->setFrom('mceyy1902@gmail.com', 'Egg Depot System');

        foreach ($customers as $customer) {
            $mail->clearAddresses(); $mail->addAddress($customer['email']);
            $mail->isHTML(true); $mail->Subject = 'Stock Update: ' . $egg_type;
            $mail->Body = "Stock updated! <b>$egg_type</b> is now <b>$new_trays trays</b>.";
            $mail->send();
        }
    } catch (Exception $e) { /* Error ignored for simplicity */ }

    $_SESSION['message'] = "Stock updated!";
    header("Location: index.php");
    exit();
}
?>