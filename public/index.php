<?php
// 1. Khởi tạo session
session_start();

// 2. NHÚNG CÁC FILE QUAN TRỌNG (Phải nhúng trước khi sử dụng)
require_once '../config/database.php';
require_once '../app/models/Product.php';
require_once '../app/models/User.php';
include_once '../app/models/Category.php';
// 3. KHỞI TẠO ĐỐI TƯỢNG (Bước này giải quyết lỗi Fatal Error của bạn)
$database = new Database();
$db = $database->getConnection();

// Tạo ra các "thợ làm việc" từ Model
$productModel = new Product($db);
$userModel = new User($db); // Đã có $userModel, không còn bị null nữa!
$categoryModel = new Category($db);
// 4. Lấy tham số 'url'
$url = $_GET['url'] ?? 'home';

// 5. HIỂN THỊ HEADER (Trừ trang login và register để giống cái ảnh hồng của bạn)
if ($url !== 'login' && $url !== 'register' && $url !== 'checklogin') {
    if (file_exists('../includes/header.php')) {
        include '../includes/header.php';
    }
    echo '<main>'; 
}

// 6. HỆ THỐNG ĐIỀU HƯỚNG (ROUTER)
switch ($url) {
    case 'home':
        include '../app/views/user/home.php';
        break;

case 'product':
    $limit = 6; // Số bánh trên mỗi trang (ví dụ 8 cái)
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $from = ($page - 1) * $limit;
    $category = $_GET['category'] ?? '';

    // Lấy data
    $products = $productModel->getProductsPaged($from, $limit, $category);
    $total_products = $productModel->countAll($category);
    $total_pages = ceil($total_products / $limit);
    
    $categories = $categoryModel->getAllCategories();
    include '../app/views/user/product.php';
    break;
    case 'detail':
    $id = $_GET['id'] ?? 0;
    $product = $productModel->getProductById($id);
    
    if (!$product) {
        echo "Sản phẩm không tồn tại!";
        exit;
    }
    include '../app/views/user/detail.php'; // Load file giao diện chi tiết
    break;
    // Nhớ khởi tạo session_start() ở đầu file index.php nhé!

case 'cart':
    $cart_data = [];
    $total_bill = 0;
    
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $id => $qty) {
            $product = $productModel->getProductById($id);
            if ($product) {
                $product['quantity'] = $qty;
                $product['subtotal'] = $product['selling_price'] * $qty;
                $total_bill += $product['subtotal'];
                $cart_data[] = $product;
            }
        }
    }
    include '../app/views/user/cart.php';
    break;

case 'add_to_cart':
    $id = $_GET['id'];
    $qty = $_GET['qty'] ?? 1;
    
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    
    // Nếu có rồi thì cộng dồn, chưa có thì gán mới
    $_SESSION['cart'][$id] = isset($_SESSION['cart'][$id]) ? $_SESSION['cart'][$id] + $qty : $qty;
    
    header("Location: index.php?url=cart"); // Thêm xong đẩy sang trang giỏ hàng luôn
    break;

case 'remove_cart':
    $id = $_GET['id'];
    unset($_SESSION['cart'][$id]);
    header("Location: index.php?url=cart");
    break;
    case 'login':
        include '../app/views/user/login.php'; 
        break;

    case 'checklogin':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = $_POST['username'];
            $pass = $_POST['password'];

            // Gọi hàm login từ $userModel đã khởi tạo ở mục 3
            $userData = $userModel->login($user, $pass);

            if ($userData) {
                // Đăng nhập thành công
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['username'] = $userData['username'];
            $_SESSION['full_name'] = $userData['full_name'];
            $_SESSION['role'] = $userData['role']; 
            $_SESSION['email'] = $userData['email'];
            $_SESSION['phone'] = $userData['phone'];
            $_SESSION['address_default'] = $userData['address_default'];
                header("Location: index.php?url=home");
                exit();
            } else {
                // Đăng nhập thất bại
                echo "<script>alert('Sai tài khoản hoặc mật khẩu!'); window.location.href='index.php?url=login';</script>";
            }
        }
        break;

    case 'register':
        include '../app/views/user/register.php';
        break;
case 'logout':
    // Xóa sạch dữ liệu phiên làm việc
    session_unset();
    session_destroy();
    // Đuổi về trang chủ
    header("Location: index.php?url=home");
    exit();
    break;

case 'profile':
    // Chỉ cho xem nếu đã đăng nhập
    if (isset($_SESSION['user_id'])) {
        include '../app/views/user/profile.php';
    } else {
        header("Location: index.php?url=login");
    }
    break;
case 'update_profile':
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
        $data = [
            'id' => $_SESSION['user_id'],
            'full_name' => $_POST['full_name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'address_default' => $_POST['address_default']
        ];
        
        // 1. Lưu vào Database (Cái này giúp dữ liệu không bị mất khi đăng xuất)
        if ($userModel->update($data)) {
            // 2. Cập nhật lại SESSION để hiển thị ngay lập tức
            $_SESSION['full_name'] = $data['full_name'];
            $_SESSION['email'] = $data['email'];
            $_SESSION['phone'] = $data['phone'];
            $_SESSION['address_default'] = $data['address_default'];

            echo "<script>alert('Cập nhật thành công vào hệ thống!'); window.location.href='index.php?url=profile';</script>";
        } else {
            echo "<script>alert('Có lỗi xảy ra, vui lòng thử lại!'); window.location.href='index.php?url=profile';</script>";
        }
    }
    break;
    default:
        include '../app/views/user/home.php';
        break;
}

// 7. HIỂN THỊ FOOTER (Trừ trang login và register)
if ($url !== 'login' && $url !== 'register' && $url !== 'checklogin') {
    echo '</main>'; 
    if (file_exists('../includes/footer.php')) {
        include '../includes/footer.php';
    }
}
?>