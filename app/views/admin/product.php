<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Administrator - Quản lý sản phẩm</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../public/css/css_admin/style.css"> 
    <link rel="stylesheet" href="../public/css/css_admin/product.css">
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
            <a href="index.php"><i class="fa-solid fa-share"></i> Vào trang web</a>
            <a href="#">Liên hệ</a>
            <a href="admin.php?url=orders">Đơn hàng</a>
        </div>

        <div class="breadcrumb">
            <i class="fa-solid fa-house"></i> Trang chủ  >  Danh mục  >  Quản lí sản phẩm
        </div>
<div class="toolbar">
    <form action="admin.php" method="GET" id="filterForm" style="display: flex; gap: 10px; align-items: center; width: 100%;">
        <input type="hidden" name="url" value="products">

        <select name="action">
            <option value="">Tác vụ</option>
            <option value="delete">Xóa đã chọn</option>
            <option value="hide">Ẩn đã chọn</option>
        </select>

        <input type="text" name="search" placeholder="Tìm kiếm tên sản phẩm..." 
               value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">

        <select name="category_id" onchange="document.getElementById('filterForm').submit()">
            <option value="">Tất cả danh mục</option>
            <?php foreach($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= (isset($_GET['category_id']) && $_GET['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                    <?= $cat['name'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="limit" onchange="document.getElementById('filterForm').submit()">
            <option value="10" <?= (isset($_GET['limit']) && $_GET['limit'] == 10) ? 'selected' : '' ?>>Hiện 10</option>
            <option value="20" <?= (isset($_GET['limit']) && $_GET['limit'] == 20) ? 'selected' : '' ?>>Hiện 20</option>
            <option value="50" <?= (isset($_GET['limit']) && $_GET['limit'] == 50) ? 'selected' : '' ?>>Hiện 50</option>
        </select>

        <button type="submit" style="padding: 6px 12px; cursor:pointer;"><i class="fa fa-search"></i></button>

<button type="button" class="btn-add" onclick="openAddModal()">
    <i class="fa-solid fa-plus"></i> Thêm mới
</button>
    </form>
</div>
        <div class="table-container">
<table>
    <thead>
        <tr>
            <th style="width: 30px;"><input type="checkbox"></th>
            <th style="width: 80px;">Mã sản phẩm</th> <th>Danh mục</th>
            <th>Tiêu đề</th>
            <th>Ảnh</th>
            <th>Hiển thị</th>
            <th>Tác vụ</th>
        </tr>
    </thead>
    <tbody>
        <?php if(!empty($products)): foreach($products as $p): ?>
        <tr>
            <td><input type="checkbox"></td>
            <td style="font-weight: bold; color: #555;">#<?= $p['id'] ?></td> 
            
            <td class="text-left"><?= htmlspecialchars($p['category_name'] ?? 'Chưa phân loại') ?></td>
            <td class="text-left" style="color: #3498db; font-weight: 500;"><?= htmlspecialchars($p['name']) ?></td>
            <td>
                <?php if(!empty($p['image'])): ?>
                   <img src="images/<?= $p['image'] ?>" class="img-thumb">
                <?php else: ?>
                    <i class="fa-solid fa-image" style="color: #ccc; font-size: 24px;"></i>
                <?php endif; ?>
            </td>
            <td>
                <input type="checkbox" <?= ($p['status'] == 1) ? 'checked' : '' ?> onclick="return false;">
            </td>
            <td>
                <button type="button" class="btn-edit" onclick='openEditModal(<?= json_encode($p) ?>)' style="border:none; background:none; color:#e67e22; cursor:pointer;">
    <i class="fa-solid fa-pen-to-square"></i>
</button>
                <a href="javascript:void(0)" class="btn-delete" onclick="if(confirm('Xóa sản phẩm này?')) window.location.href='admin.php?url=delete_product&id=<?= $p['id'] ?>'">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            </td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="7">Không tìm thấy sản phẩm nào phù hợp.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
            
            <div class="pagination" style="margin-top: 20px; display: flex; justify-content: center; gap: 5px;">
                <?php if($page > 1): ?>
                    <a href="admin.php?url=products&page=<?= $page - 1 ?>">« Trước</a>
                <?php endif; ?>

                <?php for($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="admin.php?url=products&page=<?= $i ?>" 
                       style="<?= ($i == $page) ? 'background: #3498db; color: white;' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if($page < $totalPages): ?>
                    <a href="admin.php?url=products&page=<?= $page + 1 ?>">Sau »</a>
                <?php endif; ?>
            </div>

            <p style="text-align: center; font-size: 12px; color: #7f8c8d; margin-top: 10px;">
                Hiển thị trang <?= $page ?> / <?= $totalPages ?> (Tổng cộng <?= $totalProducts ?> sản phẩm)
            </p>
        </div>
    </div>
<div id="productModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle">Thêm sản phẩm mới</h2>
        
        <form action="admin.php?url=save_product" method="POST" enctype="multipart/form-data" id="productForm">
            <input type="hidden" name="id" id="prod_id">
            <input type="hidden" name="current_image" id="prod_current_image">

            <div class="form-group">
                <label>Tên sản phẩm:</label>
                <input type="text" name="name" id="prod_name" required class="form-control">
            </div>

            <div class="form-group">
                <label>Danh mục:</label>
                <select name="category_id" id="prod_category" class="form-control">
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Giá vốn:</label>
                <input type="number" name="gia_von" id="prod_gia_von" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Lợi nhuận (%):</label>
                <input type="number" name="loi_nhuan" id="prod_loi_nhuan" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Ảnh sản phẩm:</label>
                <input type="file" name="image" class="form-control">
                <small id="img_preview_text"></small>
            </div>

            <button type="submit" class="btn-add" style="width: 100%; justify-content: center;">Lưu sản phẩm</button>
        </form>
    </div>
</div>
</body>
<script>
function toggleProductMenu() {
    const submenu = document.getElementById("product-submenu");
    const arrow = document.getElementById("arrow-icon");

    // Thêm hoặc xóa class 'show' để ẩn hiện
    submenu.classList.toggle("show");
    
    // Thêm hoặc xóa class 'rotate' để xoay mũi tên
    arrow.classList.toggle("rotate");
}
// Thêm hàm này vào trong thẻ <script> của bạn
function handleAction() {
    const action = document.getElementsByName('action')[0].value;
    const selectedIds = Array.from(document.querySelectorAll('input[type="checkbox"]:checked'))
                             .filter(cb => cb.value !== 'on') // Loại bỏ checkbox "chọn tất cả"
                             .map(cb => cb.value);

    if (!action || selectedIds.length === 0) {
        alert("Vui lòng chọn tác vụ và ít nhất một sản phẩm!");
        return;
    }

    if (confirm(`Bạn có chắc chắn muốn thực hiện tác vụ này cho ${selectedIds.length} sản phẩm?`)) {
        // Gửi dữ liệu qua ID hoặc sử dụng Form ẩn
        window.location.href = `admin.php?url=bulk_action&type=${action}&ids=${selectedIds.join(',')}`;
    }
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
const modal = document.getElementById("productModal");
const form = document.getElementById("productForm");

// Mở modal để THÊM MỚI
function openAddModal() {
    document.getElementById("modalTitle").innerText = "Thêm sản phẩm mới";
    form.action = "admin.php?url=save_product"; // Đường dẫn lưu mới
    form.reset(); // Xóa sạch dữ liệu cũ
    document.getElementById("prod_id").value = "";
    document.getElementById("img_preview_text").innerText = "";
    modal.style.display = "block";
}

// Mở modal để SỬA (Đổ dữ liệu thật vào)
function openEditModal(prod) {
    document.getElementById("modalTitle").innerText = "Chỉnh sửa sản phẩm";
    form.action = "admin.php?url=update_product"; // Đường dẫn cập nhật
    
    // Đổ dữ liệu vào input
    document.getElementById("prod_id").value = prod.id;
    document.getElementById("prod_name").value = prod.name;
    document.getElementById("prod_category").value = prod.category_id;
    document.getElementById("prod_gia_von").value = prod.gia_von;
    document.getElementById("prod_loi_nhuan").value = prod.loi_nhuan;
    document.getElementById("prod_current_image").value = prod.image;
    document.getElementById("img_preview_text").innerText = "Ảnh hiện tại: " + prod.image;

    modal.style.display = "block";
}

function closeModal() {
    modal.style.display = "none";
}

// Đóng modal khi bấm ra ngoài vùng form
window.onclick = function(event) {
    if (event.target == modal) closeModal();
}
</script>
</html>