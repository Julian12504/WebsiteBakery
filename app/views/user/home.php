<div class="home-container">
    <!-- HERO BANNER -->
    <section class="home-hero">
        <div class="hero-overlay">
            <h1>Sweet Home</h1>
            <p>Tiệm Bánh Ngọt Ngào</p>
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <section class="features-section">
        <div class="features-container">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fa-solid fa-leaf"></i>
                </div>
                <h3>Nguyên Liệu Sạch</h3>
                <p>Sử dụng nguyên liệu nhập khẩu cao cấp, 100% tự nhiên</p>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <h3>Làm Tươi Mỗi Ngày</h3>
                <p>Bánh được làm tươi ngày để đảm bảo chất lượng tốt nhất</p>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fa-solid fa-gift"></i>
                </div>
                <h3>Đặc Biệt Cho Lễ</h3>
                <p>Thiết kế bánh theo ý muốn cho các dịp lễ quan trọng</p>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fa-solid fa-truck"></i>
                </div>
                <h3>Giao Hàng Nhanh</h3>
                <p>Giao hàng miễn phí toàn TP.HCM, nhanh chóng & an toàn</p>
            </div>
        </div>
    </section>

    <!-- PRODUCTS SECTION -->
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
                    <div class="showcase-item">
                        <a href="index.php?url=detail&id=<?php echo $product['id']; ?>" class="cake-card" style="background-image: url('img/<?php echo htmlspecialchars(trim($product['image'])); ?>'); background-size: cover; background-position: center;"></a>
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p><?php echo number_format($product['selling_price'], 0, ',', '.'); ?> đ</p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="padding: 40px; text-align: center; color: #666; width: 100%;">
                    Không có sản phẩm nào để hiển thị.
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>