<?php
class InventoryController {
    private $productModel;
    private $orderModel;

    public function __construct($productModel, $orderModel) {
        $this->productModel = $productModel;
        $this->orderModel = $orderModel;
    }

    public function index() {
        $products = $this->productModel->getAllProducts();
        $product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
        $asof = $_GET['asof'] ?? '';
        $from_date = $_GET['from_date'] ?? '';
        $to_date = $_GET['to_date'] ?? '';
        $threshold = isset($_GET['threshold']) ? (int)$_GET['threshold'] : 10;

        $selectedProduct = null;
        $stockAtTime = null;
        $importsRange = null;
        $exportsRange = null;
        $netRange = null;

        if ($product_id) {
            $selectedProduct = $this->productModel->getProductByIdAdmin($product_id);
        }

        if ($selectedProduct && $asof) {
            $timestamp = strtotime($asof);
            if ($timestamp !== false) {
                $searchDatetime = date('Y-m-d H:i:s', $timestamp);
                $stockAtTime = $this->productModel->getStockAtDateTime($product_id, $searchDatetime);
            }
        }

        if ($selectedProduct && $from_date) {
            $fromDate = date('Y-m-d 00:00:00', strtotime($from_date));
            $toDate = $to_date ? date('Y-m-d 23:59:59', strtotime($to_date)) : date('Y-m-d 23:59:59', strtotime($from_date));
            $importsRange = $this->productModel->getImportQuantityInRange($product_id, $fromDate, $toDate);
            $exportsRange = $this->productModel->getExportQuantityInRange($product_id, $fromDate, $toDate);
            $netRange = $importsRange - $exportsRange;
        }

        $lowStockProducts = $this->productModel->getLowStockProductsByThreshold($threshold);
        include '../app/views/admin/inventory.php';
    }
}
?>