<div class="container cart-container">
    <div class="cart-header">
        <h2>Giỏ hàng của bạn (<?php echo count($cart_data); ?> sản phẩm)</h2>
    </div>

    <div class="cart-main">
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                    <th style="text-align: center;">Xóa</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_data as $item): ?>
                <tr>
                    <td>
                        <div class="cart-item-info">
                            <img src="img/<?php echo trim($item['image']); ?>" onerror="this.src='img/cake.png'">
                            <a href="index.php?url=detail&id=<?php echo $item['id']; ?>" class="cart-item-name">
                                <?php echo $item['name']; ?>
                            </a>
                        </div>
                    </td>
                    <td class="price-col"><?php echo number_format($item['selling_price'], 0, ',', '.'); ?>đ</td>
                    <td>
                        <input type="number" value="<?php echo $item['quantity']; ?>" class="qty-input" min="1">
                    </td>
                    <td class="subtotal-col"><?php echo number_format($item['subtotal'], 0, ',', '.'); ?>đ</td>
                    <td style="text-align: center;">
                        <a href="index.php?url=remove_cart&id=<?php echo $item['id']; ?>" class="btn-remove">
                            <i class="fa-solid fa-trash-can"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <aside class="cart-sidebar">
        <div class="summary-row">
            <span>Tạm tính:</span>
            <span><?php echo number_format($total_bill, 0, ',', '.'); ?>đ</span>
        </div>
        <div class="summary-row">
            <span>Phí vận chuyển:</span>
            <span></span>
        </div>
        <div class="summary-row summary-total">
            <span>Tổng cộng:</span>
            <span><?php echo number_format($total_bill, 0, ',', '.'); ?>đ</span>
        </div>
        
        <a href="index.php?url=checkout" class="btn-checkout">TIẾN HÀNH THANH TOÁN</a>
        <a href="index.php?url=product" class="continue-shopping">← Tiếp tục mua sắm</a>
    </aside>
</div>