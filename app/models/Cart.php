<?php
class Cart {
    private $conn;

    public function __construct($db = null) {
        $this->conn = $db;
        // Đảm bảo session luôn được khởi tạo để dùng giỏ hàng
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Thêm sản phẩm vào giỏ hàng (Sử dụng Session)
     */
    public function addToCart($product_id, $quantity) {
        // Nếu sản phẩm đã có, cộng dồn số lượng
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            // Nếu chưa có, tạo mới
            $_SESSION['cart'][$product_id] = $quantity;
        }
        return true;
    }

    /**
     * Lấy toàn bộ danh sách ID sản phẩm và số lượng
     */
    public function getCartItems() {
        return $_SESSION['cart'] ?? [];
    }

    /**
     * Cập nhật số lượng sản phẩm
     */
    public function updateQuantity($product_id, $quantity) {
        if (isset($_SESSION['cart'][$product_id])) {
            if ($quantity <= 0) {
                $this->removeItem($product_id);
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
            }
        }
    }

    /**
     * Xóa 1 sản phẩm khỏi giỏ
     */
    public function removeItem($product_id) {
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
    }

    /**
     * Xóa sạch giỏ hàng (Sau khi thanh toán xong)
     */
    public function clearCart() {
        unset($_SESSION['cart']);
    }

    /**
     * Tính tổng số lượng món đồ trong giỏ (Hiện ở icon giỏ hàng)
     */
    public function getTotalItems() {
        $total = 0;
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $qty) {
                $total += $qty;
            }
        }
        return $total;
    }
}