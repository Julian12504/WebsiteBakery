<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng #<?= $order['id'] ?></title>
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
    <a href="admin_logout.php" class="menu-item" style="color: #e74c3c;"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
</div>

<div class="main-content">
    <div class="top-nav">
        <div><a href="admin.php?url=orders" style="color: #3498db;"><i class="fa-solid fa-arrow-left"></i> Quay lại danh sách</a></div>
        <div>Chào, Admin</div>
    </div>

    <div class="content-padding">
        <h2 style="margin-bottom: 20px;">Chi Tiết Đơn Hàng #<?= $order['id'] ?></h2>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); height: fit-content;">
    <h3 style="border-bottom: 1px solid #eee; padding-bottom: 10px; color: #2c3e50;">Thông tin giao hàng</h3>
    
    <p><strong>Người nhận:</strong> <?= htmlspecialchars($order['full_name'] ?? $order['name'] ?? 'Khách hàng') ?></p>
    <p><strong>Số điện thoại:</strong> <?= $order['phone'] ?? $order['sdt'] ?? 'Chưa cập nhật' ?></p>
    <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['shipping_address'] ?? $order['address'] ?? 'N/A') ?></p>
    
    <p><strong>Ngày đặt:</strong> 
        <?php 
            $date_key = $order['created_at'] ?? $order['order_date'] ?? null;
            echo $date_key ? date('d/m/Y H:i', strtotime($date_key)) : 'Không rõ ngày';
        ?>
    </p>
    
    <p><strong>Ghi chú:</strong> <br>
        <span style="color: #7f8c8d; font-style: italic;">
            <?= htmlspecialchars($order['note'] ?? $order['ghi_chu'] ?? 'Không có ghi chú') ?>
        </span>
    </p>
</div>

            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <h3 style="border-bottom: 1px solid #eee; padding-bottom: 10px; color: #2c3e50;">Sản phẩm đã đặt</h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Ảnh</th>
                            <th>Tên bánh</th>
                            <th>Đơn giá</th>
                            <th>SL</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orderItems as $item): ?>
                        <tr>
                            <td style="width: 60px;">
                             <img src="images/<?= $item['image'] ?>" style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td class="text-left"><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= number_format($item['price']) ?>đ</td>
                            <td>x<?= $item['quantity'] ?></td>
                            <td style="font-weight: bold;"><?= number_format($item['price'] * $item['quantity']) ?>đ</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" style="text-align: right; padding: 15px;"><strong>Tổng cộng thanh toán:</strong></td>
                            <td style="color: #e74c3c; font-size: 18px; font-weight: bold;"><?= number_format($order['total_amount']) ?>đ</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>