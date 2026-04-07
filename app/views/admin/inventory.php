<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý tồn kho - Administrator</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../public/css/css_admin/style.css">
     <link rel="stylesheet" href="../public/css/css_admin/inventory.css">
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
        <a href="admin.php?url=users" class="menu-item"><i class="fa-solid fa-users"></i> Quản lý người dùng</a>
        <a href="admin.php?url=import_product" class="menu-item"><i class="fa-solid fa-truck-ramp-box"></i> Quản lý nhập hàng</a>
        <a href="admin.php?url=inventory" class="menu-item active"><i class="fa-solid fa-boxes-stacked"></i> Tồn kho / Báo cáo</a>
        <a href="admin_logout.php" class="menu-item" style="color: #e74c3c;"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
    </div>

    <div class="main-content">
        <div class="top-nav">
            <div>Chào, <?= $_SESSION['admin_name'] ?? 'Admin' ?></div>
            <div><a href="index.php"><i class="fa-solid fa-globe"></i> Xem trang chủ</a></div>
        </div>

        <div class="content-padding">
            <h2>Quản lý tồn kho và báo cáo</h2>

            <div class="inventory-panel">
                <div class="panel-card">
                    <h3>Tra cứu tồn kho tại thời điểm</h3>
                    <form method="GET" action="admin.php">
                        <input type="hidden" name="url" value="inventory">
                        <div class="form-row">
                            <label>Sản phẩm</label>
                            <select name="product_id" required>
                                <option value="">Chọn sản phẩm</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?= $product['id'] ?>" <?= $product_id == $product['id'] ? 'selected' : '' ?>><?= htmlspecialchars($product['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-row">
                            <label>Chọn thời điểm</label>
                            <input type="datetime-local" name="asof" value="<?= htmlspecialchars($asof) ?>" required>
                        </div>
                        <button type="submit" class="btn-primary">Tra cứu</button>
                    </form>

                    <?php if ($selectedProduct && $stockAtTime !== null): ?>
                        <div class="result-box">
                            <h4>Kết quả</h4>
                            <p><strong><?= htmlspecialchars($selectedProduct['name']) ?></strong> có <strong><?= number_format($stockAtTime) ?></strong> đơn vị tồn kho tại <strong><?= date('d/m/Y H:i', strtotime($asof)) ?></strong>.</p>
                        </div>
                    <?php elseif ($selectedProduct && $asof): ?>
                        <div class="result-box warning">Không thể xác định số lượng tồn kho. Vui lòng kiểm tra lại thời điểm.</div>
                    <?php endif; ?>
                </div>

                <div class="panel-card">
                    <h3>Báo cáo nhập - xuất theo khoảng thời gian</h3>
                    <form method="GET" action="admin.php">
                        <input type="hidden" name="url" value="inventory">
                        <div class="form-row">
                            <label>Sản phẩm</label>
                            <select name="product_id" required>
                                <option value="">Chọn sản phẩm</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?= $product['id'] ?>" <?= $product_id == $product['id'] ? 'selected' : '' ?>><?= htmlspecialchars($product['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-row">
                            <label>Ngày bắt đầu</label>
                            <input type="date" name="from_date" value="<?= htmlspecialchars($from_date) ?>" required>
                        </div>
                        <div class="form-row">
                            <label>Ngày kết thúc</label>
                            <input type="date" name="to_date" value="<?= htmlspecialchars($to_date) ?>">
                        </div>
                        <button type="submit" class="btn-primary">Xem báo cáo</button>
                    </form>

                    <?php if ($selectedProduct && $importsRange !== null): ?>
                        <div class="result-box">
                            <h4>Kết quả</h4>
                            <p>Sản phẩm <strong><?= htmlspecialchars($selectedProduct['name']) ?></strong></p>
                            <p>Nhập: <strong><?= number_format($importsRange) ?></strong></p>
                            <p>Xuất: <strong><?= number_format($exportsRange) ?></strong></p>
                            <p>Chênh lệch (Nhập - Xuất): <strong><?= number_format($netRange) ?></strong></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="panel-card inventory-alert-card">
                <h3>Cảnh báo sản phẩm sắp hết hàng</h3>
                <form method="GET" action="admin.php" class="inline-form">
                    <input type="hidden" name="url" value="inventory">
                    <div class="form-row">
                        <label>Ngưỡng cảnh báo</label>
                        <input type="number" name="threshold" min="0" value="<?= htmlspecialchars($threshold) ?>">
                    </div>
                    <button type="submit" class="btn-warning">Cập nhật</button>
                </form>

                <?php if (empty($lowStockProducts)): ?>
                    <p>Không có sản phẩm nào dưới ngưỡng hiện tại.</p>
                <?php else: ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Mã</th>
                                <th>Sản phẩm</th>
                                <th>Kho hiện tại</th>
                                <th>Ngưỡng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lowStockProducts as $product): ?>
                                <tr>
                                    <td><?= $product['id'] ?></td>
                                    <td><?= htmlspecialchars($product['name']) ?></td>
                                    <td><?= number_format($product['stock']) ?></td>
                                    <td><?= number_format($threshold) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
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
    document.getElementById("product-submenu").classList.add("show");
    document.getElementById("arrow-icon").classList.add("rotate");
}
</script>
</body>
</html>