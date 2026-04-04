<link rel="stylesheet" href="public/css/checkout.css">
<div class="container checkout-container">
    <form action="index.php?url=process_checkout" method="POST" class="checkout-form">
        <h2 class="section-title">Thông tin giao hàng</h2>
        
        <div class="form-group">
            <label>Họ và tên người nhận</label>
            <input type="text" name="fullname" value="<?= $_SESSION['full_name'] ?>" required>
        </div>

        <div class="form-group">
            <label>Số điện thoại</label>
            <input type="text" name="phone" value="<?= $_SESSION['phone'] ?>" required>
        </div>

        <div class="form-group">
            <label>Địa chỉ nhận bánh (Mặc định hoặc nhập mới)</label>
            <textarea name="address" rows="3" required><?= $_SESSION['address_default'] ?? '' ?></textarea>
        </div>

        <h2 class="section-title" style="margin-top:30px;">Phương thức thanh toán</h2>
        <div class="payment-methods">
            <label class="payment-item">
                <input type="radio" name="payment_method" value="Tiền mặt" checked>
                <span>Tiền mặt (COD)</span>
            </label>
            <label class="payment-item">
                <input type="radio" name="payment_method" value="Chuyển khoản">
                <span>Chuyển khoản (Techcombank: 1903... - Nội dung: Ten + SDT)</span>
            </label>
            <label class="payment-item">
                <input type="radio" name="payment_method" value="Trực tuyến">
                <span>Thanh toán trực tuyến (MoMo/VNPAY)</span>
            </label>
        </div>

        <button type="submit" class="btn-confirm">XÁC NHẬN ĐẶT HÀNG</button>
    </form>

    <div class="order-summary">
        <h3>Tóm tắt đơn hàng</h3>
        <div class="summary-list">
            <?php foreach ($cart_items as $item): ?>
                <div class="summary-item">
                    <img src="img/<?php echo trim($item['image']); ?>" onerror="this.src='img/cake.png'">
                    <div class="item-info">
                        <p><strong><?= $item['name'] ?></strong></p>
                        <p>Số lượng: <?= $item['quantity'] ?></p>
                    </div>
                    <span class="item-price"><?= number_format($item['subtotal'], 0, ',', '.') ?>đ</span>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="summary-total">
            <span>Tổng tiền thanh toán:</span>
            <span class="total-amount"><?= number_format($total_bill, 0, ',', '.') ?>đ</span>
        </div>
    </div>
</div>