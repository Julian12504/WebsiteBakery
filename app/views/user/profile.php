<?php
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập để xem thông tin!'); window.location.href='index.php?url=login';</script>";
    exit();
}
?>
<link rel="stylesheet" href="public/css/profile.css">

<div class="container profile-container">
    <div class="profile-wrapper">
        
        <div class="profile-sidebar">
            <div class="avatar-circle">
                <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
            </div>
            <h3 class="user-name"><?php echo $_SESSION['full_name']; ?></h3>
            <p class="join-date">Thành viên từ: <?php echo date('d/m/Y'); ?></p>
            <span class="role-badge">
                <?php echo $_SESSION['role'] ?? 'Khách hàng'; ?>
            </span>
        </div>

        <div class="profile-main">
            <h2 class="profile-title">Thông Tin Tài Khoản</h2>
            
            <div class="info-grid">
                <div class="info-item">
                    <label>Tên đăng nhập</label>
                    <p><?php echo $_SESSION['username']; ?></p>
                </div>
                
                <div class="info-item">
                    <label>Họ và Tên</label>
                    <p><?php echo $_SESSION['full_name']; ?></p>
                </div>

                <div class="info-item">
                    <label>Email liên hệ</label>
                    <p><?php echo $_SESSION['email'] ?? 'Chưa cập nhật'; ?></p>
                </div>

                <div class="info-item">
                    <label>Số điện thoại</label>
                    <p><?php echo $_SESSION['phone'] ?? 'Chưa cập nhật'; ?></p>
                </div>
            </div>

            <div class="info-item address-section">
                <label>Địa chỉ mặc định</label>
                <p class="address-text">
                    <?php echo $_SESSION['address_default'] ?? 'Bạn chưa thiết lập địa chỉ giao hàng.'; ?>
                </p>
            </div>

            <div class="profile-actions">
                <a href="javascript:void(0)" class="btn-edit" onclick="openModal()">Chỉnh sửa thông tin</a>
                <a href="index.php?url=logout" onclick="return confirm('Bạn có muốn đăng xuất?')" class="btn-logout-alt">Đăng xuất</a>
            </div>
        </div>
    </div>
</div>
<div class="order-section" style="margin-top: 40px;">
    <h2 class="profile-title">Đơn Mua Của Tôi</h2>
    
    <div class="order-tabs">
        <div class="tab-item active" onclick="showOrder('pending')">Chờ xác nhận</div>
        <div class="tab-item" onclick="showOrder('processing')">Chờ lấy hàng</div>
        <div class="tab-item" onclick="showOrder('shipping')">Chờ giao hàng</div>
        <div class="tab-item" onclick="showOrder('completed')">Đánh giá</div>
    </div>

    <div id="order-content" style="margin-top: 20px;">
        <div class="order-placeholder" style="text-align: center; padding: 40px; color: #999;">
            <i class="fa-solid fa-file-invoice" style="font-size: 50px; margin-bottom: 10px; display: block;"></i>
            <p>Chưa có đơn hàng nào ở trạng thái này.</p>
        </div>
    </div>
</div>

<div id="editProfileModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 class="profile-title">Cập nhật thông tin</h2>
        
        <form action="index.php?url=update_profile" method="POST">
            <div class="info-item" style="margin-bottom: 15px;">
                <label>Họ và Tên</label>
                <input type="text" name="full_name" value="<?php echo $_SESSION['full_name']; ?>" required>
            </div>
            <div class="info-item" style="margin-bottom: 15px;">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo $_SESSION['email'] ?? ''; ?>">
            </div>
            <div class="info-item" style="margin-bottom: 15px;">
                <label>Số điện thoại</label>
                <input type="text" name="phone" value="<?php echo $_SESSION['phone'] ?? ''; ?>">
            </div>
            <div class="info-item" style="margin-bottom: 15px;">
                <label>Địa chỉ</label>
                <textarea name="address_default" rows="3"><?php echo $_SESSION['address_default'] ?? ''; ?></textarea>
            </div>
            <button type="submit" class="role-badge" style="border:none; cursor:pointer; width:100%; padding:12px;">LƯU THAY ĐỔI</button>
        </form>
    </div>
</div>

<script>
function openModal() { document.getElementById("editProfileModal").style.display = "block"; }
function closeModal() { document.getElementById("editProfileModal").style.display = "none"; }
// Đóng modal khi bấm ra ngoài vùng nội dung
window.onclick = function(event) {
    let modal = document.getElementById("editProfileModal");
    if (event.target == modal) modal.style.display = "none";
}
function showOrder(status) {
    // 1. Cập nhật màu sắc cho Tab
    document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
    event.target.classList.add('active');

    // 2. Thay đổi nội dung hiển thị (Sau này chỗ này sẽ dùng AJAX để lấy data thật)
    let content = document.getElementById('order-content');
    
    if(status === 'completed') {
        content.innerHTML = `
            <div class="order-item" style="border-left: 5px solid #2ecc71;">
                <div class="order-info">
                    <p style="font-weight: 600;">Mã đơn: #SH9999</p>
                    <p style="font-size: 14px;">Bánh kem bơ Pháp (x1)</p>
                    <p style="color: #d81b60; font-weight: 600;">350.000đ</p>
                </div>
                <div class="order-status">
                    <a href="#" class="btn-review" style="background: #2ecc71;">Đã giao thành công</a>
                    <a href="#" class="btn-review" style="margin-left:5px;">Đánh giá ngay</a>
                </div>
            </div>`;
    } else {
        content.innerHTML = `
            <div class="order-placeholder" style="text-align: center; padding: 40px; color: #999;">
                <i class="fa-solid fa-file-invoice" style="font-size: 50px; margin-bottom: 10px; display: block;"></i>
                <p>Bạn không có đơn hàng nào ${event.target.innerText.toLowerCase()}.</p>
            </div>`;
    }
}
</script>