<div class="container detail-container">
    <div class="product-detail-left">
        <img src="img/<?php echo trim($product['image']); ?>" 
             alt="<?php echo htmlspecialchars($product['name']); ?>"
             class="product-detail-img"
             onerror="this.src='img/cake.png'">
    </div>

    <div class="product-detail-right">
        <h1 class="product-detail-name"><?php echo $product['name']; ?></h1>
        
        <div class="product-detail-price">
            <?php echo number_format($product['selling_price'], 0, ',', '.'); ?> đ
        </div>
        
        <div class="product-detail-desc">
            <h4>Mô tả sản phẩm</h4>
            <p><?php echo nl2br($product['description']); ?></p>
        </div>

        <div class="quantity-selection">
            <strong>Số lượng:</strong>
            <input type="number" id="quantity" class="quantity-input" value="1" min="1">
        </div>

        <div class="action-buttons">
            <button class="btn-add-cart" onclick="handleCart(<?php echo $product['id']; ?>, 'add')">
                <i class="fa-solid fa-cart-plus"></i> Thêm giỏ hàng
            </button>

            <button class="btn-buy-now" onclick="handleCart(<?php echo $product['id']; ?>, 'buy')">
                Mua ngay
            </button>
        </div>
    </div>
</div>

<script>
function handleCart(productId, action) {
    let qty = document.getElementById('quantity').value;
    
    if(action === 'buy') {
        // Chuyển hướng thẳng tới trang thanh toán kèm thông tin sản phẩm
        window.location.href = `index.php?url=checkout&id=${productId}&qty=${qty}`;
    } else {
        // Gửi tới xử lý thêm vào giỏ hàng (PHP Session)
        window.location.href = `index.php?url=add_to_cart&id=${productId}&qty=${qty}`;
    }
}
</script>