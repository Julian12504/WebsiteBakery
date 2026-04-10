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

        // Báo cáo nhập-xuất
        $fromDate = $_GET['from_date'] ?? '';
        $toDate = $_GET['to_date'] ?? '';
        $reportData = null;
        $reportProductInfo = null;

        // Luôn lấy danh sách tất cả sản phẩm cho select box
        $allProducts = $this->productModel->getProductsPagedAdmin(0, 10000, '', '', '', '', '', '', '', '');

        $products = [];
        $totalProducts = 0;
        $totalPages = 0;
        $inventoryResult = null;
        $searchedProduct = null;

        // Báo cáo nhập-xuất trong khoảng thời gian
        if ($productId > 0 && $fromDate !== '' && $toDate !== '') {
            // Chuyển đổi datetime-local sang định dạng SQL
            $fromDateTime = $fromDate ? date('Y-m-d H:i:s', strtotime($fromDate)) : null;
            $toDateTime = $toDate ? date('Y-m-d 23:59:59', strtotime($toDate)) : null;
            
            if ($fromDateTime && $toDateTime) {
                $importQty = (int)$this->productModel->getImportQuantityInRange($productId, $fromDateTime, $toDateTime);
                $exportQty = (int)$this->productModel->getExportQuantityInRange($productId, $fromDateTime, $toDateTime);
                $netChange = $importQty - $exportQty;
                
                $reportProductInfo = $this->productModel->getProductByIdAdmin($productId);
                $costPrice = (float)($reportProductInfo['gia_von'] ?? 0);
                
                $reportData = [
                    'product_id' => $productId,
                    'product_name' => $reportProductInfo['name'] ?? '',
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'import_qty' => $importQty,
                    'export_qty' => $exportQty,
                    'net_change' => $netChange,
                    'import_value' => $importQty * $costPrice,
                    'export_value' => $exportQty * $costPrice,
                    'cost_price' => $costPrice
                ];
            }
        }

        // Tra cứu tồn kho tại thời điểm
        if ($productId > 0 && $atDatetime !== '' && !($fromDate && $toDate)) {
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
        } else if (!($productId > 0 && $fromDate && $toDate)) {
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