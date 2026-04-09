<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sweet Home - Tiệm Bánh Thủ Công</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css"> 

    <?php 
    $current_url = $_GET['url'] ?? 'home'; 
   
    if ($current_url == 'home'): ?>
        <link rel="stylesheet" href="css/homepage.css">
<?php elseif ($current_url == 'product'): ?>
    <link rel="stylesheet" href="css/shoppage.css"> 
<?php elseif ($current_url == 'checkout'): ?>
    <link rel="stylesheet" href="css/checkout.css"> 
<?php elseif ($current_url == 'profile'): ?>
    <link rel="stylesheet" href="css/profile.css"> 
<?php elseif ($current_url == 'detail'): ?>
    <link rel="stylesheet" href="css/detail.css"> 
<?php elseif ($current_url == 'cart'): ?>
    <link rel="stylesheet" href="css/cart.css">
<?php elseif ($current_url == 'about'): ?>
    <link rel="stylesheet" href="css/about.css">
<?php elseif ($current_url == 'contact'): ?>
    <link rel="stylesheet" href="css/contact.css">
<?php endif; ?>

</head>
<body>

<header>
    <div class="container header-content">
        <div class="logo">
            <a href="index.php">
                <img src="images/logo.png" alt="Logo" onerror="this.style.display='none'"> 
                <span>SWEET HOME</span>
            </a>
        </div>

        <nav>
            <ul>
                <li><a href="index.php">Trang chủ</a></li>
                <li><a href="index.php?url=product">Cửa hàng</a></li>
                <li><a href="index.php?url=about">Giới thiệu</a></li>
                <li><a href="index.php?url=contact">Liên hệ</a></li>
            </ul>
        </nav>

<div class="header-icons" style="display: flex; align-items: flex-start; gap: 20px;">

    <!-- Tài khoản -->
    <div class="icon-box" style="display: flex; flex-direction: column; align-items: center; gap: 4px; cursor: pointer;">
        <?php if (isset($_SESSION['username'])): ?>
            <a href="index.php?url=profile" title="Quản lý tài khoản" style="font-size: 18px; color: #999;">
                <i class="fa-solid fa-user-circle"></i>
            </a>
            <span style="font-size: 11px; color: #666;">Tài khoản</span>
        <?php else: ?>
            <a href="index.php?url=login" style="font-size: 18px; color: #999;">
                <i class="fa-solid fa-user"></i>
            </a>
            <span style="font-size: 11px; color: #666;">Tài khoản</span>
        <?php endif; ?>
    </div>

    <!-- Đăng xuất -->
    <?php if (isset($_SESSION['username'])): ?>
        <div class="icon-box" style="display: flex; flex-direction: column; align-items: center; gap: 4px; cursor: pointer;">
            <a href="index.php?url=logout" 
               onclick="return confirm('Bạn có muốn đăng xuất không?')" 
               title="Đăng xuất" 
               style="font-size: 18px; color: #999;">
                <i class="fa-solid fa-sign-out-alt"></i>
            </a>
            <span style="font-size: 11px; color: #666;">Đăng xuất</span>
        </div>
    <?php endif; ?>

    <!-- Giỏ hàng -->
    <div class="icon-box" style="display: flex; flex-direction: column; align-items: center; gap: 4px; position: relative;">
        <a href="index.php?url=cart" class="cart-icon" style="font-size: 18px; color: #999; position: relative;">
            <i class="fa-solid fa-shopping-cart"></i>
            <span class="cart-count" style="position: absolute; top: -10px; right: -24px; background: #d81b60; color: white; border-radius: 50%; width: 10px; height: 15px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                <?php 
                    $cart_count = 0;
                    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                        foreach ($_SESSION['cart'] as $qty) {
                            $cart_count += $qty;
                        }
                    }
                    echo $cart_count;
                ?>
            </span>
        </a>
        <span style="font-size: 11px; color: #666;">Giỏ hàng</span>
    </div>
</div>

<style>
.user-menu:hover .dropdown-content { display: block; }
.dropdown-content {
    display: none;
    position: absolute;
    right: 0;
    background-color: white;
    min-width: 160px;
    box-shadow: 0px 8px 16px rgba(0,0,0,0.1);
    z-index: 1000;
    border-radius: 8px;
    padding: 10px 0;
    list-style: none;
    text-align: left;
}
.dropdown-content li a {
    color: #333;
    padding: 8px 15px;
    display: block;
    text-decoration: none;
    font-size: 14px;
}
.dropdown-content li a:hover { background-color: #fce4ec; color: #d81b60; }
</style>
    </div>
</header>