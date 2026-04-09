<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý danh mục - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../public/css/css_admin/style.css">
    <link rel="stylesheet" href="../public/css/css_admin/product.css">
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
        <a href="admin.php?url=categories" class="menu-item active" style="padding-left: 40px; font-size: 13px;">
            <i class="fa-solid fa-list"></i> Danh mục
        </a>
        <a href="admin.php?url=products" class="menu-item" style="padding-left: 40px; font-size: 13px;">
            <i class="fa-solid fa-box"></i> Tất cả sản phẩm
        </a>
        <a href="admin.php?url=price_management" class="menu-item" style="padding-left: 40px; font-size: 13px;">
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
            <i class="fa-solid fa-house"></i> Trang chủ  >  Danh mục  >  Quản lý danh mục
        </div>

        <div class="toolbar" style="flex-wrap: wrap; gap: 15px; align-items: flex-start;">
            <div style="flex:1; min-width:260px;">
                <h2>Danh mục sản phẩm</h2>
                <p>Quản lý các loại sản phẩm và thông tin mô tả cho từng loại.</p>
            </div>
        </div>

        <?php if (!empty($msg)): ?>
            <div style="margin: 15px 20px; padding: 15px; background: #f0f9eb; border: 1px solid #c6ecd9; color: #3d8b49; border-radius: 8px;">
                <?php
                switch ($msg) {
                    case 'created': echo 'Thêm danh mục thành công.'; break;
                    case 'updated': echo 'Cập nhật danh mục thành công.'; break;
                    case 'deleted': echo 'Xóa danh mục thành công.'; break;
                    case 'missing_name': echo 'Vui lòng nhập tên danh mục.'; break;
                    case 'invalid_id': echo 'Danh mục không hợp lệ.'; break;
                    case 'not_found': echo 'Không tìm thấy danh mục.'; break;
                    default: echo htmlspecialchars($msg); break;
                }
                ?>
            </div>
        <?php endif; ?>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px; padding: 20px;">
            <div class="card" style="padding: 20px;">
                <h3 style="margin-top:0;"><?= isset($category) ? 'Chỉnh sửa danh mục' : 'Thêm danh mục mới' ?></h3>
                <form action="admin.php?url=<?= isset($category) ? 'update_category' : 'save_category' ?>" method="POST" style="display:grid; gap: 15px;">
                    <?php if (isset($category)): ?>
                        <input type="hidden" name="id" value="<?= $category['id'] ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Tên danh mục</label>
                        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($category['name'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Mô tả</label>
                        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn-submit" style="width: 160px;"><?= isset($category) ? 'Lưu thay đổi' : 'Thêm danh mục' ?></button>
                    <?php if (isset($category)): ?>
                        <a href="admin.php?url=categories" class="btn-cancel" style="display:inline-flex; align-items:center; justify-content:center; width: 160px;">Hủy</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="card" style="padding:20px; overflow-x:auto;">
                <h3 style="margin-top:0;">Danh sách danh mục</h3>
                <table class="admin-table" style="width:100%; min-width:100%; margin-top:15px;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tên danh mục</th>
                            <th>Mô tả</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $index => $cat): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($cat['name']) ?></td>
                                    <td class="text-left"><?= htmlspecialchars($cat['description']) ?></td>
                                    <td style="display:flex; gap:10px; flex-wrap:wrap; justify-content:center;">
                                        <a href="admin.php?url=edit_category&id=<?= $cat['id'] ?>" class="btn-edit" style="text-decoration:none;">Sửa</a>
                                        <a href="admin.php?url=delete_category&id=<?= $cat['id'] ?>" class="btn-delete" style="text-decoration:none;" onclick="return confirm('Xóa danh mục này?')">Xóa</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4">Chưa có danh mục nào.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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
window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    const currentUrl = urlParams.get('url');
    if (currentUrl === 'products' || currentUrl === 'categories' || currentUrl === 'price_management') {
        document.getElementById("product-submenu").classList.add("show");
        document.getElementById("arrow-icon").classList.add("rotate");
    }
}
</script>
</body>
</html>