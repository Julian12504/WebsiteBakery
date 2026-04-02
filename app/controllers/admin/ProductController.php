<?php
class ProductController {
    // Logic tính giá vốn khi hoàn thành phiếu nhập (Ảnh 3)
    public function updateCostPrice($product_id, $new_import_qty, $new_import_price) {
        // 1. Lấy thông tin tồn kho và giá vốn hiện tại
        // 2. Áp dụng công thức:
        // $new_cost = (($old_stock * $old_cost) + ($new_qty * $new_price)) / ($old_stock + $new_qty)
        
        // 3. Cập nhật lại bảng Products (cost_price, stock_quantity, selling_price)
    }
}