<?php
// 1. Khởi tạo session
session_start();

// 2. NHÚNG CÁC FILE QUAN TRỌNG (Phải nhúng trước khi sử dụng)
require_once '../config/database.php';
require_once '../app/models/Product.php';
require_once '../app/models/User.php';
include_once '../app/models/Category.php';
require_once '../app/models/Order.php';
require_once '../app/models/Cart.php';
// 3. KHỞI TẠO ĐỐI TƯỢNG (Bước này giải quyết lỗi Fatal Error của bạn)
$database = new Database();
$db = $database->getConnection();

// Tạo ra các "thợ làm việc" từ Model
$productModel = new Product($db);
$userModel = new User($db); // Đã có $userModel, không còn bị null nữa!
$categoryModel = new Category($db);
$orderModel = new Order($db);
$cartModel = new Cart($db);
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
        $new_products = $productModel->getProductsPaged(0, 4);
        include '../app/views/user/home.php';
        break;

case 'product':
    $limit = 6; // Số bánh trên mỗi trang
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $from = ($page - 1) * $limit;
    $category = $_GET['category'] ?? '';
    $search = trim($_GET['search'] ?? '');
    $min_price = isset($_GET['min_price']) ? trim($_GET['min_price']) : '';
    $max_price = isset($_GET['max_price']) ? trim($_GET['max_price']) : '';

    // Lấy data
    $products = $productModel->getProductsPaged($from, $limit, $category, $search, $min_price, $max_price);
    $total_products = $productModel->countAll($category, $search, $min_price, $max_price);
    $total_pages = $total_products > 0 ? ceil($total_products / $limit) : 1;
    
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
case 'order_detail':
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php?url=login");
        exit();
    }
    
    $order_id = $_GET['id'] ?? 0;
    
    // 1. Lấy thông tin chung của đơn hàng
    $order = $orderModel->getOrderById($order_id);
    
    // Bảo mật: Nếu đơn hàng không phải của người đang đăng nhập thì không cho xem
    if (!$order || $order['user_id'] != $_SESSION['user_id']) {
        echo "<script>alert('Đơn hàng không tồn tại!'); window.location.href='index.php?url=profile';</script>";
        exit();
    }

    // 2. Lấy danh sách các món bánh trong đơn đó
    $order_items = $orderModel->getOrderDetails($order_id);
    
    include '../app/views/user/order_detail.php';
    break;
case 'cart':
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Vui lòng đăng nhập để sử dụng giỏ hàng!'); window.location.href='index.php?url=login';</script>";
        exit();
    }

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
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng!'); window.location.href='index.php?url=login';</script>";
        exit();
    }

    $id = $_GET['id'];
    $qty = isset($_GET['qty']) ? intval($_GET['qty']) : 1;
    if ($qty < 1) {
        $qty = 1;
    }
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Nếu có rồi thì cộng dồn, chưa có thì gán mới
    $_SESSION['cart'][$id] = isset($_SESSION['cart'][$id]) ? $_SESSION['cart'][$id] + $qty : $qty;
    
    $redirect = $_GET['redirect'] ?? '';
    if ($redirect === 'checkout') {
        header("Location: index.php?url=checkout");
    } else {
        header("Location: index.php?url=cart");
    }
    break;

case 'remove_cart':
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Vui lòng đăng nhập để điều chỉnh giỏ hàng!'); window.location.href='index.php?url=login';</script>";
        exit();
    }

    $id = $_GET['id'];
    unset($_SESSION['cart'][$id]);
    header("Location: index.php?url=cart");
    break;
    case 'login':
        include '../app/views/user/login.php'; 
        break;

    case 'checklogin':
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Dùng trim() để loại bỏ khoảng trắng thừa
        $user = trim($_POST['username']);
        $pass = $_POST['password'];

        // Gọi hàm login từ $userModel (hàm này phải dùng password_verify như tui chỉ ở trên)
        $userData = $userModel->checklogin($user, $pass);

        if ($userData) {
            // Kiểm tra nếu tài khoản bị khóa (status = 0) thì không cho vào
            if (isset($userData['status']) && $userData['status'] == 0) {
                echo "<script>alert('Tài khoản của bạn đã bị khóa!'); window.location.href='index.php?url=login';</script>";
                exit();
            }

            // Đăng nhập thành công - Lưu Session
            // Gom nhóm vào mảng $_SESSION['user'] cho gọn cũng được, hoặc để rời như bạn đều OK
            $_SESSION['user_id']         = $userData['id'];
            $_SESSION['username']        = $userData['username'];
            $_SESSION['full_name']       = $userData['full_name'];
            $_SESSION['role']            = $userData['role']; 
            $_SESSION['email']           = $userData['email'];
            $_SESSION['phone']           = $userData['phone'];
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
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $full_name = trim($_POST['fullname']); // Lấy từ name="fullname" của form
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $address_default = trim($_POST['address_default']);

        $province = trim($_POST['province']);
        $ward = trim($_POST['ward']);
        $address_detail = trim($_POST['address_detail']);
        $address_default = trim($_POST['address_default']);

        if (empty($province) || empty($ward) || empty($address_detail)) {
            $error = "Vui lòng nhập đầy đủ địa chỉ giao hàng mặc định!";
        } elseif ($password !== $confirm_password) {
            $error = "Mật khẩu nhập lại không chính xác!";
        } elseif ($userModel->emailExists($email)) {
            $error = "Email này đã được sử dụng!";
        } elseif ($userModel->phoneExists($phone)) {
            $error = "Số điện thoại này đã được sử dụng!";
        } elseif ($userModel->usernameExists($username)) {
            $error = "Tên đăng nhập này đã được sử dụng!";
        } else {
            // Truyền $full_name vào đây
        $username = trim($_POST['username']);
            if ($userModel->register($full_name, $username, $email, $password, $phone, $address_default)) {
                echo "<script>alert('Đăng ký thành công!'); window.location.href='index.php?url=login';</script>";
                exit;
            } else {
                $error = "Lỗi hệ thống, vui lòng thử lại!";
            }
        }
    }
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
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php?url=login");
        exit();
    }
    
    $user_id = $_SESSION['user_id'];

    // 1. Lấy thông tin người dùng mới nhất từ DB
    $user_info = $userModel->getUserById($user_id); 

    // 2. Cập nhật lại Session
    if ($user_info) {
        $_SESSION['full_name'] = $user_info['full_name'];
        $_SESSION['email'] = $user_info['email'];
        $_SESSION['phone'] = $user_info['phone'];
        $_SESSION['address_default'] = $user_info['address_default'];
    }

    // 3. Lấy danh sách đơn hàng (Đổi tên thành $all_orders để khớp bên dưới)
    $all_orders = $orderModel->getOrdersByUserId($user_id);
    
    // Đảm bảo $all_orders là mảng để không bị lỗi Fatal error
    if (!$all_orders) { $all_orders = []; }

    // 4. Lọc đơn hàng theo từng trạng thái
    $pending_orders = array_filter($all_orders, fn($o) => $o['status'] == 0);
    $processing_orders = array_filter($all_orders, fn($o) => $o['status'] == 1);
    $shipping_orders = array_filter($all_orders, fn($o) => $o['status'] == 2);
    $completed_orders = array_filter($all_orders, fn($o) => $o['status'] == 3);
    $cancelled_orders = array_filter($all_orders, fn($o) => $o['status'] == 4);

    include '../app/views/user/profile.php';
    break;
case 'reorder':
    $order_id = $_GET['id'] ?? 0;
    
    // 1. Lấy danh sách món từ đơn cũ
    $items = $orderModel->getOrderDetails($order_id);
    
    if ($items) {
        foreach ($items as $item) {
            // 2. Sử dụng hàm của Cart Model đã tạo ở trên
            $cartModel->addToCart($item['product_id'], $item['quantity']);
        }
        echo "<script>alert('Đã thêm các sản phẩm từ đơn #DH$order_id vào giỏ hàng!'); window.location.href='index.php?url=cart';</script>";
    } else {
        echo "<script>alert('Lỗi: Không tìm thấy sản phẩm!'); window.history.back();</script>";
    }
    break; 
case 'cancel_order':
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $order_id = $_POST['order_id'];
        $reason = $_POST['reason'];
        
        // Nếu chọn "Khác" thì lấy lý do từ textarea
        if ($reason == 'other') {
            $reason = "Khác: " . $_POST['other_reason'];
        }

        if ($orderModel->cancelOrder($order_id, $reason)) {
            echo "<script>alert('Đã hủy đơn hàng thành công!'); window.location.href='index.php?url=profile';</script>";
        } else {
            echo "<script>alert('Không thể hủy đơn hàng này!'); window.history.back();</script>";
        }
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
case 'process_checkout':
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (!isset($_SESSION['user_id'])) {
            echo "<script>alert('Vui lòng đăng nhập để thanh toán!'); window.location.href='index.php?url=login';</script>";
            exit();
        }

        // 1. Kiểm tra giỏ hàng có trống không
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            echo "<script>alert('Giỏ hàng trống!'); window.location.href='index.php';</script>";
            break;
        }

        // 2. Lấy dữ liệu
        $user_id = $_SESSION['user_id'];
        $address = $_POST['address'];
        $payment_method = $_POST['payment_method'];

        // 3. Tính toán tổng tiền và chuẩn bị chi tiết giỏ hàng
        $total_bill = 0;
        $cart_details = [];
        
        foreach ($_SESSION['cart'] as $id => $qty) {
            $p = $productModel->getProductById($id);
            if ($p) {
                $subtotal = $p['selling_price'] * $qty;
                $total_bill += $subtotal;
                $cart_details[] = [
                    'id' => $id,
                    'price' => $p['selling_price'],
                    'qty' => $qty
                ];
            }
        }

        // 4. Gọi hàm tạo đơn hàng (Chỉ truyền 4 tham số như Model đã định nghĩa)
        $order_id = $orderModel->createOrder($user_id, $total_bill, $address, $payment_method);

        if ($order_id) {
            // 5. Lưu chi tiết từng món vào bảng order_details
            foreach ($cart_details as $item) {
                $orderModel->createOrderDetail($order_id, $item['id'], $item['price'], $item['qty']);
            }
            
            // 6. Xóa giỏ hàng và thông báo thành công
            unset($_SESSION['cart']);
            echo "<script>alert('Nhà Ngọt đã nhận đơn hàng của bạn!'); window.location.href='index.php?url=home';</script>";
        } else {
            echo "<script>alert('Có lỗi khi lưu đơn hàng, vui lòng thử lại!'); history.back();</script>";
        }
    }
    break;
case 'checkout':
    // Kiểm tra đăng nhập để lấy thông tin địa chỉ sẵn có
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Vui lòng đăng nhập để thanh toán!'); window.location.href='index.php?url=login';</script>";
        exit;
    }
    
    $cart_items = [];
    $total_bill = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $id => $qty) {
            $product = $productModel->getProductById($id);
            $product['quantity'] = $qty;
            $product['subtotal'] = $product['selling_price'] * $qty;
            $total_bill += $product['subtotal'];
            $cart_items[] = $product;
        }
    }
    include '../app/views/user/checkout.php';
    break;
    case 'about':
        include '../app/views/user/about.php';
        break;
    case 'contact':
        include '../app/views/user/contact.php';
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