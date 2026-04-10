<?php
session_start();
require_once '../config/database.php';
require_once '../app/models/User.php';

$db = (new Database())->getConnection();
$userModel = new User($db);
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = $userModel->login($username, $password);

    // Kiểm tra: Phải đúng user và role phải là 1 (Admin)
    if ($user && $user['role'] == 1) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_name'] = $user['full_name'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        
        // Đăng nhập xong đẩy qua admin.php
        header("Location: admin.php?url=dashboard");
        exit();
    } else {
        $error = "Sai tài khoản hoặc bạn không có quyền quản trị!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hệ Thống Quản Trị - Sweet Home</title>
    <link rel="stylesheet" href="css/admin_style.css"> <style>
        body { background: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; font-family: sans-serif; }
        .login-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); width: 350px; }
        .login-box h2 { color: #d81b60; text-align: center; margin-bottom: 25px; }
        .input-group { margin-bottom: 15px; }
        .input-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .btn-login { width: 100%; background: #d81b60; color: white; border: none; padding: 12px; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .error { color: red; font-size: 13px; text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>ADMIN LOGIN</h2>
        <?php if($error) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <div class="input-group">
                <input type="text" name="username" placeholder="Tên đăng nhập" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Mật khẩu" required>
            </div>
            <button type="submit" class="btn-login">ĐĂNG NHẬP HỆ THỐNG</button>
        </form>
    </div>
</body>
</html>