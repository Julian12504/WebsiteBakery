<!-- <?php
// Test thử xem biến có sang tới đây không
if (!isset($search_date)) {
    $search_date = date('Y-m-d'); 
}
if (!isset($dailyImports)) {
    $dailyImports = [];
}
?> -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tạo Phiếu Nhập Kho - WebsiteBakery</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../public/css/css_admin/style.css"> 
    <link rel="stylesheet" href="../public/css/css_admin/import.css">
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
        <div class="import-wrapper">
            <div class="top-section">
                <div class="left-panel">
                    <div class="import-card">
                        <div class="card-header">
                            <h3><i class="fa-solid fa-cart-plus"></i> Chọn sản phẩm nhập</h3>
                        </div>
                        <div class="card-body">
                            <div class="info-grid">
                                <div class="form-group">
                                    <label>Sản phẩm</label>
                                    <select id="select_product" class="form-control">
                                        <option value="">-- Chọn bánh --</option>
                                        <?php foreach($products as $p): ?>
                                            <option value="<?= $p['id'] ?>" data-name="<?= $p['name'] ?>">
                                                <?= $p['name'] ?> (Tồn: <?= $p['stock'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Số lượng</label>
                                    <input type="number" id="input_qty" class="form-control" min="1">
                                </div>
                                <div class="form-group">
                                    <label>Giá nhập</label>
                                    <input type="number" id="input_price" class="form-control" min="0">
                                </div>
                                <div class="form-group" style="display:flex; align-items:flex-end;">
                                    <button type="button" onclick="addToTempList()" class="btn-submit" style="background:#3498db; width:100%;">
                                        <i class="fa-solid fa-plus"></i> Thêm vào danh sách
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="admin.php?url=process_import_all" method="POST">
                        <div class="import-card" style="margin-top:20px;">
                            <div class="card-header">
                                <h3><i class="fa-solid fa-list-check"></i> Danh sách chờ nhập kho</h3>
                            </div>
                            <div class="card-body">
                                <table class="import-table" id="temp_table">
                                    <thead>
                                        <tr>
                                            <th>Sản phẩm</th>
                                            <th width="15%">Số lượng</th>
                                            <th width="20%">Giá nhập</th>
                                            <th width="20%">Thành tiền</th>
                                            <th width="10%">Xóa</th>
                                        </tr>
                                    </thead>
                                    <tbody id="temp_list_body"></tbody>
                                </table>

                                <div class="action-bar" style="margin-top:20px;">
                                    <div style="font-size:18px;">Tổng tiền phiếu: <b id="grand_total" style="color:red;">0</b>đ</div>
                                    <button type="submit" class="btn-submit" id="btn_submit_all" style="display:none;">
                                        <i class="fa-solid fa-check-double"></i> Xác nhận nhập kho
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="right-panel">
                    <?php if (!empty($editImport)): ?>
                        <div class="import-card edit-card">
                            <div class="card-header">
                                <h3><i class="fa-solid fa-pen-to-square"></i> Sửa dòng nhập hàng</h3>
                            </div>
                            <div class="card-body">
                                <form action="admin.php?url=update_import" method="POST">
                                    <div class="form-group">
                                        <label>Sản phẩm</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($editImport['product_name']) ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>Số lượng</label>
                                        <input type="number" name="quantity" class="form-control" value="<?= $editImport['quantity'] ?>" min="1" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Giá nhập</label>
                                        <input type="number" name="import_price" class="form-control" value="<?= $editImport['import_price'] ?>" min="0" step="0.01" required>
                                    </div>
                                    <input type="hidden" name="id" value="<?= $editImport['detail_id'] ?>">
                                    <input type="hidden" name="search_date" value="<?= htmlspecialchars($search_date) ?>">
                                    <div class="action-bar" style="margin-top:10px;">
                                        <button type="submit" class="btn-submit"><i class="fa-solid fa-save"></i> Lưu thay đổi</button>
                                        <a href="admin.php?url=import_product&search_date=<?= urlencode($search_date) ?>" class="btn-cancel"><i class="fa-solid fa-ban"></i> Hủy</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="import-card history-section">
                        <div class="card-header">
                            <h3><i class="fa-solid fa-list-ul"></i> Danh sách hàng đã nhập</h3>
                        </div>
                        <div class="card-body">
                            <form action="admin.php" method="GET" class="search-section">
                                <input type="hidden" name="url" value="import_product">
                                <div class="form-group" style="flex:1; min-width:140px;">
                                    <label>Chọn ngày tra cứu:</label>
                                    <input type="date" name="search_date" class="form-control" value="<?= $search_date ?>">
                                </div>
                                <button type="submit" class="btn-submit" style="height:42px; margin-top:24px;">Tra cứu</button>
                            </form>

                            <table class="history-table">
                                <thead>
                                    <tr>
                                        <th>Giờ nhập</th>
                                        <th>Sản phẩm</th>
                                        <th>Số lượng</th>
                                        <th>Giá nhập</th>
                                        <th>Thành tiền</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($dailyImports)): ?>
                                        <?php foreach ($dailyImports as $row): ?>
                                            <tr>
                                                <td><?= date('H:i', strtotime($row['receipt_date'])) ?></td>
                                                <td><?= htmlspecialchars($row['product_name']) ?></td>
                                                <td><?= $row['quantity'] ?> <?= $row['unit'] ?></td>
                                                <td><?= number_format($row['import_price']) ?>đ</td>
                                                <td style="font-weight:bold; color:#27ae60;">
                                                    <?= number_format($row['quantity'] * $row['import_price']) ?>đ
                                                </td>
                                                <td class="table-actions">
                                                    <a href="admin.php?url=edit_import&id=<?= $row['detail_id'] ?>&search_date=<?= urlencode($search_date) ?>" class="btn-submit" style="padding:6px 10px; font-size:13px;"><i class="fa-solid fa-pen-to-square"></i> Sửa</a>
                                                    <a href="admin.php?url=delete_import&id=<?= $row['detail_id'] ?>&search_date=<?= urlencode($search_date) ?>" class="btn-cancel" style="padding:6px 10px; font-size:13px;"><i class="fa-solid fa-trash"></i> Xóa</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" style="text-align:center; padding:30px; color:#999;">
                                                <i class="fa-solid fa-box-open" style="font-size:24px; display:block; margin-bottom:10px;"></i>
                                                Không có dữ liệu nhập trong ngày <?= date('d/m/Y', strtotime($search_date)) ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    // 1. Khai báo biến
    const qtyInput = document.getElementById('input_qty');     // Sửa ID cho khớp HTML
    const priceInput = document.getElementById('input_price'); // Sửa ID cho khớp HTML
    let grandTotal = 0;

    // 2. Hàm thêm vào danh sách tạm (Queue)
    function addToTempList() {
        const select = document.getElementById('select_product');
        const productId = select.value;
        const productName = select.options[select.selectedIndex].getAttribute('data-name');
        const qty = parseInt(qtyInput.value);
        const price = parseFloat(priceInput.value);

        if (!productId || isNaN(qty) || isNaN(price)) {
            alert("Vui lòng chọn sản phẩm, nhập số lượng và giá!");
            return;
        }

        const total = qty * price;
        const tbody = document.getElementById('temp_list_body');

        // Tạo dòng mới
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${productName} <input type="hidden" name="product_ids[]" value="${productId}"></td>
            <td><input type="number" name="quantities[]" value="${qty}" class="form-control" readonly></td>
            <td><input type="number" name="prices[]" value="${price}" class="form-control" readonly></td>
            <td><b>${total.toLocaleString('vi-VN')}đ</b></td>
            <td><button type="button" onclick="removeRow(this, ${total})" class="btn-cancel" style="padding:5px 10px; background:red; color:white; border:none; cursor:pointer;">X</button></td>
        `;

        tbody.appendChild(row);

        // Cập nhật tổng tiền
        grandTotal += total;
        document.getElementById('grand_total').textContent = grandTotal.toLocaleString('vi-VN');
        document.getElementById('btn_submit_all').style.display = 'block';

        // Reset ô nhập
        qtyInput.value = '';
        priceInput.value = '';
        select.value = '';
    }

    // 3. Hàm xóa dòng khỏi danh sách tạm
    function removeRow(btn, rowTotal) {
        btn.closest('tr').remove();
        grandTotal -= rowTotal;
        document.getElementById('grand_total').textContent = grandTotal.toLocaleString('vi-VN');
        
        if (grandTotal <= 0) {
            document.getElementById('btn_submit_all').style.display = 'none';
        }
    }
</script>
</body>
</html>