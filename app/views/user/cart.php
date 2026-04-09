<div class="container cart-container">
    <div class="cart-header">
        <h2>Giỏ hàng của bạn (<?php echo count($cart_data); ?> sản phẩm)</h2>
    </div>

    <div class="cart-main">
        <form action="index.php?url=update_cart" method="POST">
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
                        <input type="number" name="qty[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" class="qty-input" min="1" max="<?php echo $item['stock']; ?>" data-price="<?php echo $item['selling_price']; ?>" data-stock="<?php echo $item['stock']; ?>" title="Tối đa <?php echo $item['stock']; ?>">
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
        <div style="margin-top: 20px; text-align: right;">
            <button type="submit" class="btn-buy" style="width: auto; padding: 12px 24px;">Cập nhật giỏ hàng</button>
        </div>
        </form>
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

<script>
(function() {
    const qtyInputs = document.querySelectorAll('.qty-input');
    const totalBillEl = document.querySelector('.summary-total span');

    function formatCurrency(value) {
        return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + ' đ';
    }

    function recalculate() {
        let total = 0;
        document.querySelectorAll('tbody tr').forEach(row => {
            const input = row.querySelector('.qty-input');
            const subtotalCell = row.querySelector('.subtotal-col');
            if (!input || !subtotalCell) return;

            const price = parseInt(input.dataset.price, 10) || 0;
            const qty = parseInt(input.value, 10) || 0;
            const subtotal = price * qty;
            subtotalCell.textContent = formatCurrency(subtotal);
            total += subtotal;
        });
        if (totalBillEl) {
            totalBillEl.textContent = formatCurrency(total);
        }
    }

    qtyInputs.forEach(input => {
        input.addEventListener('input', () => {
            let value = parseInt(input.value, 10);
            const maxStock = parseInt(input.dataset.stock, 10);
            if (isNaN(value) || value < 1) {
                value = 1;
            }
            if (!isNaN(maxStock) && value > maxStock) {
                value = maxStock;
                alert('Số lượng tối đa là ' + maxStock + ' sản phẩm.');
            }
            input.value = value;
            recalculate();
        });
    });
})();
</script>