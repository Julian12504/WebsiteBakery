<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Administrator - Sweet Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../public/css/css_admin/style.css">  
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
    <a href="admin_logout.php" class="menu-item" style="color: #e74c3c;"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
</div>

    <div class="main-content">
        <div class="top-nav">
            <div>Chào, <?= $_SESSION['admin_name'] ?? 'Admin' ?></div>
            <div><a href="index.php"><i class="fa-solid fa-globe"></i> Xem trang chủ</a></div>
        </div>

        <div class="content-padding">
            <h2>Bảng Điều Khiển Quản Trị</h2>

            <div class="dashboard-cards">
                <div class="card" style="border-left: 5px solid #3498db;">
                    <h4>Tổng Đơn Hàng</h4>
                    <span class="value"><?= $totalOrders ?></span>
                </div>
                <div class="card" style="border-left: 5px solid #2ecc71;">
                    <h4>Doanh Thu (Đã giao)</h4>
                    <span class="value" style="color: #2ecc71;"><?= number_format($totalRevenue) ?>đ</span>
                </div>
                <div class="card" style="border-left: 5px solid #e74c3c;">
                    <h4>Sản Phẩm Sắp Hết</h4>
                    <span class="value" style="color: #e74c3c;"><?= count($lowStockProducts) ?></span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px;">
                <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <h3 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px;">Đơn hàng mới đặt</h3>
<h3>Đơn hàng chờ xử lý mới nhất</h3>
<table class="admin-table">
    <thead>
        <tr>
            <th>Mã đơn</th>
            <th>Khách hàng</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($recentOrders as $order): ?>
        <tr>
            <td>#DH<?= $order['id'] ?></td>
            <td><?= htmlspecialchars($order['full_name']) ?></td>
            <td><?= number_format($order['total_amount']) ?>đ</td>
            <td>
                <span class="badge" style="background-color: #f39c12; color: white; padding: 4px 8px; border-radius: 4px;">
                    Chờ xử lý
                </span>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
                </div>

                <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <h3 style="margin-top: 0; color: #e67e22; border-bottom: 1px solid #eee; padding-bottom: 10px;">Cảnh báo tồn kho</h3>
                    <ul class="warning-list">
                        <?php foreach($lowStockProducts as $p): ?>
                        <li>
                            • <strong><?= $p['name'] ?></strong>: <span style="color: #e74c3c;">Còn <?= $p['stock'] ?> cái</span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="admin.php?url=products" style="display: block; margin-top: 15px; color: #3498db; text-decoration: none; font-size: 13px;">Nhập hàng ngay →</a>
                </div>
            </div>
        </div>
    </div>
<script>
function toggleProductMenu() {
    const submenu = document.getElementById("product-submenu");
    const arrow = document.getElementById("arrow-icon");

    // Thêm hoặc xóa class 'show' để ẩn hiện
    submenu.classList.toggle("show");
    
    // Thêm hoặc xóa class 'rotate' để xoay mũi tên
    arrow.classList.toggle("rotate");
}

// Giữ menu luôn mở nếu đang ở trang liên quan đến sản phẩm
window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    const currentUrl = urlParams.get('url');

    if (currentUrl === 'products' || currentUrl === 'categories') {
        document.getElementById("product-submenu").classList.add("show");
        document.getElementById("arrow-icon").classList.add("rotate");
    }
}
</script>
</body>
</html>