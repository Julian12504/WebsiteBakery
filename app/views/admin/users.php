<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý người dùng - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../public/css/css_admin/style.css">
    <link rel="stylesheet" href="../public/css/css_admin/import.css">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header"><i class="fa-solid fa-user-shield"></i> Administrator</div>
        <a href="admin.php?url=dashboard" class="menu-item"><i class="fa-solid fa-house"></i> Trang chủ Admin</a>

        <div class="menu-item" onclick="toggleProductMenu()" style="cursor: pointer;">
            <i class="fa-solid fa-cake-candles"></i>
            Quản lý sản phẩm
            <i class="fa-solid fa-chevron-down" id="arrow-icon" style="margin-left:auto; font-size: 10px; transition: 0.3s;"></i>
        </div>
        <div class="sub-menu" id="product-submenu">
            <a href="admin.php?url=categories" class="menu-item" style="padding-left: 40px; font-size: 13px;"><i class="fa-solid fa-list"></i> Danh mục</a>
            <a href="admin.php?url=products" class="menu-item" style="padding-left: 40px; font-size: 13px;"><i class="fa-solid fa-box"></i> Tất cả sản phẩm</a>
        </div>

        <a href="admin.php?url=orders" class="menu-item"><i class="fa-solid fa-cart-shopping"></i> Đơn hàng</a>
        <a href="admin.php?url=users" class="menu-item active"><i class="fa-solid fa-users"></i> Quản lý người dùng</a>
        <a href="admin.php?url=import_product" class="menu-item"><i class="fa-solid fa-truck-ramp-box"></i> Quản lý nhập hàng</a>
        <a href="admin.php?url=inventory" class="menu-item"><i class="fa-solid fa-boxes-stacked"></i> Tồn kho / Báo cáo</a>
        <a href="admin_logout.php" class="menu-item" style="color: #e74c3c;"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
    </div>

    <div class="main-content">
        <div class="content-padding">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; gap:20px; flex-wrap:wrap;">
                <div>
                    <h2>Quản lý người dùng</h2>
                    <p>Thêm tài khoản mới, reset mật khẩu và khoá/mở khoá tài khoản.</p>
                </div>
            </div>

            <?php if (!empty($msg)): ?>
                <div style="margin-bottom:20px; padding:15px 20px; border-radius:8px; background:#f0f9eb; color:#3d8b49; border:1px solid #c6ecd9;">
                    <?php
                    switch ($msg) {
                        case 'created':
                            echo 'Tạo tài khoản thành công.';
                            break;
                        case 'missing_fields':
                            echo 'Vui lòng điền đầy đủ thông tin tạo tài khoản.';
                            break;
                        case 'username_exists':
                            echo 'Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.';
                            break;
                        case 'email_exists':
                            echo 'Email đã tồn tại. Vui lòng kiểm tra lại.';
                            break;
                        case 'phone_exists':
                            echo 'Số điện thoại đã sử dụng.';
                            break;
                        case 'password_reset':
                            echo 'Reset mật khẩu thành công. Mật khẩu mới là 123456.';
                            break;
                        case 'locked':
                            echo 'Tài khoản đã bị khoá.';
                            break;
                        case 'unlocked':
                            echo 'Tài khoản đã được mở khoá.';
                            break;
                        case 'status_update_failed':
                            echo 'Cập nhật trạng thái tài khoản thất bại.';
                            break;
                        case 'password_reset_failed':
                            echo 'Reset mật khẩu thất bại, vui lòng thử lại.';
                            break;
                        case 'create_failed':
                            echo 'Tạo tài khoản thất bại, vui lòng thử lại.';
                            break;
                        case 'invalid_id':
                            echo 'ID người dùng không hợp lệ.';
                            break;
                        default:
                            echo htmlspecialchars($msg);
                            break;
                    }
                    ?>
                </div>
            <?php endif; ?>

            <div class="card" style="background:white; padding:20px; margin-bottom:20px; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.05);">
                <h3 style="margin-top:0;">Thêm tài khoản mới</h3>
                <form action="admin.php?url=add_user" method="POST" style="display:grid; gap:15px;">
                    <div style="display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:15px;">
                        <div>
                            <label>Họ và tên</label>
                            <input type="text" name="full_name" class="form-control" placeholder="Nguyễn Văn A" required>
                        </div>
                        <div>
                            <label>Tên đăng nhập</label>
                            <input type="text" name="username" class="form-control" placeholder="username" required>
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:15px;">
                        <div>
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" placeholder="email@example.com" required>
                        </div>
                        <div>
                            <label>Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" placeholder="090xxxxxxx" required>
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:15px;">
                        <div>
                            <label>Mật khẩu</label>
                            <input type="password" name="password" class="form-control" placeholder="123456" required>
                        </div>
                        <div>
                            <label>Vai trò</label>
                            <select name="role" class="form-control" style="padding:10px; border:1px solid #ddd; border-radius:6px;">
                                <option value="0">Khách hàng</option>
                                <option value="1">Admin</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn-submit" style="width:180px;">Tạo tài khoản</button>
                </form>
            </div>

            <div class="card" style="background:white; padding:20px; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.05);">
                <h3 style="margin-top:0;">Danh sách người dùng</h3>
                <div style="overflow-x:auto;">
                    <table class="admin-table" style="width:100%; min-width:900px; margin-top:15px;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Họ và tên</th>
                                <th>Tên đăng nhập</th>
                                <th>Email</th>
                                <th>Điện thoại</th>
                                <th>Vai trò</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $index => $user): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($user['full_name']) ?></td>
                                        <td><?= htmlspecialchars($user['username']) ?></td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td><?= htmlspecialchars($user['phone']) ?></td>
                                        <td><?= $user['role'] == 1 ? 'Admin' : 'Khách hàng' ?></td>
                                        <td>
                                            <span class="badge" style="background: <?= $user['status'] == 1 ? '#e1f3dd' : '#fde2e1' ?>; color: <?= $user['status'] == 1 ? '#2f7a38' : '#b02a2a' ?>;">
                                                <?= $user['status'] == 1 ? 'Hoạt động' : 'Khoá' ?>
                                            </span>
                                        </td>
                                        <td style="display:flex; gap:10px; flex-wrap:wrap;">
                                            <a href="admin.php?url=reset_password&id=<?= $user['id'] ?>" class="btn-submit" style="padding:7px 12px; background:#3498db;" onclick="return confirm('Reset mật khẩu cho <?= addslashes(htmlspecialchars($user['username'])) ?>?')">
                                                <i class="fa-solid fa-key"></i>&nbsp;Reset
                                            </a>
                                            <a href="admin.php?url=toggle_user_status&id=<?= $user['id'] ?>" class="btn-cancel" style="padding:7px 12px;" onclick="return confirm('Bạn có chắc muốn <?= $user['status'] == 1 ? 'khoá' : 'mở khoá' ?> tài khoản này?')">
                                                <i class="fa-solid fa-lock<?= $user['status'] == 1 ? '' : '-open' ?>"></i>&nbsp;<?= $user['status'] == 1 ? 'Khoá' : 'Mở' ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" style="text-align:center; padding:20px; color:#888;">Chưa có người dùng nào.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<script>
function toggleProductMenu() {
    const submenu = document.getElementById('product-submenu');
    const arrow = document.getElementById('arrow-icon');
    submenu.classList.toggle('show');
    arrow.classList.toggle('rotate');
}
</script>
</body>
</html>
