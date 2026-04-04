<?php
class Product {
    private $conn;
    private $table_name = "products";

    public function __construct($db) {
        $this->conn = $db;
    }

    // --- CÁC HÀM DÀNH CHO USER SITE ---
    public function getAllProducts() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 1 ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id AND status = 1 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
// Hàm bổ sung để sửa lỗi ở trang chủ index.php
    public function getProductsPaged($offset, $limit) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE status = 1 
                  ORDER BY id DESC 
                  LIMIT :offset, :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Hàm đếm tổng sản phẩm cho phân trang trang chủ
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE status = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
    // --- CÁC HÀM DÀNH CHO ADMIN SITE ---

    // Lấy sản phẩm có phân trang VÀ tìm kiếm (Gộp làm 1 hàm duy nhất)
  public function getProductsPagedAdmin($offset, $limit, $search = '', $category_id = '') {
    $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.name LIKE :search";
    
    // Nếu có lọc theo danh mục thì nối thêm SQL
    if (!empty($category_id)) {
        $sql .= " AND p.category_id = :cat_id";
    }

    $sql .= " ORDER BY p.id DESC LIMIT :offset, :limit";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    if (!empty($category_id)) {
        $stmt->bindValue(':cat_id', $category_id, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // Đếm tổng số sản phẩm có lọc theo tìm kiếm
  public function countAllAdmin($search = '', $category_id = '') {
    $sql = "SELECT COUNT(*) as total FROM products WHERE name LIKE :search";
    if (!empty($category_id)) {
        $sql .= " AND category_id = :cat_id";
    }
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    if (!empty($category_id)) {
        $stmt->bindValue(':cat_id', $category_id, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total'];
}

    public function getProductByIdAdmin($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllCategories() {
        $query = "SELECT * FROM categories ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

public function insertProduct($data) {
    $sql = "INSERT INTO products (name, category_id, description, unit, image, gia_von, loi_nhuan, selling_price, stock, low_stock_threshold, status) 
            VALUES (:name, :category_id, :description, :unit, :image, :gia_von, :loi_nhuan, :selling_price, :stock, :low_stock_threshold, :status)";
    
    $stmt = $this->conn->prepare($sql);
    
    // Bind từng biến để kiểm soát lỗi
    $stmt->bindValue(':name', $data['name']);
    $stmt->bindValue(':category_id', $data['category_id']);
    $stmt->bindValue(':description', $data['description'] ?? '');
    $stmt->bindValue(':unit', $data['unit'] ?? 'Cái');
    $stmt->bindValue(':image', $data['image']);
    $stmt->bindValue(':gia_von', $data['gia_von']);
    $stmt->bindValue(':loi_nhuan', $data['loi_nhuan']);
    $stmt->bindValue(':selling_price', $data['selling_price']);
    $stmt->bindValue(':stock', $data['stock']);
    $stmt->bindValue(':low_stock_threshold', $data['low_stock_threshold'] ?? 5);
    $stmt->bindValue(':status', $data['status'] ?? 1);
    
    return $stmt->execute();
}

    public function updateProduct($data) {
        $sql = "UPDATE products SET 
                name = :name, category_id = :category_id, description = :description,
                gia_von = :gia_von, loi_nhuan = :loi_nhuan, selling_price = :selling_price,
                stock = :stock, image = :image, status = :status 
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    public function getLowStockProducts($threshold) {
        $sql = "SELECT * FROM products WHERE stock <= :threshold AND status = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['threshold' => $threshold]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // 1. Xóa sản phẩm
public function delete($id) {
    $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
}

// 2. Thay đổi trạng thái (Ẩn/Hiện)
public function updateStatus($id, $status) {
    $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
}

// 3. Thêm sản phẩm mới
public function create($data) {
    $query = "INSERT INTO " . $this->table_name . " (name, category_id, image, status, description, selling_price) 
              VALUES (:name, :cat_id, :img, :status, :desc, :price)";
    $stmt = $this->conn->prepare($query);
    return $stmt->execute($data);
}
public function processImport($admin_id, $product_id, $qty, $price) {
    try {
        $this->conn->beginTransaction();

        // 1. Tạo hóa đơn nhập vào bảng purchase_receipts
        $sqlReceipt = "INSERT INTO purchase_receipts (admin_id, receipt_date, status) VALUES (?, NOW(), 1)";
        $stmtReceipt = $this->conn->prepare($sqlReceipt);
        $stmtReceipt->execute([$admin_id]);
        $receipt_id = $this->conn->lastInsertId();

        // 2. Lưu chi tiết vào bảng purchase_details
        $sqlDetail = "INSERT INTO purchase_details (receipt_id, product_id, import_quantity, import_price) VALUES (?, ?, ?, ?)";
        $stmtDetail = $this->conn->prepare($sqlDetail);
        $stmtDetail->execute([$receipt_id, $product_id, $qty, $price]);

        // 3. Tính toán giá vốn bình quân (Quy tắc bạn đưa ra)
        $product = $this->getProductByIdAdmin($product_id); // Giả sử hàm này lấy đủ stock, gia_von, loi_nhuan
        $current_stock = $product['stock'];
        $current_cost = $product['gia_von'];

        $new_stock = $current_stock + $qty;
        $new_cost = (($current_stock * $current_cost) + ($qty * $price)) / $new_stock;
        
        // Giá bán mới dựa trên % lợi nhuận cũ
        $new_selling_price = $new_cost * (1 + $product['loi_nhuan'] / 100);

        // 4. Cập nhật lại bảng products
        $sqlUpdateProd = "UPDATE products SET stock = ?, gia_von = ?, selling_price = ? WHERE id = ?";
        $stmtUpdateProd = $this->conn->prepare($sqlUpdateProd);
        $stmtUpdateProd->execute([$new_stock, $new_cost, $new_selling_price, $product_id]);

        $this->conn->commit();
        return true;
    } catch (Exception $e) {
        $this->conn->rollBack();
        return false;
    }
}
// Lấy toàn bộ danh sách sản phẩm để phục vụ việc chọn sản phẩm khi nhập kho
public function getAllProductsAdmin() {
    $sql = "SELECT id, name, stock, gia_von, loi_nhuan, unit FROM products ORDER BY name ASC";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}