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
// Kiểm tra giá trị của biến url trên thanh địa chỉ
$current_url = $_GET['url'] ?? 'home'; 

if ($current_url == 'home'): ?>
    <link rel="stylesheet" href="css/homepage.css"> 

<?php elseif ($current_url == 'product'): ?>
    <link rel="stylesheet" href="css/shoppage.css"> 
<?php elseif ($current_url == 'profile'): ?>
    <link rel="stylesheet" href="css/profile.css"> 
<?php elseif ($current_url == 'detail'): ?>
    <link rel="stylesheet" href="css/detail.css"> 
<?php elseif ($current_url == 'cart'): ?>
    <link rel="stylesheet" href="css/cart.css"> 
<?php endif; ?>

</head>
<body>

<header>
    <div class="container header-content">
        <div class="logo">
            <a href="index.php">
                <img src="public/images/logo.png" alt="Logo" onerror="this.style.display='none'"> 
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

<div class="header-icons" style="display: flex; align-items: center; gap: 15px;">


    <?php if (isset($_SESSION['username'])): ?>
        <div class="user-logged-in" style="display: flex; align-items: center; gap: 12px; border-left: 1px solid #ddd; padding-left: 15px;">
            <span style="font-weight: 600; color: #d81b60; font-size: 14px;">
                Chào, <?php echo explode(' ', $_SESSION['full_name'])[0]; ?>
            </span>
            
            <a href="index.php?url=profile" title="Thông tin cá nhân" style="font-size: 14px; color: #555;">
                <i class="fa-solid fa-address-card"></i>
            </a>

            <a href="index.php?url=logout" 
               onclick="return confirm('Bạn có muốn đăng xuất không?')" 
               title="Đăng xuất" 
               style="color: #ff4d4d; font-size: 14px;">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        </div>
    <?php else: ?>
        <a href="index.php?url=login"><i class="fa-solid fa-user"></i></a>
    <?php endif; ?>

    <a href="index.php?url=cart" class="cart-icon" style="position: relative;">
        <i class="fa-solid fa-basket-shopping"></i>
        <span class="cart-count" style="position: absolute; top: -8px; right: -10px; background: #d81b60; color: white; border-radius: 50%; padding: 2px 5px; font-size: 10px;">0</span>
    </a>
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