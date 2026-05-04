<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === 'Admin' && $password === 'admin123') {
        $_SESSION['username'] = 'Admin';
        $_SESSION['role'] = 'admin'; // <--- Eto ang susi para sa Admin
        header("Location: index.php");
        exit;
    } 
    else if ($username === 'Staff1' && $password === 'staff123') {
        $_SESSION['username'] = 'Staff1';
        $_SESSION['role'] = 'staff'; // <--- Eto ang susi para sa Staff
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css">
</head>
<body style="background:#242939; background-image:url('css/egg.jfif');background-size: cover; display:flex; align-items:center; justify-content:center; height:100vh;">
    <div style="background:white; padding:30px; border-radius:10px; width:350px;">
        <h2 class="text-center">Egg Store</h2>
        <form method="POST">
            <input type="text" name="username" class="form-control mb-2" placeholder="Username" required>
            <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</body>
</html>