<?php
class Review {
    private $conn;
    private $table_name = "product_reviews";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Tạo bảng product_reviews nếu chưa có
    public function createTable() {
        $query = "CREATE TABLE IF NOT EXISTS " . $this->table_name . " (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT NOT NULL,
            user_id INT NOT NULL,
            rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
            comment LONGTEXT,
            status INT DEFAULT 0 COMMENT '0: pending, 1: approved',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )";

        return $this->conn->exec($query);
    }

    // Thêm đánh giá mới
    public function createReview($product_id, $user_id, $rating, $comment = '') {
        $query = "INSERT INTO " . $this->table_name . " 
                  (product_id, user_id, rating, comment, status) 
                  VALUES (:product_id, :user_id, :rating, :comment, 0)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':comment', $comment);

        return $stmt->execute();
    }

    // Lấy đánh giá theo sản phẩm (chỉ approved)
    public function getReviewsByProduct($product_id, $limit = 10) {
        $query = "SELECT r.*, u.full_name, u.username, r.created_at 
                  FROM " . $this->table_name . " r 
                  JOIN users u ON r.user_id = u.id 
                  WHERE r.product_id = :product_id AND r.status = 1
                  ORDER BY r.created_at DESC 
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tính rating trung bình của sản phẩm (chỉ approved)
    public function getAverageRating($product_id) {
        $query = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews 
                  FROM " . $this->table_name . " 
                  WHERE product_id = :product_id AND status = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return [
            'average' => round($result['avg_rating'] ?? 0, 1),
            'total' => $result['total_reviews'] ?? 0
        ];
    }

    // Kiểm tra user đã đánh giá sản phẩm này chưa
    public function hasUserReviewedProduct($product_id, $user_id) {
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE product_id = :product_id AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    // Lấy các sản phẩm trong đơn hàng để đánh giá
    public function getProductsFromOrderForReview($order_id) {
        $query = "SELECT DISTINCT od.product_id, p.name, p.image, od.quantity, od.price
                  FROM order_details od
                  JOIN products p ON od.product_id = p.id
                  WHERE od.order_id = :order_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy reviews của user cho các sản phẩm trong đơn hàng
    public function getUserReviewsForOrder($order_id, $user_id) {
        $query = "SELECT od.product_id, r.rating, r.comment, r.status
                  FROM order_details od
                  LEFT JOIN " . $this->table_name . " r ON od.product_id = r.product_id AND r.user_id = :user_id
                  WHERE od.order_id = :order_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
