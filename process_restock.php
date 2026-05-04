<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

require_once 'db.php';
require_once 'Sales.php';

$database = new Database();
$db = $database->getConnection();
$sales = new Sales($db);

if (isset($_POST['egg_id'])) {
    $egg_id = $_POST['egg_id'];
    $trays = $_POST['trays'];
    $cost = $_POST['cost'];

    $result = $sales->recordPurchase($egg_id, $trays, $cost);

    if ($result == "success") {
        try {
            $stmt = $db->prepare("SELECT type FROM eggs WHERE id = ?");
            $stmt->execute([$egg_id]);
            $egg_data = $stmt->fetch(PDO::FETCH_ASSOC);
            $product_name = $egg_data ? $egg_data['type'] : "the restocked product";

            $customers = $db->query("SELECT email FROM customers WHERE receive_updates = 1")->fetchAll(PDO::FETCH_ASSOC);
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mceyy1902@gmail.com';
            $mail->Password = 'ghwe gptn fced wjga'; // App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('mceyy1902@gmail.com', 'Egg Depot System');

            foreach ($customers as $customer) {
                $mail->clearAddresses();
                $mail->addAddress($customer['email']);
                $mail->isHTML(true);
                $mail->Subject = 'New Restock Alert!';
            
                $mail->Body = "Good news! We have restocked: <b>" . htmlspecialchars($product_name) . "</b>. You can now place your orders.";
                $mail->send();
            }
        } catch (Exception $e) { 
        }
    }

    echo $result;
}
?>
