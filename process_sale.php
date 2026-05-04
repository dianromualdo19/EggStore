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

$result = "error"; 

if (isset($_POST['egg_id'])) {
    $result = $sales->recordSale($_POST['egg_id'], $_POST['trays'], $_POST['price']);

    if ($result == "success") {
        try {
            
            $stmt = $db->prepare("SELECT type, trays FROM eggs WHERE id = ?");
            $stmt->execute([$_POST['egg_id']]);
            $egg_info = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $threshold = 5; // Dito mo itatakda kung kailan ka ma-aalerto
            $current_stock = $egg_info['trays'];
            $product_name = $egg_info['type'];

        
            if ($current_stock <= $threshold) {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'mceyy1902@gmail.com';
                $mail->Password = 'ghwe gptn fced wjga'; // App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->setFrom('mceyy1902@gmail.com', 'Egg Depot System');
                $mail->addAddress('mceyy1902@gmail.com'); // Ipadala sa sarili mong email

                $mail->isHTML(true);
                
                if ($current_stock <= 0) {
                    $mail->Subject = '! OUT OF STOCK ALERT';
                    $mail->Body = "Alert: The <b>" . $product_name . "</b> is out of stock. Please restock as soon as possible.";
                } else {
                    $mail->Subject = '! Low Stock Alert';
                    $mail->Body = "Reminders: The <b>" . $product_name . "</b> has only <b>" . $current_stock . "</b> trays left. The stock is running low.";
                }
                
                $mail->send();
            }
        } catch (Exception $e) { 
        }
    }
}

echo $result;
?>
