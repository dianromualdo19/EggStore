<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

function sendStockNotification($db, $egg_type, $new_trays) {
    // 1. Get the customer list
    $stmt = $db->prepare("SELECT email FROM customers WHERE receive_updates = 1");
    $stmt->execute();
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Setup Mailer
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'mceyy1902@gmail.com'; 
    $mail->Password   = 'ghwe gptn fced wjga'; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->setFrom('mceyy1902@gmail.com', 'Egg Depot System');

    // 3. Send
    foreach ($customers as $customer) {
        $mail->clearAddresses();
        $mail->addAddress($customer['email']);
        $mail->isHTML(true);
        $mail->Subject = 'Stock Update: ' . $egg_type;
        $mail->Body    = "Stock update! The <b>$egg_type</b> is now at <b>$new_trays trays</b>.";
        $mail->send();
    }
}
?>