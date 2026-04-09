<?php
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập để xem thông tin!'); window.location.href='index.php?url=login';</script>";
    exit();
}

// Hàm render đơn hàng đã được đánh giá
function renderReviewedOrders($orders) {
    if (empty($orders)) {
        echo '<div class="order-placeholder" style="text-align: center; padding: 40px; color: #999;">
                <i class="fa-solid fa-check-circle" style="font-size: 50px; margin-bottom: 10px; display: block;"></i>
                <p>Bạn chưa đánh giá đơn hàng nào.</p>
              </div>';
    } else {
        foreach ($orders as $order) {
            ?>
            <div class="order-item" style="border-left: 5px solid #9b59b6; background: #fff; padding: 15px; margin-bottom: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center;">
                <div class="order-info">
                    <p style="font-weight: 600; margin-bottom: 5px;">Mã đơn: #DH<?= $order['id'] ?></p>
                    <p style="font-size: 14px; color: #666;">Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></p>
                    <p style="color: #d81b60; font-weight: 600; margin-top: 5px;">Tổng tiền: <?= number_format($order['total_amount'], 0, ',', '.') ?>đ</p>
                    <p style="font-size: 13px; color: #27ae60; margin-top: 5px;"><i class="fa-solid fa-check"></i> Đã đánh giá</p>
                </div>
                <div class="order-status">
                    <a href="index.php?url=order_detail&id=<?= $order['id'] ?>" style="display: block; margin-top: 10px; color: #007bff; font-size: 13px;">Xem chi tiết</a>
                </div>
            </div>
            <?php
        }
    }
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

// Hàm render đơn hàng đã giao (có thể đánh giá)
function renderDeliveredOrders($orders) {
    if (empty($orders)) {
        echo '<div class="order-placeholder" style="text-align: center; padding: 40px; color: #999;">
                <i class="fa-solid fa-truck" style="font-size: 50px; margin-bottom: 10px; display: block;"></i>
                <p>Không có đơn hàng nào đã giao.</p>
              </div>';
    } else {
        foreach ($orders as $order) {
            ?>
            <div class="order-item" style="border-left: 5px solid #27ae60; background: #fff; padding: 15px; margin-bottom: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                <div class="order-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <div>
                        <p style="font-weight: 600; margin: 0;">Mã đơn: #DH<?= $order['id'] ?></p>
                        <p style="font-size: 14px; color: #666; margin: 5px 0;">Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></p>
                        <p style="color: #d81b60; font-weight: 600; margin: 5px 0;">Tổng tiền: <?= number_format($order['total_amount'], 0, ',', '.') ?>đ</p>
                    </div>
                    <button onclick="openReviewModal(<?= $order['id'] ?>)" style="background: #27ae60; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer;">
                        <i class="fa-solid fa-star"></i> Đánh giá
                    </button>
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
            <!-- <span class="role-badge"><?= $_SESSION['role'] == 1 ? 'Admin' : 'Khách hàng'; ?></span> -->
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
        <div class="tab-item" onclick="switchOrderTab('delivered', this)">Đã giao</div>
        <div class="tab-item" onclick="switchOrderTab('completed', this)">Đánh giá</div>
        <div class="tab-item" onclick="switchOrderTab('cancelled', this)">Đã hủy</div>
    </div>

    <div id="order-content">
        <div id="tab-pending" class="order-group"><?php renderOrderList($pending_orders, "chờ xác nhận"); ?></div>
        <div id="tab-processing" class="order-group" style="display:none;"><?php renderOrderList($processing_orders, "chờ lấy hàng"); ?></div>
        <div id="tab-shipping" class="order-group" style="display:none;"><?php renderOrderList($shipping_orders, "chờ giao hàng"); ?></div>
        <div id="tab-delivered" class="order-group" style="display:none;"><?php renderDeliveredOrders($delivered_orders); ?></div>
        <div id="tab-completed" class="order-group" style="display:none;"><?php renderReviewedOrders($completed_orders); ?></div>
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
            <input type="email" name="email" readonly value="<?= htmlspecialchars($_SESSION['email'] ?? '', ENT_QUOTES); ?>" style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:6px; background:#f7f7f7; color:#555; cursor:not-allowed;">
            <label style="font-weight:600; font-size:14px; color:#333;">Số điện thoại</label>
            <input type="text" name="phone" readonly value="<?= htmlspecialchars($_SESSION['phone'] ?? '', ENT_QUOTES); ?>" style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:6px; background:#f7f7f7; color:#555; cursor:not-allowed;">
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

// Hàm chuyển đổi tab đơn hàng
function switchOrderTab(tabName, element) {
    // Ẩn tất cả tab content
    document.querySelectorAll('.order-group').forEach(group => {
        group.style.display = 'none';
    });

    // Bỏ active class khỏi tất cả tab
    document.querySelectorAll('.tab-item').forEach(tab => {
        tab.classList.remove('active');
    });

    // Hiển thị tab được chọn
    document.getElementById('tab-' + tabName).style.display = 'block';
    element.classList.add('active');
}

// Hàm mở modal đánh giá
function openReviewModal(orderId) {
    document.getElementById('reviewModal').style.display = 'block';
    document.getElementById('reviewOrderId').value = orderId;
    // Load chi tiết đơn hàng để hiển thị sản phẩm cần đánh giá
    loadOrderItemsForReview(orderId);
}

// Hàm đóng modal
function closeReviewModal() {
    document.getElementById('reviewModal').style.display = 'none';
}

// Hàm load chi tiết đơn hàng
function loadOrderItemsForReview(orderId) {
    fetch('index.php?url=get_order_items&order_id=' + orderId)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('reviewItemsContainer');
            container.innerHTML = '';

            data.forEach(item => {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'review-item';
                itemDiv.innerHTML = `
                    <div class="product-info">
                        <img src="img/${item.image}" alt="${item.name}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; onerror=\"this.src='img/cake.png'\"">
                        <div>
                            <h4>${item.name}</h4>
                            <p>Số lượng: ${item.quantity}</p>
                        </div>
                    </div>
                    <div class="rating-section">
                        <div class="stars" data-product-id="${item.product_id}">
                            <i class="fa-regular fa-star" data-rating="1"></i>
                            <i class="fa-regular fa-star" data-rating="2"></i>
                            <i class="fa-regular fa-star" data-rating="3"></i>
                            <i class="fa-regular fa-star" data-rating="4"></i>
                            <i class="fa-regular fa-star" data-rating="5"></i>
                        </div>
                        <textarea placeholder="Nhập đánh giá của bạn..." class="review-comment" data-product-id="${item.product_id}"></textarea>
                    </div>
                `;
                container.appendChild(itemDiv);
            });

            // Thêm event listener cho các ngôi sao
            document.querySelectorAll('.stars i').forEach(star => {
                star.addEventListener('click', function() {
                    const rating = this.getAttribute('data-rating');
                    const starsContainer = this.parentElement;
                    const productId = starsContainer.getAttribute('data-product-id');

                    // Reset all stars
                    starsContainer.querySelectorAll('i').forEach(s => {
                        s.className = 'fa-regular fa-star';
                    });

                    // Fill selected stars
                    for (let i = 1; i <= rating; i++) {
                        starsContainer.querySelector(`i[data-rating="${i}"]`).className = 'fa-solid fa-star';
                    }

                    // Store rating
                    starsContainer.setAttribute('data-selected-rating', rating);
                });
            });
        })
        .catch(error => console.error('Error loading order items:', error));
}

// Hàm gửi đánh giá
function submitReview() {
    const orderId = document.getElementById('reviewOrderId').value;
    const reviews = [];

    document.querySelectorAll('.review-item').forEach(item => {
        const productId = item.querySelector('.stars').getAttribute('data-product-id');
        const rating = item.querySelector('.stars').getAttribute('data-selected-rating') || 0;
        const comment = item.querySelector('.review-comment').value.trim();

        if (rating > 0) {
            reviews.push({
                product_id: productId,
                rating: rating,
                comment: comment
            });
        }
    });

    if (reviews.length === 0) {
        alert('Vui lòng đánh giá ít nhất một sản phẩm!');
        return;
    }

    fetch('index.php?url=submit_review', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            order_id: orderId,
            reviews: reviews
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Cảm ơn bạn đã đánh giá!');
            closeReviewModal();
            location.reload();
        } else {
            alert('Có lỗi xảy ra: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error submitting review:', error);
        alert('Có lỗi xảy ra khi gửi đánh giá!');
    });
}
</script>

<!-- Modal đánh giá -->
<div id="reviewModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div class="modal-content" style="background-color: #fff; margin: 5% auto; padding: 20px; border-radius: 12px; width: 90%; max-width: 600px; max-height: 80vh; overflow-y: auto;">
        <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; color: #d81b60;">Đánh giá sản phẩm</h3>
            <span onclick="closeReviewModal()" style="cursor: pointer; font-size: 24px; color: #999;">&times;</span>
        </div>

        <input type="hidden" id="reviewOrderId">

        <div id="reviewItemsContainer">
            <!-- Các item đánh giá sẽ được load vào đây -->
        </div>

        <div class="modal-actions" style="text-align: center; margin-top: 20px;">
            <button onclick="submitReview()" style="background: #d81b60; color: white; border: none; padding: 12px 30px; border-radius: 8px; cursor: pointer; font-size: 16px;">
                Gửi đánh giá
            </button>
            <button onclick="closeReviewModal()" style="background: #6c757d; color: white; border: none; padding: 12px 30px; border-radius: 8px; cursor: pointer; font-size: 16px; margin-left: 10px;">
                Hủy
            </button>
        </div>
    </div>
</div>

<style>
.review-item {
    display: flex;
    gap: 15px;
    padding: 15px;
    border: 1px solid #eee;
    border-radius: 8px;
    margin-bottom: 15px;
    background: #f9f9f9;
}

.product-info {
    display: flex;
    gap: 10px;
    flex: 1;
}

.product-info h4 {
    margin: 0 0 5px 0;
    font-size: 16px;
}

.product-info p {
    margin: 0;
    color: #666;
    font-size: 14px;
}

.rating-section {
    flex: 1;
}

.stars {
    margin-bottom: 10px;
}

.stars i {
    color: #ffc107;
    font-size: 20px;
    cursor: pointer;
    margin-right: 5px;
}

.stars i:hover {
    transform: scale(1.1);
}

.review-comment {
    width: 100%;
    min-height: 60px;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    resize: vertical;
}

.tab-item {
    cursor: pointer;
    padding: 10px 15px;
    border-bottom: 2px solid transparent;
    transition: all 0.3s;
}

.tab-item.active {
    border-bottom-color: #d81b60;
    color: #d81b60;
    font-weight: 600;
}

.tab-item:hover {
    color: #d81b60;
}
</style>