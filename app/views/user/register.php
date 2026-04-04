<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Sweet Home</title>
 <link rel="stylesheet" href="css/login.css"> <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="login-body">

    <div class="login-container">
        <div class="login-box">
            <h2>GIA NHẬP NHÀ NGỌT</h2>
            <p>Tạo tài khoản để nhận nhiều ưu đãi hấp dẫn.</p>
            
            <?php if(isset($error)): ?>
                <div style="background: #fff5f5; color: #e53e3e; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 14px; border: 1px solid #feb2b2;">
                    <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form action="index.php?url=register" method="POST">
                <div class="input-group">
                    <label>Họ và tên:</label>
                    <input type="text" name="fullname" placeholder="Nguyễn Văn A" 
                           value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>" required>
                </div>

                <div class="input-group">
                    <label>Email:</label>
                    <input type="email" name="email" placeholder="example@email.com" 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                </div>

                <div class="input-group">
                    <label>Số điện thoại:</label>
                    <input type="text" name="phone" placeholder="090xxxxxxx" 
                           value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" required>
                </div>
                <div class="input-group">
    <label>Tên đăng nhập:</label>
    <input type="text" name="username" placeholder="Ví dụ: khachhang123" required>
</div>
                <div class="input-group">
                    <label>Mật khẩu:</label>
                    <input type="password" name="password" placeholder="********" required>
                </div>

                <div class="input-group">
                    <label>Nhập lại mật khẩu:</label>
                    <input type="password" name="confirm_password" placeholder="********" required>
                </div>
                
                <button type="submit" class="btn-login">Đăng Ký</button>
            </form>
            
            <div class="login-footer">
                <p>Đã có tài khoản? <a href="index.php?url=login">Đăng Nhập Ngay</a></p>
                <a href="index.php?url=home" class="back-home">← Quay về Trang Chủ</a>
            </div>
        </div>
    </div>

</body>
</html>