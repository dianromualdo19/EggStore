<?php
session_start();
require_once 'db.php';
require_once 'Inventory.php';

$database = new Database();
$db = $database->getConnection();
$inventory = new Inventory($db);
$eggs = $inventory->getAllEggs();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Egg Depot | Restock Inventory</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f6f9; margin: 0; display: flex; font-family: 'Inter', sans-serif; }
        .sidebar { background-color: #242939; width: 250px; height: 100vh; position: fixed; color: white; padding: 20px; }
        .main-content { margin-left: 250px; padding: 30px; width: 100%; }
        .restock-card { background: white; border-radius: 15px; padding: 30px; max-width: 500px; margin: 50px auto; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
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
    <div class="restock-card">
        <h4 class="fw-bold mb-4 text-center">Restock / Buy Eggs</h4>
        <p class="text-muted small text-center mb-4">This will deduct from <b>Today's Cash</b> and add to <b>Inventory</b>.</p>
        
        <form id="restockForm">
            <div class="mb-3">
                <label class="fw-bold mb-1 small">Select Egg to Restock</label>
                <select id="egg_id" class="form-select" required>
                    <option value="">-- Choose Type --</option>
                    <?php foreach ($eggs as $egg): ?>
                        <option value="<?php echo $egg['id']; ?>">
                            <?php echo $egg['type']; ?> (<?php echo $egg['size']; ?>) - Current: <?php echo $egg['trays']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="fw-bold mb-1 small">New Trays Purchased</label>
                <input type="number" id="trays_bought" class="form-control" min="1" placeholder="How many trays?" required>
            </div>
            <div class="mb-4">
                <label class="fw-bold mb-1 small">Cost per Tray (₱)</label>
                <input type="number" id="cost_per_tray" class="form-control" step="0.01" placeholder="0.00" required>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">Confirm Purchase</button>
            <a href="index.php" class="btn btn-link w-100 mt-2 text-muted text-decoration-none small">Back to Dashboard</a>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $('#restockForm').on('submit', function(e) {
        e.preventDefault();
        
        const restockData = {
            egg_id: $('#egg_id').val(),
            trays: $('#trays_bought').val(),
            cost: $('#cost_per_tray').val()
        };

        if(confirm("Confirm spending Today's Cash for this restock?")) {
            $.post('process_restock.php', restockData, function(res) {
                if(res.trim() === "success") {
                    alert("Stock Updated and Cash Deducted!");
                    window.location.href = "index.php";
                } else {
                    alert("Error: " + res);
                }
            });
        }
    });
</script>
</body>
</html>