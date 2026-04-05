<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tạo Phiếu Nhập Kho - WebsiteBakery</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../public/css/css_admin/style.css"> <link rel="stylesheet" href="../public/css/css_admin/import.css"> </head>
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
    <div class="import-wrapper">
        <div class="top-nav" style="margin-bottom: 20px;">
            <a href="admin.php?url=import_management" style="text-decoration: none; color: #606266;">
                <i class="fa-solid fa-chevron-left"></i> Quay lại danh sách phiếu
            </a>
        </div>

        <form action="admin.php?url=process_import" method="POST">
            <div class="import-card">
                <div class="card-header">
                    <h3><i class="fa-solid fa-file-invoice"></i> Thông tin phiếu nhập</h3>
                    <span style="color: #909399; font-size: 13px;">Mã phiếu: Tự động tạo</span>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="form-group">
                            <label>Người nhập</label>
                            <input type="text" class="form-control" value="Admin" readonly style="background:#f5f7fa;">
                        </div>
                        <div class="form-group">
                            <label>Ngày nhập</label>
                            <input type="text" class="form-control" value="<?= date('d/m/Y H:i') ?>" readonly style="background:#f5f7fa;">
                        </div>
                        <div class="form-group">
                            <label>Ghi chú phiếu</label>
                            <input type="text" name="note" class="form-control" placeholder="Nhập ghi chú (nếu có)...">
                        </div>
                    </div>
                </div>
            </div>

            <div class="import-card">
                <div class="card-header">
                    <h3><i class="fa-solid fa-list-check"></i> Chi tiết mặt hàng</h3>
                </div>
                <div class="card-body">
                    <div class="formula-alert">
                        <i class="fa-solid fa-circle-info"></i> 
                        <b>Quy tắc bình quân gia quyền:</b> Giá vốn mới sẽ tự cập nhật.
                    </div>

                    <table class="import-table">
                        <thead>
                            <tr>
                                <th width="40%">Sản phẩm</th>
                                <th width="20%">Số lượng</th>
                                <th width="20%">Giá nhập (đ)</th>
                                <th width="20%">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select name="product_id" class="form-control" id="product_select" required>
                                        <option value="">-- Chọn bánh --</option>
                                        <?php foreach($products as $p): ?>
                                            <option value="<?= $p['id'] ?>">
                                                <?= htmlspecialchars($p['name']) ?> (Tồn: <?= $p['stock'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><input type="number" name="quantity" id="qty_input" class="form-control" min="1" required></td>
                                <td><input type="number" name="import_price" id="price_input" class="form-control" min="0" required></td>
                                <td style="font-weight: bold; color: #f56c6c;"><span id="total_row">0</span>đ</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="action-bar" style="margin-top: 20px; text-align: right;">
                        <a href="admin.php?url=import_management" class="btn-cancel">Hủy bỏ</a>
                        <button type="submit" class="btn-submit">Xác nhận nhập kho</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const qtyInput = document.getElementById('qty_input');
    const priceInput = document.getElementById('price_input');
    const totalRow = document.getElementById('total_row');

    function updateTotal() {
        const qty = parseFloat(qtyInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        totalRow.textContent = (qty * price).toLocaleString('vi-VN');
    }

    qtyInput.addEventListener('input', updateTotal);
    priceInput.addEventListener('input', updateTotal);
</script>

</body>
</html>