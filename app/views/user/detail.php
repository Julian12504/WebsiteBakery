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
            <strong>Số lượng còn:</strong>
            <span class="stock-quantity"><?php echo $product['stock']; ?></span>
        </div>
        <input type="number" id="quantity" class="quantity-input" value="1" min="1" max="<?php echo $product['stock']; ?>">
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

<!-- Phần đánh giá sản phẩm -->
<div class="reviews-section" style="margin-top: 40px; background: #fff; padding: 20px; border-radius: 12px;">
    <h3 style="color: #d81b60; margin-bottom: 20px;">Đánh giá sản phẩm</h3>

    <?php if (!empty($reviews)): ?>
        <div class="reviews-summary" style="display: flex; align-items: center; gap: 20px; margin-bottom: 20px; padding: 15px; background: #f9f9f9; border-radius: 8px;">
            <div class="rating-overview">
                <div style="font-size: 24px; font-weight: bold; color: #d81b60;">
                    <?= $rating_stats['average'] ?>/5
                </div>
                <div class="stars" style="margin: 5px 0;">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fa-solid fa-star" style="color: <?= $i <= $rating_stats['average'] ? '#ffc107' : '#ddd' ?>;"></i>
                    <?php endfor; ?>
                </div>
                <div style="font-size: 14px; color: #666;">
                    (<?= $rating_stats['total'] ?> đánh giá)
                </div>
            </div>
        </div>

        <div class="reviews-list">
            <?php foreach ($reviews as $review): ?>
                <div class="review-item" style="border-bottom: 1px solid #eee; padding: 15px 0;">
                    <div class="review-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <div class="reviewer-info">
                            <strong style="color: #d81b60;"><?= htmlspecialchars($review['full_name']) ?></strong>
                            <span style="font-size: 12px; color: #666; margin-left: 10px;">
                                <?= date('d/m/Y H:i', strtotime($review['created_at'])) ?>
                            </span>
                        </div>
                        <div class="review-rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fa-solid fa-star" style="color: <?= $i <= $review['rating'] ? '#ffc107' : '#ddd' ?>; font-size: 14px;"></i>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <?php if (!empty($review['comment'])): ?>
                        <div class="review-comment" style="color: #555; line-height: 1.5;">
                            <?= nl2br(htmlspecialchars($review['comment'])) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-reviews" style="text-align: center; padding: 40px; color: #999;">
            <i class="fa-solid fa-comments" style="font-size: 50px; margin-bottom: 10px; display: block;"></i>
            <p>Chưa có đánh giá nào cho sản phẩm này.</p>
        </div>
    <?php endif; ?>
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