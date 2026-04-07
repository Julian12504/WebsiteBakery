<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý đơn hàng - Sweet Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../public/css/css_admin/style.css">
    <link rel="stylesheet" href="../public/css/css_admin/order.css"> 
</head>
<body>

  <div class="sidebar">
    <div class="sidebar-header"><i class="fa-solid fa-user-shield"></i> Administrator</div>
    
    <a href="admin.php?url=dashboard" class="menu-item active"><i class="fa-solid fa-house"></i> Trang chủ Admin</a>

    <div class="menu-item" onclick="toggleProductMenu()" style="cursor: pointer;">
        <i class="fa-solid fa-cake-candles"></i> 
        Quản lý sản phẩm 
        <i class="fa-solid fa-chevron-down" id="arrow-icon" style="margin-left:auto; font-size: 10px; transition: 0.3s;"></i>
    </div>
    
    <div class="sub-menu" id="product-submenu">
        <a href="admin.php?url=categories" class="menu-item" style="padding-left: 40px; font-size: 13px;">
            <i class="fa-solid fa-list"></i> Danh mục
        </a>
        <a href="admin.php?url=products" class="menu-item" style="padding-left: 40px; font-size: 13px;">
            <i class="fa-solid fa-box"></i> Tất cả sản phẩm
        </a>
    </div>

    <a href="admin.php?url=orders" class="menu-item"><i class="fa-solid fa-cart-shopping"></i> Đơn hàng</a>
    <a href="admin.php?url=users" class="menu-item"><i class="fa-solid fa-users"></i> Quản lý người dùng</a>
    <a href="admin.php?url=import_product" class="menu-item">
    <i class="fa-solid fa-truck-ramp-box"></i> Quản lý nhập hàng
</a>
    <a href="admin.php?url=inventory" class="menu-item"><i class="fa-solid fa-boxes-stacked"></i> Tồn kho / Báo cáo</a>
    <a href="admin_logout.php" class="menu-item" style="color: #e74c3c;"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
</div>

<div class="main-content">
    <div class="top-nav">
        <div>Chào, <?= $_SESSION['admin_name'] ?? 'Admin' ?></div>
        <div><a href="index.php"><i class="fa-solid fa-globe"></i> Xem trang chủ</a></div>
    </div>

    <div class="content-padding">
        <h2>Quản Lý Đơn Hàng</h2>

        <div class="toolbar" style="background: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <form action="admin.php" method="GET" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                <input type="hidden" name="url" value="orders">
                
                <div style="display: flex; align-items: center; gap: 5px; font-size: 14px;">
                    <label>Từ:</label>
                    <input type="date" name="from_date" value="<?= $_GET['from_date'] ?? '' ?>" style="padding: 6px; border: 1px solid #ddd; border-radius: 4px;">
                </div>

                <div style="display: flex; align-items: center; gap: 5px; font-size: 14px;">
                    <label>Đến:</label>
                    <input type="date" name="to_date" value="<?= $_GET['to_date'] ?? '' ?>" style="padding: 6px; border: 1px solid #ddd; border-radius: 4px;">
                </div>

                <select name="status" style="padding: 7px; border: 1px solid #ddd; border-radius: 4px; min-width: 150px;">
                    <option value="">Tất cả trạng thái</option>
                    <option value="0" <?= (isset($_GET['status']) && $_GET['status'] == '0') ? 'selected' : '' ?>>Chưa xử lý</option>
                    <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : '' ?>>Đã xác nhận</option>
                    <option value="2" <?= (isset($_GET['status']) && $_GET['status'] == '2') ? 'selected' : '' ?>>Đã giao thành công</option>
                    <option value="4" <?= (isset($_GET['status']) && $_GET['status'] == '4') ? 'selected' : '' ?>>Đã hủy</option>
                </select>

                <button type="submit" style="background: #3498db; color: white; border: none; padding: 8px 20px; border-radius: 4px; cursor: pointer;">
                    <i class="fa-solid fa-filter"></i> Lọc
                </button>

                <a href="admin.php?url=orders" style="text-decoration: none; color: #666; font-size: 14px;"><i class="fa-solid fa-rotate-left"></i> Làm mới</a>
            </form>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Khách hàng</th>
                        <th>Địa chỉ giao</th>
                        <th>Tổng tiền</th>
                        <th style="width: 200px;">Trạng thái</th>
                        <th style="width: 120px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($orders)): foreach($orders as $o): ?>
                    <tr>
                        <td style="font-weight: bold;">#<?= $o['id'] ?></td>
                        <td><strong><?= htmlspecialchars($o['full_name']) ?></strong></td>
                        <td style="font-size: 13px; color: #666;"><?= htmlspecialchars($o['shipping_address']) ?></td>
                        <td style="color: #e74c3c; font-weight: bold;"><?= number_format($o['total_amount']) ?>đ</td>
                        <td>
                            <form action="admin.php?url=update_order_status" method="POST">
                                <input type="hidden" name="id" value="<?= $o['id'] ?>">
                                <select name="status" onchange="if(confirm('Cập nhật trạng thái đơn hàng?')) this.form.submit()" 
                                    style="padding: 5px; border-radius: 4px; border: 1px solid #ccc; font-size: 13px; width: 100%; cursor: pointer;
                                    <?= $o['status']==0 ? 'border-left: 4px solid #f39c12;' : '' ?>
                                    <?= $o['status']==1 ? 'border-left: 4px solid #3498db;' : '' ?>
                                    <?= $o['status']==2 ? 'border-left: 4px solid #27ae60;' : '' ?>
                                    <?= $o['status']==4 ? 'border-left: 4px solid #e74c3c;' : '' ?> ">
                                    <option value="0" <?= $o['status']==0 ? 'selected':'' ?>>🟠 Chưa xử lý</option>
                                    <option value="1" <?= $o['status']==1 ? 'selected':'' ?>>🔵 Đã xác nhận</option>
                                    <option value="2" <?= $o['status']==2 ? 'selected':'' ?>>🟢 Đã giao</option>
                                    <option value="4" <?= $o['status']==4 ? 'selected':'' ?>>🔴 Đã hủy</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <a href="admin.php?url=order_detail&id=<?= $o['id'] ?>" style="color: #3498db; text-decoration: none; font-size: 14px;">
                                <i class="fa-solid fa-eye"></i> Chi tiết
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6" style="text-align: center; padding: 20px; color: #999;">Không tìm thấy đơn hàng nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleProductMenu() {
    const submenu = document.getElementById("product-submenu");
    const arrow = document.getElementById("arrow-icon");
    submenu.classList.toggle("show");
    arrow.classList.toggle("rotate");
}

window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    const currentUrl = urlParams.get('url');
    // Luôn mở menu sản phẩm nếu đang ở các trang con của nó
    if (currentUrl === 'products' || currentUrl === 'categories') {
        document.getElementById("product-submenu").classList.add("show");
        document.getElementById("arrow-icon").classList.add("rotate");
    }
}
</script>
</body>
</html>