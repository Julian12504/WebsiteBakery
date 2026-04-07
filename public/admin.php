<?php
session_start();

// 1. Bật hiển thị lỗi để dễ dàng debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Kiểm tra quyền Admin
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] != 1) {
    header("Location: admin_login.php");
    exit();
}

// 3. Import các cấu hình và Models
require_once '../config/database.php';
require_once '../app/models/User.php';
require_once '../app/models/Order.php';
require_once '../app/models/Product.php';
require_once '../app/models/Category.php';
require_once '../app/models/Cart.php';

// 4. Khởi tạo kết nối Database và Models
$db = (new Database())->getConnection();
$orderModel = new Order($db);
$productModel = new Product($db);
$userModel = new User($db);

// 5. Lấy URL điều hướng
$url = $_GET['url'] ?? 'dashboard';

// 6. Bộ điều hướng (Router)
switch ($url) {
    
    case 'dashboard':
        require_once '../app/controllers/admin/DashboardController.php';
        $controller = new DashboardController($orderModel, $productModel);
        $controller->index();
        break;

    // --- QUẢN LÝ SẢN PHẨM ---
    case 'products':
    case 'save_product':
    case 'edit_product':
    case 'update_product':
    case 'delete_product':
    case 'bulk_action':
        require_once '../app/controllers/admin/ProductController.php';
        $controller = new ProductController($productModel);

        if ($url == 'products')        $controller->index();
        if ($url == 'save_product')    $controller->add();
        if ($url == 'edit_product')    $controller->edit();
        if ($url == 'update_product')  $controller->update();
        if ($url == 'delete_product')  $controller->delete();
        if ($url == 'bulk_action')     $controller->bulk_action();
        break;

    // --- QUẢN LÝ DANH MỤC ---
    case 'categories':
    case 'save_category':
    case 'edit_category':
    case 'update_category':
    case 'delete_category':
        require_once '../app/models/Category.php';
        require_once '../app/controllers/admin/CategoryController.php';
        $categoryModel = new Category($db);
        $controller = new CategoryController($categoryModel);

        if ($url == 'categories')      $controller->index();
        if ($url == 'save_category')   $controller->add();
        if ($url == 'edit_category')   $controller->edit();
        if ($url == 'update_category') $controller->update();
        if ($url == 'delete_category') $controller->delete();
        break;

    // --- QUẢN LÝ ĐƠN HÀNG ---
    case 'orders':
    case 'update_order_status':
    case 'order_detail':
        require_once '../app/controllers/admin/OrderController.php';
        $controller = new OrderController($orderModel);
        
        if ($url == 'orders')              $controller->index();
        if ($url == 'update_order_status') $controller->updateStatus();
        if ($url == 'order_detail')        $controller->orderDetail();
        break;

    // --- QUẢN LÝ NGƯỜI DÙNG ---
    case 'users':
    case 'add_user':
    case 'reset_password':
    case 'toggle_user_status':
        require_once '../app/controllers/admin/UserController.php';
        $controller = new UserController($userModel);

        if ($url == 'users')              $controller->index();
        if ($url == 'add_user')           $controller->add();
        if ($url == 'reset_password')     $controller->resetPassword();
        if ($url == 'toggle_user_status') $controller->toggleStatus();
        break;

    case 'inventory':
        require_once '../app/controllers/admin/InventoryController.php';
        $controller = new InventoryController($productModel, $orderModel);
        $controller->index();
        break;

    case 'process_import':
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $admin_id = $_SESSION['admin_id']; // Lấy ID admin đang đăng nhập
        $p_id = $_POST['product_id'];
        $qty = $_POST['quantity'];
        $price = $_POST['import_price'];

        if ($productModel->processImport($admin_id, $p_id, $qty, $price)) {
            header("Location: admin.php?url=products&msg=import_success");
        }
    }
    break;

case 'process_import_all':
    require_once '../app/controllers/admin/ProductController.php';
    $controller = new ProductController($productModel);
    $controller->process_import_all();
    break;

case 'edit_import':
    require_once '../app/controllers/admin/ProductController.php';
    $controller = new ProductController($productModel);
    $controller->editImport();
    break;

case 'update_import':
    require_once '../app/controllers/admin/ProductController.php';
    $controller = new ProductController($productModel);
    $controller->updateImport();
    break;

case 'delete_import':
    require_once '../app/controllers/admin/ProductController.php';
    $controller = new ProductController($productModel);
    $controller->deleteImport();
    break;

case 'import_product':
    require_once '../app/controllers/admin/ProductController.php';
    $controller = new ProductController($productModel);
    $controller->import_product();
    break;

    default:
        echo "<h2 style='text-align:center; color:red; margin-top:50px;'>404 - Trang bạn tìm không tồn tại!</h2>";
        echo "<p style='text-align:center;'>URL hiện tại: " . htmlspecialchars($url) . "</p>";
        echo "<p style='text-align:center;'><a href='admin.php?url=dashboard'>Quay về Dashboard</a></p>";
        break;
}