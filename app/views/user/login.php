<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Sweet Home</title>
    <link rel="stylesheet" href="css/login.css"> <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="login-body">

    <div class="login-container">
        <div class="login-box">
            <h2>KHOẢNG KHẮC NGỌT NGÀO</h2>
            <p>Đăng nhập để quản lý đơn hàng.</p>
            
            <form action="index.php?url=checklogin" method="POST">
    <div class="input-group">
        <label>Tên đăng nhập:</label>
        <input type="text" name="username" required>
    </div>
    <div class="input-group">
        <label>Mật khẩu:</label>
        <input type="password" name="password" required>
    </div>
    <button type="submit" class="btn-login">Đăng Nhập</button>
</form>
            
            <div class="login-footer">
                <p>Chưa có tài khoản? <a href="index.php?url=register">Đăng Ký Ngay</a></p>
                <a href="index.php?url=home" class="back-home">← Quay về Trang Chủ</a>
            </div>
        </div>
    </div>

</body>
</html>