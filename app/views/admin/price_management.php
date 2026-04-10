<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Administrator - Quản lý giá bán</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../public/css/css_admin/style.css">
    <link rel="stylesheet" href="../public/css/css_admin/product.css">
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
        <div class="top-nav">
            <a href="index.php"><i class="fa-solid fa-share"></i> Vào trang web</a>
            <a href="#">Liên hệ</a>
            <a href="admin.php?url=orders">Đơn hàng</a>
        </div>

        <div class="breadcrumb">
            <i class="fa-solid fa-house"></i> Trang chủ  >  Danh mục  >  Quản lý giá bán
        </div>

        <div class="page-header" style="padding: 20px 15px 0 15px;">
            <h1 style="margin: 0 0 8px; font-size: 24px; color: #2c3e50;">Quản lý giá bán</h1>
            <p style="margin: 0; color: #555;">Tra cứu giá vốn, % lợi nhuận và giá bán. Chỉnh sửa tỉ lệ lợi nhuận để cập nhật giá bán phù hợp theo từng sản phẩm.</p>
        </div>

        <div class="toolbar">
            <form action="admin.php" method="GET" id="filterForm" style="display: flex; gap: 10px; align-items: center; width: 100%; flex-wrap: wrap;">
                <input type="hidden" name="url" value="price_management">

                <input type="text" name="search" placeholder="Tìm kiếm tên sản phẩm..." 
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" style="flex:1; min-width: 200px;">

                <select name="category_id" onchange="document.getElementById('filterForm').submit()" style="min-width: 150px;">
                    <option value="">Tất cả danh mục</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= (isset($_GET['category_id']) && $_GET['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                            <?= $cat['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" style="padding: 6px 12px; cursor:pointer;">Lọc</button>
            </form>
            <form action="admin.php" method="GET" style="display:flex; flex-wrap:wrap; gap:10px; align-items:center; margin-top:10px; padding:0 15px 15px;">
                <input type="hidden" name="url" value="price_management">
                <input type="number" name="min_cost" placeholder="Giá vốn từ" step="0.01" value="<?= htmlspecialchars($_GET['min_cost'] ?? '') ?>" class="form-control" style="width:140px;">
                <input type="number" name="max_cost" placeholder="đến" step="0.01" value="<?= htmlspecialchars($_GET['max_cost'] ?? '') ?>" class="form-control" style="width:140px;">
                <input type="number" name="min_margin" placeholder="% Lợi nhuận từ" step="0.01" value="<?= htmlspecialchars($_GET['min_margin'] ?? '') ?>" class="form-control" style="width:160px;">
                <input type="number" name="max_margin" placeholder="đến" step="0.01" value="<?= htmlspecialchars($_GET['max_margin'] ?? '') ?>" class="form-control" style="width:140px;">
                <input type="number" name="min_price" placeholder="Giá bán từ" step="0.01" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>" class="form-control" style="width:140px;">
                <input type="number" name="max_price" placeholder="đến" step="0.01" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>" class="form-control" style="width:140px;">
                <button type="submit" class="btn-submit" style="padding: 8px 16px;">Lọc</button>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 80px;">Mã</th>
                        <th>Danh mục</th>
                        <th>Tiêu đề</th>
                        <th>Giá vốn</th>
                        <th>% Lợi nhuận</th>
                        <th>Giá bán</th>
                        <th>Sửa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($products)): foreach($products as $p): ?>
                    <tr>
                        <td style="font-weight: bold; color: #555;">#<?= $p['id'] ?></td>
                        <td class="text-left"><?= htmlspecialchars($p['category_name'] ?? 'Chưa phân loại') ?></td>
                        <td class="text-left" style="color: #3498db; font-weight: 500;"><?= htmlspecialchars($p['name']) ?></td>
                        <td><?= number_format($p['gia_von'], 0, ',', '.') ?>đ</td>
                        <td><?= number_format($p['loi_nhuan'], 2) ?>%</td>
                        <td><?= number_format($p['selling_price'], 0, ',', '.') ?>đ</td>
                        <td>
                            <button type="button" class="btn-edit" onclick='openEditMarginModal(<?= json_encode($p) ?>)' style="border:none; background:none; color:#e67e22; cursor:pointer;">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="8">Không tìm thấy sản phẩm nào phù hợp.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="pagination" style="margin-top: 20px; display: flex; justify-content: center; gap: 5px;">
                <?php if($page > 1): ?>
                    <a href="admin.php?url=price_management&page=<?= $page - 1 ?>">« Trước</a>
                <?php endif; ?>

                <?php for($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="admin.php?url=price_management&page=<?= $i ?>" 
                       style="<?= ($i == $page) ? 'background: #3498db; color: white;' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if($page < $totalPages): ?>
                    <a href="admin.php?url=price_management&page=<?= $page + 1 ?>">Sau »</a>
                <?php endif; ?>
            </div>

            <p style="text-align: center; font-size: 12px; color: #7f8c8d; margin-top: 10px;">
                Hiển thị trang <?= $page ?> / <?= $totalPages ?> (Tổng cộng <?= $totalProducts ?> sản phẩm)
            </p>
        </div>
    </div>

<div id="priceModal" class="modal">
    <div class="modal-content" style="max-width: 520px;">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h2>Chỉnh sửa tỉ lệ lợi nhuận</h2>
        <form action="admin.php?url=update_profit" method="POST" id="priceForm">
            <input type="hidden" name="id" id="mp_prod_id">
            <input type="hidden" name="gia_von" id="mp_prod_cost">

            <div class="form-group">
                <label>Sản phẩm:</label>
                <div id="mp_prod_name" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; background: #f8f9fa;"></div>
            </div>

            <div class="form-group">
                <label>Giá vốn (VNĐ):</label>
                <div id="mp_prod_cost_text" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; background: #f8f9fa;"></div>
            </div>

            <div class="form-group">
                <label>% Lợi nhuận:</label>
                <input type="number" name="loi_nhuan" id="mp_prod_margin" class="form-control" min="0" step="0.01" required>
            </div>

            <div class="form-group">
                <label>Giá bán dự kiến:</label>
                <div id="mp_prod_price_preview" style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 4px; background: #fafafa; color: #e74c3c; font-weight: 600;">0đ</div>
            </div>

            <button type="submit" class="btn-add" style="width: 100%; justify-content: center;">Lưu thay đổi</button>
        </form>
    </div>
</div>

</body>
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
    if (currentUrl === 'products' || currentUrl === 'categories' || currentUrl === 'price_management') {
        document.getElementById("product-submenu").classList.add("show");
        document.getElementById("arrow-icon").classList.add("rotate");
    }
}

const modal = document.getElementById("priceModal");

function openEditMarginModal(prod) {
    document.getElementById('mp_prod_id').value = prod.id;
    document.getElementById('mp_prod_cost').value = prod.gia_von;
    document.getElementById('mp_prod_name').innerText = prod.name;
    document.getElementById('mp_prod_cost_text').innerText = parseFloat(prod.gia_von).toLocaleString('vi-VN', {maximumFractionDigits: 0}) + 'đ';
    document.getElementById('mp_prod_margin').value = prod.loi_nhuan;
    updatePricePreview();
    modal.style.display = 'block';
}

function updatePricePreview() {
    const cost = parseFloat(document.getElementById('mp_prod_cost').value) || 0;
    const margin = parseFloat(document.getElementById('mp_prod_margin').value) || 0;
    const total = cost + (cost * margin / 100);
    document.getElementById('mp_prod_price_preview').innerText = total.toLocaleString('vi-VN', {maximumFractionDigits: 0}) + 'đ';
}

document.getElementById('mp_prod_margin').addEventListener('input', updatePricePreview);

function closeModal() {
    modal.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == modal) closeModal();
}
</script>
</html>