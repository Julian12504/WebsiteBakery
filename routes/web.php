// public/index.php
require_once '../config/database.php';
$action = $_GET['url'] ?? 'home';

switch($action) {
    case 'home':
        // Gọi HomeController và view home.php
        break;
    case 'product-detail':
        // Gọi chi tiết sản phẩm
        break;
    case 'cart':
        // Kiểm tra login rồi mới cho vào giỏ (Yêu cầu ảnh 1)
        break;
}