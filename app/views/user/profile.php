<?php
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập để xem thông tin!'); window.location.href='index.php?url=login';</script>";
    exit();
}

// Hàm render danh sách đơn hàng để tái sử dụng
function renderOrderList($orders, $statusLabel) {
    if (empty($orders)) {
        echo '<div class="order-placeholder" style="text-align: center; padding: 40px; color: #999;">
                <i class="fa-solid fa-file-invoice" style="font-size: 50px; margin-bottom: 10px; display: block;"></i>
                <p>Bạn không có đơn hàng nào ' . $statusLabel . '.</p>
              </div>';
    } else {
        foreach ($orders as $order) {
            ?>
            <div class="order-item" style="border-left: 5px solid #d81b60; background: #fff; padding: 15px; margin-bottom: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center;">
                <div class="order-info">
                    <p style="font-weight: 600; margin-bottom: 5px;">Mã đơn: #DH<?= $order['id'] ?></p>
                    <p style="font-size: 14px; color: #666;">Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></p>
                    <p style="color: #d81b60; font-weight: 600; margin-top: 5px;">Tổng tiền: <?= number_format($order['total_amount'], 0, ',', '.') ?>đ</p>
                </div>
                <div class="order-status">
                    <!-- <span style="padding: 5px 12px; border-radius: 20px; font-size: 12px; background: #fff3e0; color: #ef6c00;">
                        <?= $statusLabel ?>
                    </span> -->
                    <a href="index.php?url=order_detail&id=<?= $order['id'] ?>" style="display: block; margin-top: 10px; color: #007bff; font-size: 13px;">Xem chi tiết</a>
                </div>
            </div>
            <?php
        }
    }
}
?>

<link rel="stylesheet" href="public/css/profile.css">

<div class="container profile-container">
    <div class="profile-wrapper">
        <div class="profile-sidebar">
            <div class="avatar-circle">
                <?= strtoupper(substr($_SESSION['username'], 0, 1)); ?>
            </div>
            <h3 class="user-name"><?= $_SESSION['full_name']; ?></h3>
            <p class="join-date">Thành viên từ: <?= date('d/m/Y'); ?></p>
            <span class="role-badge"><?= $_SESSION['role'] == 1 ? 'Admin' : 'Khách hàng'; ?></span>
        </div>

        <div class="profile-main">
            <h2 class="profile-title">Thông Tin Tài Khoản</h2>
            <div class="info-grid">
                <div class="info-item"><label>Tên đăng nhập</label><p><?= $_SESSION['username']; ?></p></div>
                <div class="info-item"><label>Họ và Tên</label><p><?= $_SESSION['full_name']; ?></p></div>
                <div class="info-item"><label>Email liên hệ</label><p><?= $_SESSION['email'] ?? 'Chưa cập nhật'; ?></p></div>
                <div class="info-item"><label>Số điện thoại</label><p><?= $_SESSION['phone'] ?? 'Chưa cập nhật'; ?></p></div>
            </div>
            <div class="info-item address-section">
                <label>Địa chỉ mặc định</label>
                <p class="address-text"><?= $_SESSION['address_default'] ?? 'Bạn chưa thiết lập địa chỉ giao hàng.'; ?></p>
            </div>
            <div class="profile-actions">
                <a href="javascript:void(0)" class="btn-edit" onclick="openModal()">Chỉnh sửa thông tin</a>
                <a href="index.php?url=logout" onclick="return confirm('Bạn có muốn đăng xuất?')" class="btn-logout-alt">Đăng xuất</a>
            </div>
        </div>
    </div>
</div>

<div class="order-section" style="margin-top: 40px; background: #fff; padding: 20px; border-radius: 12px;">
    <h2 class="profile-title">Đơn Mua Của Tôi</h2>
    
    <div class="order-tabs" style="display: flex; gap: 20px; border-bottom: 1px solid #eee; margin-bottom: 20px;">
        <div class="tab-item active" onclick="switchOrderTab('pending', this)">Chờ xác nhận</div>
        <div class="tab-item" onclick="switchOrderTab('processing', this)">Chờ lấy hàng</div>
        <div class="tab-item" onclick="switchOrderTab('shipping', this)">Chờ giao hàng</div>
        <div class="tab-item" onclick="switchOrderTab('completed', this)">Đánh giá</div>
        <div class="tab-item" onclick="switchOrderTab('cancelled', this)">Đã hủy</div>
    </div>

    <div id="order-content">
        <div id="tab-pending" class="order-group"><?php renderOrderList($pending_orders, "chờ xác nhận"); ?></div>
        <div id="tab-processing" class="order-group" style="display:none;"><?php renderOrderList($processing_orders, "chờ lấy hàng"); ?></div>
        <div id="tab-shipping" class="order-group" style="display:none;"><?php renderOrderList($shipping_orders, "chờ giao hàng"); ?></div>
        <div id="tab-completed" class="order-group" style="display:none;"><?php renderOrderList($completed_orders, "đã hoàn thành"); ?></div>
<div id="tab-cancelled" class="order-group" style="display:none;">
    <?php if (empty($cancelled_orders)): ?>
        <p style="text-align:center; padding:20px; color:#999;">Không có đơn hàng nào bị hủy.</p>
    <?php else: ?>
        <?php foreach ($cancelled_orders as $order): ?>
            <div class="order-item" style="border-left: 5px solid #95a5a6; background: #f9f9f9; padding: 15px; margin-bottom: 15px; display: flex; justify-content: space-between;">
                <div>
                    <p><strong>Mã đơn: #DH<?= $order['id'] ?></strong></p>
                    <p style="font-size: 13px; color: #e74c3c;">Lý do: <?= $order['cancel_reason'] ?? 'Không có lý do' ?></p>
                </div>
                <div>
                    <a href="index.php?url=reorder&id=<?= $order['id'] ?>" 
                       style="background: #d81b60; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none; font-size: 13px;">
                       Mua lại
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
    </div>
</div>

<div id="editProfileModal" style="display:none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: #fff; width: 100%; max-width: 520px; margin: 30px auto; border-radius: 12px; overflow: hidden; box-shadow: 0 15px 40px rgba(0,0,0,0.2); position: relative;">
        <div style="padding: 20px 24px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin:0; font-size: 20px;">Chỉnh sửa thông tin</h3>
            <button type="button" onclick="closeModal()" style="border:none; background:none; font-size:24px; line-height:1; cursor:pointer; color:#333;">&times;</button>
        </div>
        <form action="index.php?url=update_profile" method="POST" style="padding: 20px; display: grid; gap: 15px;">
            <label style="font-weight:600; font-size:14px; color:#333;">Họ và tên</label>
            <input type="text" name="full_name" required value="<?= htmlspecialchars($_SESSION['full_name'] ?? '', ENT_QUOTES); ?>" style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:6px;">
            <label style="font-weight:600; font-size:14px; color:#333;">Email</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($_SESSION['email'] ?? '', ENT_QUOTES); ?>" style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:6px;">
            <label style="font-weight:600; font-size:14px; color:#333;">Số điện thoại</label>
            <input type="text" name="phone" required value="<?= htmlspecialchars($_SESSION['phone'] ?? '', ENT_QUOTES); ?>" style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:6px;">
            <label style="font-weight:600; font-size:14px; color:#333;">Địa chỉ giao hàng</label>
            <textarea name="address_default" rows="3" required style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:6px;"><?= htmlspecialchars($_SESSION['address_default'] ?? '', ENT_QUOTES); ?></textarea>
            <div style="display:flex; gap:12px; justify-content:flex-end; margin-top:10px;">
                <button type="button" onclick="closeModal()" style="padding:10px 18px; border:1px solid #ccc; background:#fff; border-radius:8px; cursor:pointer;">Hủy</button>
                <button type="submit" style="padding:10px 18px; border:none; background:#d81b60; color:#fff; border-radius:8px; cursor:pointer;">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<script>
function switchOrderTab(status, element) {
    // 1. Cập nhật class active cho tab
    document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
    element.classList.add('active');

    // 2. Ẩn tất cả danh sách, hiện danh sách tương ứng
    document.querySelectorAll('.order-group').forEach(group => group.style.display = 'none');
    document.getElementById('tab-' + status).style.display = 'block';
}

function openModal() { document.getElementById("editProfileModal").style.display = "flex"; }
function closeModal() { document.getElementById("editProfileModal").style.display = "none"; }
</script>