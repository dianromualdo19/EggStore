<?php
session_start();
require_once 'db.php';
require_once 'Inventory.php';
require_once 'Sales.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection(); 

$inventory = new Inventory($db);
$sales = new Sales($db);

$eggs = $inventory->getAllEggs();
$logs = $inventory->getHistory(5);
$todays_cash = $sales->getTodaysTotal();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Egg Depot | Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    body { background-color: #f4f6f9; margin: 0; font-family: 'Inter', sans-serif; }
    .sidebar { background-color: #242939; width: 250px; height: 100vh; position: fixed; color: white; padding: 20px; }
    .main-content { margin-left: 250px; padding: 30px; min-width: 0; }
    .stat-card { border-radius: 15px; border: none; background: white; padding: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .card-panel { background: white; border-radius: 15px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); height: 100%; }
    .nav-link { color: #8e94a9; margin-bottom: 10px; text-decoration: none; display: block; }
    .nav-link.active { color: #ff8c00; font-weight: bold; }
    .nav-link:hover { color: white; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3 class="mb-5 text-center"><b>EGG DEPOT</b></h3>
    <nav class="nav flex-column">
        <a href="index.php" class="nav-link active"><i class="bi bi-grid-1x2-fill me-2"></i> Dashboard</a>
        
        <a href="customers.php" class="nav-link"><i class="bi bi-people-fill me-2"></i> Customers</a>

        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="new_sale.php" class="nav-link"><i class="bi bi-cash-register me-2"></i> New Sale</a>
            <a href="restock.php" class="nav-link"><i class="bi bi-plus-circle me-2"></i> Restock Stock</a>
        <?php endif; ?>
        <a href="export_csv.php" class="btn btn-success">
    <i class="bi bi-file-earmark-excel"></i> Export to CSV
</a>
        <a href="logout.php" class="nav-link text-warning mt-5"><i class="bi bi-power me-2"></i> Logout</a>
    </nav>
</div>
<div class="main-content">
    <?php if(isset($_SESSION['message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['message']); // Clear the message so it doesn't show on refresh ?>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['error']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card border-bottom border-warning border-5">
                <small class="text-muted fw-bold">Today's Cash</small>
                <h2 class="m-0 mt-1">₱<?php echo number_format($todays_cash, 2); ?></h2>
            </div>
        </div>
        <div class="col-md-4"><div class="stat-card border-bottom border-info border-5"><small class="text-muted fw-bold">Outstanding Utang</small><h2 class="m-0 mt-1">₱0.00</h2></div></div>
        <div class="col-md-4"><div class="stat-card border-bottom border-primary border-5"><small class="text-muted fw-bold">Egg Products</small><h2 class="m-0 mt-1"><?php echo count($eggs); ?></h2></div></div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-8"><div class="card-panel"><h5 class="fw-bold mb-3">Stock Overview</h5><div style="height: 350px;"><canvas id="liveEggChart"></canvas></div></div></div>
        <div class="col-lg-4">
            <div class="card-panel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold m-0">History</h5>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <button id="clearHistory" class="btn btn-danger btn-sm">Clear</button>
                    <?php endif; ?>
                </div>
                <div style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-sm small">
                        <tbody>
                            <?php if(empty($logs)): ?>
                                <tr><td class="text-center py-5 text-muted">No recent changes found.</td></tr>
                            <?php else: foreach ($logs as $log): ?>
                                <tr>
                                    <td><b><?php echo $log['egg_type']; ?></b><br><small class="text-muted"><?php echo date('M d, g:i A', strtotime($log['change_date'])); ?></small></td>
                                    <td class="text-end align-middle"><?php echo $log['old_trays']; ?> → <b><?php echo $log['new_trays']; ?></b></td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card-panel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold m-0">Inventory List</h5>
                    <div class="d-flex align-items-center gap-2">
                        <?php if ($_SESSION['role'] !== 'admin'): ?>
                            <span class="badge bg-secondary">View Only Mode</span>
                        <?php endif; ?>
                        <select id="typeFilter" class="form-select form-select-sm" style="width: 200px;">
                            <option value="all">-- Show All Eggs --</option>
                            <option value="Quail">Quail</option>
                            <option value="Salted">Salted</option>
                            <option value="Soft-shelled">Soft-shelled</option>
                            <option value="White">White</option>
                        </select>
                    </div>
                </div>
                <table class="table align-middle" id="inventoryTable">
                    <thead class="table-light">
                        <thead>
                        <tr>
                            <th>Type</th>
                            <th>Size</th>
                            <th>Trays</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($eggs as $row): 
                            // Automatic Logic: Kung 5 trays pababa, automatic na magiging LOW
                            $isLow = ($row['trays'] <= 5);
                            $statusText = $isLow ? 'LOW' : 'GOOD';
                            $badgeClass = $isLow ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success';
                        ?>
                        <tr class="egg-row" data-type="<?php echo $row['type']; ?>">
                            <td><b><?php echo $row['type']; ?></b></td>
                            <td class="egg-size"><?php echo $row['size']; ?></td>
                            <td class="tray-count"><?php echo $row['trays']; ?></td>
                            <td>
                                <span class="badge <?php echo $badgeClass; ?> p-2 px-3">
                                    <?php echo $statusText; ?>
                                </span>
                            </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update Stock</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="update_stock.php" method="GET">
          <div class="modal-body">
            <input type="hidden" name="id" id="edit-id">
            <label class="form-label">Enter new tray count:</label>
            <input type="number" name="trays" id="edit-trays" class="form-control" required>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // 1. Filter Logic
    $('#typeFilter').on('change', function() {
        const selected = $(this).val();
        $('.egg-row').each(function() {
            const type = $(this).data('type');
            (selected === 'all' || type === selected) ? $(this).show() : $(this).hide();
        });
    });

    // 2. Modal Data
    $('.edit-btn').on('click', function() {
        $('#edit-id').val($(this).data('id'));
        $('#edit-trays').val($(this).data('trays'));
    });

    // 3. Chart - FIXED Labels (S, M, L etc) and Colors
    const ctx = document.getElementById('liveEggChart').getContext('2d');
    const labels = [];
    const chartData = [];
    
    $('#inventoryTable tbody tr').each(function() {
        let type = $(this).find('td:first').text().trim();
        let size = $(this).find('.egg-size').text().trim();
        
        // Show Size if it's "White" egg, otherwise show Type
        labels.push(type === 'White' ? size : type);
        chartData.push(parseInt($(this).find('.tray-count').text()));
    });

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Trays',
                data: chartData,
                backgroundColor: ['#fdcb6e','#00cec9','#6c5ce7','#0984e3','#00b894','#fab1a0','#d63031','#ff7675'],
                borderRadius: 10
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false, 
            plugins: { legend: { display: false } } 
        }
    });

    // 4. Clear History
    $('#clearHistory').on('click', function() {
        if(confirm("Clear history?")) {
            $.post('delete_history.php', function(res) { if(res.trim()==="success") window.location.reload(); });
        }
    });
});
</script>
</body>
</html>