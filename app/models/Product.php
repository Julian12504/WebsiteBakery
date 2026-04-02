<?php
class Product {
    private $conn;
    private $table_name = "products"; // Nên để chữ thường cho đồng bộ

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllProducts() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 1 ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Hàm tìm kiếm nâng cao - Đã hoàn thiện logic BindParam
    public function searchAdvanced($keyword, $cat_id, $min_price, $max_price) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 1";
        $params = [];

        if (!empty($keyword)) {
            $query .= " AND name LIKE :name";
            $params[':name'] = "%$keyword%";
        }
        if (!empty($cat_id)) {
            $query .= " AND category_id = :cat";
            $params[':cat'] = $cat_id;
        }
        if (!empty($min_price)) {
            $query .= " AND selling_price >= :min";
            $params[':min'] = $min_price;
        }
        if (!empty($max_price)) {
            $query .= " AND selling_price <= :max";
            $params[':max'] = $max_price;
        }

        $query .= " ORDER BY id DESC";
        
        $stmt = $this->conn->prepare($query);
        
        // Thực thi với mảng tham số đã lọc
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
public function getProductById($id) {
    $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id AND status = 1 LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
// Hàm lấy sản phẩm có phân trang
public function getProductsPaged($from, $limit, $category = '') {
    $query = "SELECT * FROM products WHERE status = 1";
    if (!empty($category)) {
        $query .= " AND category_id = :cat";
    }
    $query .= " ORDER BY id DESC LIMIT :from, :limit";
    
    $stmt = $this->conn->prepare($query);
    if (!empty($category)) $stmt->bindValue(':cat', $category);
    $stmt->bindValue(':from', (int)$from, PDO::PARAM_INT);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Hàm đếm tổng số sản phẩm để tính tổng số trang
public function countAll($category = '') {
    $query = "SELECT COUNT(*) as total FROM products WHERE status = 1";
    if (!empty($category)) $query .= " AND category_id = :cat";
    
    $stmt = $this->conn->prepare($query);
    if (!empty($category)) $stmt->bindValue(':cat', $category);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total'];
}
}