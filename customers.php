<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// 1. Logic: Add Customer
if (isset($_POST['add_customer'])) {
    $stmt = $db->prepare("INSERT INTO customers (name, email) VALUES (?, ?)");
    $stmt->execute([$_POST['name'], $_POST['email']]);
}

// 2. Logic: Delete Customer
if (isset($_GET['delete'])) {
    $stmt = $db->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: customers.php");
}

// 3. Logic: Get Customers
$customers = $db->query("SELECT * FROM customers ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Egg Depot | Customers</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Inter', sans-serif; }
        .sidebar { background-color: #242939; width: 250px; height: 100vh; position: fixed; color: white; padding: 20px; }
        .main-content { margin-left: 250px; padding: 30px; }
        .card-panel { background: white; border-radius: 15px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="sidebar">
    <h3 class="mb-5 text-center"><b>EGG DEPOT</b></h3>
    <nav class="nav flex-column">
        <a href="index.php" class="nav-link text-white"><i class="bi bi-grid-1x2-fill me-2"></i> Dashboard</a>
        <a href="customers.php" class="nav-link active text-warning"><i class="bi bi-people-fill me-2"></i> Customers</a>
        <a href="logout.php" class="nav-link text-danger mt-5"><i class="bi bi-power me-2"></i> Logout</a>
    </nav>
</div>

<div class="main-content">
    <div class="row">
        <div class="col-md-4">
            <div class="card-panel">
                <h5 class="fw-bold mb-3">Add New Customer</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <button type="submit" name="add_customer" class="btn btn-primary w-100">Add Customer</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card-panel">
                <h5 class="fw-bold mb-3">Customer List</h5>
                <table class="table table-hover">
                    <thead>
                        <tr><th>Name</th><th>Email</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $c): ?>
                        <tr>
                            <td><?php echo $c['name']; ?></td>
                            <td><?php echo $c['email']; ?></td>
                            <td>
                                <a href="customers.php?delete=<?php echo $c['id']; ?>" class="text-danger" onclick="return confirm('Remove this customer?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>