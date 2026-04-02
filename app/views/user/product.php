

<div class="container shop-container">
    
<aside class="filter-sidebar">
    <form action="index.php" method="GET">
        <input type="hidden" name="url" value="product">
        
        <div class="filter-group">
            <h4>PHÂN LOẠI</h4>
            <select name="category">
                <option value="">Tất cả bánh</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" 
                        <?php echo (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : ''; ?>>
                        <?php echo $cat['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn-buy" style="width: 100%;">ÁP DỤNG</button>
    </form>
</aside>

<main>
    <div class="products-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 25px; align-items: start;">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $row): ?>
                <div class="product-item" style="min-height: 380px; display: flex; flex-direction: column;">
                <a href="index.php?url=detail&id=<?php echo $row['id']; ?>" style="display: block; height: 200px; background: #f5f5f5; overflow: hidden; position: relative;">
    <img src="img/<?php echo trim($row['image']); ?>" class="product-img" style="width: 100%; height: 100%; object-fit: cover; display: block;" onerror="this.src='img/cake.png'">
</a>
                    <div class="product-info" style="flex-grow: 1; padding: 15px; display: flex; flex-direction: column; justify-content: space-between;">
                        <div class="name-price">
                            <span class="product-name"><?php echo $row['name']; ?></span>
                            <div class="product-price"><?php echo number_format($row['selling_price'], 0, ',', '.'); ?> đ</div>
                        </div>
                        <button class="btn-buy"><i class="fa-solid fa-cart-shopping"></i> Thêm giỏ hàng</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php if ($total_pages > 1): ?>
    <div class="pagination" style="display: flex; justify-content: center; gap: 10px; margin-top: 40px;">
        <?php if ($page > 1): ?>
            <a href="index.php?url=product&page=<?php echo $page - 1; ?>&category=<?php echo $category; ?>" class="page-link">&laquo;</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="index.php?url=product&page=<?php echo $i; ?>&category=<?php echo $category; ?>" 
               class="page-link <?php echo ($i == $page) ? 'active' : ''; ?>">
               <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="index.php?url=product&page=<?php echo $page + 1; ?>&category=<?php echo $category; ?>" class="page-link">&raquo;</a>
        <?php endif; ?>
    </div>
<?php endif; ?>
</main>
</div>
