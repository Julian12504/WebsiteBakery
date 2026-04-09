<?php
class InventoryController {
    private $productModel;
    private $orderModel;

    public function __construct($productModel, $orderModel) {
        $this->productModel = $productModel;
        $this->orderModel = $orderModel;
    }

    public function index() {
        $productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
        $atDatetime = $_GET['at_datetime'] ?? '';
        $threshold = isset($_GET['threshold']) ? (int)$_GET['threshold'] : 5;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 20; // Số sản phẩm mỗi trang

        // Luôn lấy danh sách tất cả sản phẩm cho select box
        $allProducts = $this->productModel->getProductsPagedAdmin(0, 10000, '', '', '', '', '', '', '', '');

        $products = [];
        $totalProducts = 0;
        $totalPages = 0;
        $inventoryResult = null;
        $searchedProduct = null;

        // Tra cứu tồn kho tại thời điểm
        if ($productId > 0 && $atDatetime !== '') {
            $timestamp = strtotime($atDatetime);
            if ($timestamp !== false) {
                $searchDatetime = date('Y-m-d H:i:s', $timestamp);
                $inventoryResult = $this->productModel->getStockAtDateTime($productId, $searchDatetime);
                
                // Lấy thông tin sản phẩm được tra cứu
                $searchedProduct = $this->productModel->getProductByIdAdmin($productId);
                if ($searchedProduct) {
                    $searchedProduct['stock_at_time'] = $inventoryResult;
                    $products = [$searchedProduct]; // Chỉ hiện sản phẩm này trong bảng
                    $totalProducts = 1;
                    $totalPages = 1;
                }
            }
        } else {
            // Lấy danh sách tất cả sản phẩm với phân trang
            $offset = ($page - 1) * $perPage;
            $products = $this->productModel->getProductsPagedAdmin($offset, $perPage, '', '', '', '', '', '', '', '');
            $totalProducts = $this->productModel->getTotalProductsCount();
            $totalPages = ceil($totalProducts / $perPage);
        }

        // Lấy danh sách cảnh báo sắp hết hàng
        $lowStockProducts = $this->productModel->getLowStockProductsByThreshold($threshold);

        include '../app/views/admin/inventory.php';
    }
}
?>