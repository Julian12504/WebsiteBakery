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
            
            <form action="index.php?url=handle_register" method="POST">
                <div class="input-group">
                    <label>Họ và tên:</label>
                    <input type="text" name="fullname" placeholder="Nguyễn Văn A" required>
                </div>

                <div class="input-group">
                    <label>Email:</label>
                    <input type="email" name="email" placeholder="example@email.com" required>
                </div>
                
                <div class="input-group">
                    <label>Mật khẩu:</label>
                    <input type="password" name="password" required>
                </div>

                <div class="input-group">
                    <label>Nhập lại mật khẩu:</label>
                    <input type="password" name="confirm_password" required>
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