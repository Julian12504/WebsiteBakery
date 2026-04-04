<?php
class Order {
    private $conn;
    private $table_name = "orders"; // THIẾU DÒNG NÀY SẼ BÁO LỖI

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lưu đơn hàng tổng quát dựa trên cấu trúc database của bạn
    public function createOrder($user_id, $total_amount, $shipping_address, $payment_method) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (user_id, order_date, total_amount, shipping_address, payment_method, status) 
                  VALUES (:user_id, NOW(), :total_amount, :shipping_address, :payment_method, 0)";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind các giá trị
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':total_amount', $total_amount);
        $stmt->bindParam(':shipping_address', $shipping_address);
        $stmt->bindParam(':payment_method', $payment_method);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Lưu chi tiết từng món bánh
  // Lưu chi tiết từng món bánh trong đơn
// Lưu chi tiết từng món bánh (Chỉ dùng các cột: order_id, product_id, quantity, price)
public function createOrderDetail($order_id, $product_id, $price, $qty) {
    // Xóa total_money vì Database của bạn không có cột này
    $query = "INSERT INTO order_details (order_id, product_id, quantity, price) 
              VALUES (?, ?, ?, ?)";
    
    $stmt = $this->conn->prepare($query);
    
    // Truyền đúng 4 tham số tương ứng với 4 dấu hỏi chấm
    return $stmt->execute([$order_id, $product_id, $qty, $price]);
}
// Thêm hàm này vào class Order trong file Order.php
public function getOrdersByUserId($user_id) {
    $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id ORDER BY order_date DESC";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Xóa đơn hàng (chỉ khi còn chờ xác nhận)
public function cancelOrder($order_id, $reason) {
    $query = "UPDATE " . $this->table_name . " 
              SET status = 4, cancel_reason = :reason 
              WHERE id = :id AND status = 0"; // Chỉ cho hủy khi còn chờ xác nhận
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':reason', $reason);
    $stmt->bindParam(':id', $order_id);
    return $stmt->execute();
}
public function getOrderDetails($order_id) {
    // Lưu ý: Thay 'name' bằng tên cột thực tế trong bảng products của bạn
    $sql = "SELECT od.*, p.name as product_name 
            FROM order_details od 
            JOIN products p ON od.product_id = p.id 
            WHERE od.order_id = :order_id";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':order_id', $order_id);
    $stmt->execute(); 
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Hàm lấy 1 đơn hàng duy nhất để hiện thông tin chung (ngày đặt, tổng tiền)
public function getOrderById($id) {
    // Hãy thay 'full_name', 'created_at' bằng tên cột thật trong bảng orders của bạn
    $query = "SELECT * FROM orders WHERE id = :id LIMIT 0,1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
// 1. Đếm tổng số đơn hàng
public function countTotalOrders() {
    $sql = "SELECT COUNT(*) as total FROM orders";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total'] ?? 0;
}

// 2. Tính tổng doanh thu (Chỉ tính các đơn đã giao thành công - status = 2)
public function getTotalRevenue() {
    $sql = "SELECT SUM(total_amount) as revenue FROM orders WHERE status = 2";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['revenue'] ?? 0;
}

// 3. Lấy tất cả đơn hàng cho Admin (Bao gồm giới hạn số lượng cho Dashboard)
public function getAllOrdersAdmin($status = null, $from = null, $to = null, $limit = null) {
    $sql = "SELECT o.*, u.full_name 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            WHERE 1=1";
    
    $params = [];
    if ($status !== null) {
        $sql .= " AND o.status = :status";
        $params['status'] = $status;
    }
    if ($from) {
        $sql .= " AND o.order_date >= :from";
        $params['from'] = $from;
    }
    if ($to) {
        $sql .= " AND o.order_date <= :to";
        $params['to'] = $to;
    }

    $sql .= " ORDER BY o.order_date DESC";

    if ($limit !== null) {
        $sql .= " LIMIT " . (int)$limit;
    }

    $stmt = $this->conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 2. Lấy danh sách sản phẩm thuộc về đơn hàng đó (Sử dụng JOIN để lấy ảnh và tên bánh)
public function getOrderItems($order_id) {
    // Sửa order_items thành order_details cho khớp với database của bạn
    $query = "SELECT od.*, p.name, p.image 
              FROM order_details od 
              JOIN products p ON od.product_id = p.id 
              WHERE od.order_id = :order_id";
    
    $stmt = $this->conn->prepare($query);
    $stmt->execute(['order_id' => $order_id]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Hàm cập nhật trạng thái đơn hàng
public function updateStatus($id, $status) {
    $query = "UPDATE orders SET status = :status WHERE id = :id";
    $stmt = $this->conn->prepare($query);
    
    // Bind các tham số để bảo mật SQL Injection
    $stmt->bindParam(':status', $status, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    return $stmt->execute();
}
}