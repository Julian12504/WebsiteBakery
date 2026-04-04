<?php
class DashboardController {
    private $orderModel;
    private $productModel;

    public function __construct($orderModel, $productModel) {
        $this->orderModel = $orderModel;
        $this->productModel = $productModel;
    }

    public function index() {
        // 1. Thống kê đơn hàng (Tổng số đơn)
        $totalOrders = $this->orderModel->countTotalOrders();

        // 2. Thống kê doanh thu (Tổng tiền các đơn status = 2: Đã giao)
        $totalRevenue = $this->orderModel->getTotalRevenue();

        // 3. Cảnh báo sản phẩm sắp hết hàng (Yêu cầu 0.25đ trong ảnh)
        // Giả sử ngưỡng sắp hết là dưới 10 sản phẩm
        $lowStockProducts = $this->productModel->getLowStockProducts(10);

        // 4. Lấy 5 đơn hàng mới nhất để hiển thị nhanh
        $recentOrders = $this->orderModel->getAllOrdersAdmin(null, null, null, 5);
        $recentOrders = $this->orderModel->getAllOrdersAdmin(0, null, null, 5);
        include '../app/views/admin/dashboard.php';
    }
}