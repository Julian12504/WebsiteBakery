<div class="home-container">
    <section class="home-section">
        <div class="section-header">
            <h2>Sản phẩm mới</h2>
            <div class="slider-nav">
                <button class="nav-btn"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="nav-btn"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
        </div>

        <div class="product-grid">
            <?php if (!empty($new_products)): ?>
                <?php foreach ($new_products as $product): ?>
                    <a href="index.php?url=detail&id=<?php echo $product['id']; ?>" class="cake-card">
                        <div class="cake-img-wrapper">
                            <img src="img/<?php echo htmlspecialchars(trim($product['image'])); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>
                        <div class="cake-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="cake-price"><?php echo number_format($product['selling_price'], 0, ',', '.'); ?> đ</p>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="padding: 40px; text-align: center; color: #666; width: 100%;">
                    Không có sản phẩm nào để hiển thị.
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>