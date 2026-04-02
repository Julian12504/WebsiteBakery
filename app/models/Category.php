<?php
class Category {
    private $conn;
    private $table_name = "categories"; // Tên bảng trong database của bạn

    // Các thuộc tính tương ứng với cột trong DB
    public $id;
    public $name;
    public $description;

    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. Lấy tất cả danh mục (Dùng cho cái Select Box ở trang Shop)
    public function getAllCategories() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Lấy thông tin chi tiết 1 danh mục (Dùng khi cần hiện tên loại bánh trên tiêu đề)
    public function getCategoryById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) return $row;
        return null;
    }

    // 3. Thêm danh mục mới (Dùng cho trang Admin sau này)
    public function create($name, $description) {
        $query = "INSERT INTO " . $this->table_name . " (name, description) VALUES (:name, :desc)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':desc', $description);

        if($stmt->execute()) return true;
        return false;
    }

    // 4. Cập nhật danh mục
    public function update($id, $name, $description) {
        $query = "UPDATE " . $this->table_name . " SET name = :name, description = :desc WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':desc', $description);

        if($stmt->execute()) return true;
        return false;
    }

    // 5. Xóa danh mục
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if($stmt->execute()) return true;
        return false;
    }
}