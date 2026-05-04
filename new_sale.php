<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

session_start();
require_once 'db.php';
require_once 'Inventory.php';

$database = new Database();
$db = $database->getConnection();
$inventory = new Inventory($db);
$eggs = $inventory->getAllEggs();


if (isset($sale_saved_to_db) && $sale_saved_to_db) {
    $customers = $db->query("SELECT email FROM customers WHERE receive_updates = 1")->fetchAll(PDO::FETCH_ASSOC);
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP(); 
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'mceyy1902@gmail.com'; 
        $mail->Password = 'ghwe gptn fced wjga';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
        $mail->Port = 587;
        $mail->setFrom('mceyy1902@gmail.com', 'Egg Depot System');

        foreach ($customers as $customer) {
            $mail->clearAddresses(); 
            $mail->addAddress($customer['email']);
            $mail->isHTML(true); 
            $mail->Subject = 'New Stock Level';
            $mail->Body = "Update! The stock for <b>$egg_type</b> has changed due to a new sale.";
            $mail->send();
        }
    } catch (Exception $e) { 
    }

    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Egg Depot | New Sale</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f6f9; margin: 0; font-family: 'Inter', sans-serif; display: flex; }
        .sidebar { background-color: #242939; width: 250px; height: 100vh; position: fixed; color: white; padding: 20px; }
        .main-content { margin-left: 250px; padding: 30px; width: 100%; }
        .sale-card { background: white; border-radius: 15px; padding: 30px; max-width: 500px; margin: 50px auto; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .nav-link { color: #8e94a9; margin-bottom: 10px; text-decoration: none; display: block; }
        .nav-link.active { color: #ff8c00; font-weight: bold; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3 class="mb-5 text-center"><b>EGG DEPOT</b></h3>
    <nav class="nav flex-column">
        <a href="index.php" class="nav-link active"><i class="bi bi-grid-1x2-fill me-2"></i> Dashboard</a>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="new_sale.php" class="nav-link"><i class="bi bi-cash-register me-2"></i> New Sale</a>
            <a href="restock.php" class="nav-link"><i class="bi bi-plus-circle me-2"></i> Restock Stock</a>
        <?php endif; ?>
        <a href="logout.php" class="nav-link text-warning mt-5"><i class="bi bi-power me-2"></i> Logout</a>
    </nav>
</div>

<div class="main-content">
    <div class="sale-card">
        <h4 class="fw-bold mb-4 text-center">Record New Sale</h4>
        <form id="saleForm">
            <div class="mb-3">
                <label class="fw-bold mb-1 small">Egg Product</label>
                <select id="egg_id" class="form-select" required>
                    <option value="">-- Select Product --</option>
                    <?php foreach ($eggs as $egg): ?>
                        <option value="<?php echo $egg['id']; ?>">
                            <?php echo $egg['type']; ?> (<?php echo $egg['size']; ?>) - Stock: <?php echo $egg['trays']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="fw-bold mb-1 small">Trays to Sell</label>
                <input type="number" id="trays_sold" class="form-control" min="1" placeholder="0" required>
            </div>
            <div class="mb-4">
                <label class="fw-bold mb-1 small">Price per Tray (₱)</label>
                <input type="number" id="price_per_tray" class="form-control" step="0.01" placeholder="0.00" required>
            </div>
            <button type="submit" class="btn btn-warning w-100 fw-bold py-2 shadow-sm">Complete Sale</button>
            <a href="index.php" class="btn btn-link w-100 mt-2 text-muted text-decoration-none small">Cancel</a>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $('#saleForm').on('submit', function(e) {
        e.preventDefault();
        
        const saleData = {
            egg_id: $('#egg_id').val(),
            trays: $('#trays_sold').val(),
            price: $('#price_per_tray').val()
        };

        $.post('process_sale.php', saleData, function(res) {
            if(res.trim() === "success") {
                alert("Sale recorded successfully!");
                window.location.href = "index.php";
            } else if(res.trim() === "insufficient_stock") {
                alert("Error: Not enough stock in inventory!");
            } else {
                alert("Error: " + res);
            }
        });
    });
</script>
</body>
</html>
