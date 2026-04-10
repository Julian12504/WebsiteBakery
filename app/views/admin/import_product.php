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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="../public/css/css_admin/style.css"> 
    <link rel="stylesheet" href="../public/css/css_admin/import.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

  <div class="sidebar">
        <div class="sidebar-header">
            <i class="fa-solid fa-user-shield"></i>
            <div style="display:inline-block; vertical-align:middle; margin-left:10px; line-height:1.2;">
                <div style="font-weight:700;">Administrator</div>
                <div style="font-size:0.95rem; opacity:0.85;">
                    <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?><br>
                    <span style="font-size:0.85rem; opacity:0.7;"><?= htmlspecialchars($_SESSION['admin_username'] ?? '') ?></span>
                </div>
            </div>
        </div>
    <a href="admin.php?url=dashboard" class="menu-item"><i class="fa-solid fa-house"></i> Trang chủ Admin</a>

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
        <a href="admin.php?url=price_management" class="menu-item active" style="padding-left: 40px; font-size: 13px;">
            <i class="fa-solid fa-tags"></i> Quản lý giá bán
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
                                    <label>Sản phẩm </label>
                                    <select id="select_product" class="form-control select2-search">
                                        <option value="">-- Chọn bánh --</option>
                                        <?php foreach($products as $p): ?>
                                            <option value="<?= $p['id'] ?>" data-name="<?= $p['name'] ?>" data-sku="<?= $p['id'] ?? '' ?>">
                                                <?= htmlspecialchars($p['name']) ?> (SKU: <?= $p['id'] ?>, Tồn: <?= $p['stock'] ?>)
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

                    <?php if (!empty($viewReceipt)): ?>
                        <div class="import-card receipt-view-card" style="margin-bottom:20px;">
                            <div class="card-header">
                                <h3><i class="fa-solid fa-file-invoice"></i> Phiếu nhập #<?= htmlspecialchars($viewReceipt['id']) ?></h3>
                                <span style="font-size:14px; color:#555;">
                                    <?= $viewReceipt['status'] == 1 ? 'Nháp' : 'Hoàn thành' ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <p><strong>Ngày tạo:</strong> <?= date('d/m/Y H:i', strtotime($viewReceipt['receipt_date'])) ?></p>
                                <ul class="list-group list-group-flush mb-3">
                                    <?php foreach ($viewItems as $item): ?>
                                        <li class="list-group-item px-0 d-flex justify-content-between">
                                            <span><?= htmlspecialchars($item['product_name']) ?> x <?= (int) $item['quantity'] ?></span>
                                            <strong><?= number_format($item['quantity'] * $item['import_price']) ?>đ</strong>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php if ($viewReceipt['status'] == 1): ?>
                                    <form method="post" action="admin.php?url=complete_import">
                                        <input type="hidden" name="receipt_id" value="<?= (int) $viewReceipt['id'] ?>">
                                        <input type="hidden" name="search_date" value="<?= htmlspecialchars($search_date) ?>">
                                        <button type="submit" class="btn-submit"><i class="fa-solid fa-check"></i> Hoàn thành phiếu nhập</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="import-card receipt-list-section" style="margin-bottom:20px;">
                        <div class="card-header">
                            <h3><i class="fa-solid fa-list-ul"></i> Danh sách phiếu nhập</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($receipts)): ?>
                                <table class="history-table">
                                    <thead>
                                        <tr>
                                            <th>Phiếu</th>
                                            <th>Trạng thái</th>
                                            <th>Số dòng</th>
                                            <th>Tổng tiền</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($receipts as $receipt): ?>
                                            <tr>
                                                <td>#<?= htmlspecialchars($receipt['id']) ?></td>
                                                <td><?= $receipt['status'] == 1 ? 'Nháp' : 'Hoàn thành' ?></td>
                                                <td><?= htmlspecialchars($receipt['item_count'] ?? 0) ?></td>
                                                <td><?= number_format($receipt['total_amount'] ?? 0) ?>đ</td>
                                                <td class="table-actions">
                                                    <a href="admin.php?url=import_product&search_date=<?= urlencode($search_date) ?>&view=<?= (int) $receipt['id'] ?>" class="btn-submit" style="padding:6px 10px; font-size:13px;"><i class="fa-solid fa-eye"></i> Xem</a>
                                                    <?php if ($receipt['status'] == 1): ?>
                                                        <form method="post" action="admin.php?url=complete_import" style="display:inline-block; margin-left:4px;">
                                                            <input type="hidden" name="receipt_id" value="<?= (int) $receipt['id'] ?>">
                                                            <input type="hidden" name="search_date" value="<?= htmlspecialchars($search_date) ?>">
                                                            <button type="submit" class="btn-submit" style="padding:6px 10px; font-size:13px;"><i class="fa-solid fa-check"></i> Hoàn thành</button>
                                                        </form>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p style="color:#777;">Chưa có phiếu nhập trong ngày này.</p>
                            <?php endif; ?>
                        </div>
                    </div>

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

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // 1. Khai báo biến
    const qtyInput = document.getElementById('input_qty');
    const priceInput = document.getElementById('input_price');
    let grandTotal = 0;

    // Khởi tạo Select2 với tìm kiếm
    document.addEventListener('DOMContentLoaded', function() {
        $('#select_product').select2({
            placeholder: '-- Chọn bánh hoặc tìm kiếm --',
            allowClear: true,
            width: '100%',
            matcher: function(params, data) {
                if ($.trim(params.term) === '') {
                    return data;
                }

                var term = params.term.toLowerCase();
                var text = data.text.toLowerCase();

                if (text.indexOf(term) > -1) {
                    return data;
                }

                return null;
            }
        });
    });

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
        $('#select_product').val(null).trigger('change');
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

    // 4. Hiển thị/ẩn menu quản lý sản phẩm
    function toggleProductMenu() {
        const submenu = document.getElementById('product-submenu');
        const arrow = document.getElementById('arrow-icon');

        submenu.classList.toggle('show');
        arrow.classList.toggle('rotate');
    }
</script>
</body>
</html>