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

    public function getTotalProductsCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE status = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total'] ?? 0);
    }

    public function getImportQuantityInRange($product_id, $from_date, $to_date) {
        $sql = "SELECT SUM(pd.import_quantity) as total_import
                FROM purchase_details pd
                JOIN purchase_receipts pr ON pd.receipt_id = pr.id
                WHERE pd.product_id = :product_id
                  AND pr.receipt_date BETWEEN :from_date AND :to_date";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindValue(':from_date', $from_date);
        $stmt->bindValue(':to_date', $to_date);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total_import'] ?? 0);
    }

    public function getExportQuantityInRange($product_id, $from_date, $to_date) {
        $sql = "SELECT SUM(od.quantity) as total_export
                FROM order_details od
                JOIN orders o ON od.order_id = o.id
                WHERE od.product_id = :product_id
                  AND o.order_date BETWEEN :from_date AND :to_date
                  AND o.status = 2";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindValue(':from_date', $from_date);
        $stmt->bindValue(':to_date', $to_date);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total_export'] ?? 0);
    }

    public function getImportQuantityAfter($product_id, $dateTime) {
        $sql = "SELECT SUM(pd.import_quantity) as total_import
                FROM purchase_details pd
                JOIN purchase_receipts pr ON pd.receipt_id = pr.id
                WHERE pd.product_id = :product_id
                  AND pr.receipt_date > :dateTime";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindValue(':dateTime', $dateTime);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total_import'] ?? 0);
    }

    public function getExportQuantityAfter($product_id, $dateTime) {
        $sql = "SELECT SUM(od.quantity) as total_export
                FROM order_details od
                JOIN orders o ON od.order_id = o.id
                WHERE od.product_id = :product_id
                  AND o.order_date > :dateTime
                  AND o.status = 2";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindValue(':dateTime', $dateTime);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total_export'] ?? 0);
    }

    public function getStockAtDateTime($product_id, $dateTime) {
        $product = $this->getProductByIdAdmin($product_id);
        if (!$product) {
            return null;
        }

        $currentStock = (int)$product['stock'];
        $importsAfter = $this->getImportQuantityAfter($product_id, $dateTime);
        $exportsAfter = $this->getExportQuantityAfter($product_id, $dateTime);

        return $currentStock - $importsAfter + $exportsAfter;
    }

    public function getLowStockProductsByThreshold($threshold) {
        $sql = "SELECT * FROM products WHERE stock <= :threshold AND status = 1 ORDER BY stock ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':threshold', $threshold, PDO::PARAM_INT);
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
    public function getProductsPaged($offset, $limit, $category = '', $search = '', $min_price = '', $max_price = '') {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE status = 1";

        if ($search !== '') {
            $sql .= " AND name LIKE :search";
        }
        if ($category !== '') {
            $sql .= " AND category_id = :category_id";
        }
        if ($min_price !== '') {
            $sql .= " AND selling_price >= :min_price";
        }
        if ($max_price !== '') {
            $sql .= " AND selling_price <= :max_price";
        }

        $sql .= " ORDER BY id DESC LIMIT :offset, :limit";

        $stmt = $this->conn->prepare($sql);
        if ($search !== '') {
            $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        }
        if ($category !== '') {
            $stmt->bindValue(':category_id', $category, PDO::PARAM_INT);
        }
        if ($min_price !== '') {
            $stmt->bindValue(':min_price', $min_price, PDO::PARAM_INT);
        }
        if ($max_price !== '') {
            $stmt->bindValue(':max_price', $max_price, PDO::PARAM_INT);
        }
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Hàm đếm tổng sản phẩm cho phân trang trang chủ
    public function countAll($category = '', $search = '', $min_price = '', $max_price = '') {
        $sql = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE status = 1";

        if ($search !== '') {
            $sql .= " AND name LIKE :search";
        }
        if ($category !== '') {
            $sql .= " AND category_id = :category_id";
        }
        if ($min_price !== '') {
            $sql .= " AND selling_price >= :min_price";
        }
        if ($max_price !== '') {
            $sql .= " AND selling_price <= :max_price";
        }

        $stmt = $this->conn->prepare($sql);
        if ($search !== '') {
            $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        }
        if ($category !== '') {
            $stmt->bindValue(':category_id', $category, PDO::PARAM_INT);
        }
        if ($min_price !== '') {
            $stmt->bindValue(':min_price', $min_price, PDO::PARAM_INT);
        }
        if ($max_price !== '') {
            $stmt->bindValue(':max_price', $max_price, PDO::PARAM_INT);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total'] ?? 0);
    }
    // --- CÁC HÀM DÀNH CHO ADMIN SITE ---

    // Lấy sản phẩm có phân trang VÀ tìm kiếm (Gộp làm 1 hàm duy nhất)
  public function getProductsPagedAdmin($offset, $limit, $search = '', $category_id = '', $min_cost = '', $max_cost = '', $min_margin = '', $max_margin = '', $min_price = '', $max_price = '') {
    $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.name LIKE :search";
    
    if (!empty($category_id)) {
        $sql .= " AND p.category_id = :cat_id";
    }
    if ($min_cost !== '') {
        $sql .= " AND p.gia_von >= :min_cost";
    }
    if ($max_cost !== '') {
        $sql .= " AND p.gia_von <= :max_cost";
    }
    if ($min_margin !== '') {
        $sql .= " AND p.loi_nhuan >= :min_margin";
    }
    if ($max_margin !== '') {
        $sql .= " AND p.loi_nhuan <= :max_margin";
    }
    if ($min_price !== '') {
        $sql .= " AND p.selling_price >= :min_price";
    }
    if ($max_price !== '') {
        $sql .= " AND p.selling_price <= :max_price";
    }

    $sql .= " ORDER BY p.id DESC LIMIT :offset, :limit";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    if (!empty($category_id)) {
        $stmt->bindValue(':cat_id', $category_id, PDO::PARAM_INT);
    }
    if ($min_cost !== '') {
        $stmt->bindValue(':min_cost', $min_cost);
    }
    if ($max_cost !== '') {
        $stmt->bindValue(':max_cost', $max_cost);
    }
    if ($min_margin !== '') {
        $stmt->bindValue(':min_margin', $min_margin);
    }
    if ($max_margin !== '') {
        $stmt->bindValue(':max_margin', $max_margin);
    }
    if ($min_price !== '') {
        $stmt->bindValue(':min_price', $min_price);
    }
    if ($max_price !== '') {
        $stmt->bindValue(':max_price', $max_price);
    }
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // Đếm tổng số sản phẩm có lọc theo tìm kiếm
  public function countAllAdmin($search = '', $category_id = '', $min_cost = '', $max_cost = '', $min_margin = '', $max_margin = '', $min_price = '', $max_price = '') {
    $sql = "SELECT COUNT(*) as total FROM products WHERE name LIKE :search";
    if (!empty($category_id)) {
        $sql .= " AND category_id = :cat_id";
    }
    if ($min_cost !== '') {
        $sql .= " AND gia_von >= :min_cost";
    }
    if ($max_cost !== '') {
        $sql .= " AND gia_von <= :max_cost";
    }
    if ($min_margin !== '') {
        $sql .= " AND loi_nhuan >= :min_margin";
    }
    if ($max_margin !== '') {
        $sql .= " AND loi_nhuan <= :max_margin";
    }
    if ($min_price !== '') {
        $sql .= " AND selling_price >= :min_price";
    }
    if ($max_price !== '') {
        $sql .= " AND selling_price <= :max_price";
    }
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    if (!empty($category_id)) {
        $stmt->bindValue(':cat_id', $category_id, PDO::PARAM_INT);
    }
    if ($min_cost !== '') {
        $stmt->bindValue(':min_cost', $min_cost);
    }
    if ($max_cost !== '') {
        $stmt->bindValue(':max_cost', $max_cost);
    }
    if ($min_margin !== '') {
        $stmt->bindValue(':min_margin', $min_margin);
    }
    if ($max_margin !== '') {
        $stmt->bindValue(':max_margin', $max_margin);
    }
    if ($min_price !== '') {
        $stmt->bindValue(':min_price', $min_price);
    }
    if ($max_price !== '') {
        $stmt->bindValue(':max_price', $max_price);
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
    $stmt->bindValue(':status', $data['status'] ?? 1, PDO::PARAM_INT);
    
    return $stmt->execute();
}

    public function updateProduct($data) {
        $sql = "UPDATE products SET 
                name = :name, category_id = :category_id, description = :description,
                unit = :unit, gia_von = :gia_von, loi_nhuan = :loi_nhuan, selling_price = :selling_price,
                stock = :stock, low_stock_threshold = :low_stock_threshold, image = :image, status = :status 
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':category_id', $data['category_id'], PDO::PARAM_INT);
        $stmt->bindValue(':description', $data['description'] ?? '');
        $stmt->bindValue(':unit', $data['unit'] ?? 'Cái');
        $stmt->bindValue(':gia_von', $data['gia_von']);
        $stmt->bindValue(':loi_nhuan', $data['loi_nhuan']);
        $stmt->bindValue(':selling_price', $data['selling_price']);
        $stmt->bindValue(':stock', $data['stock'], PDO::PARAM_INT);
        $stmt->bindValue(':low_stock_threshold', $data['low_stock_threshold'], PDO::PARAM_INT);
        $stmt->bindValue(':image', $data['image']);
        $stmt->bindValue(':status', $data['status'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $data['id'], PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateProductPricing($id, $gia_von, $loi_nhuan, $selling_price) {
        $sql = "UPDATE products SET gia_von = :gia_von, loi_nhuan = :loi_nhuan, selling_price = :selling_price WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':gia_von', $gia_von);
        $stmt->bindValue(':loi_nhuan', $loi_nhuan);
        $stmt->bindValue(':selling_price', $selling_price);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getLowStockProducts($threshold) {
        $sql = "SELECT * FROM products WHERE stock <= :threshold AND status = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['threshold' => $threshold]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // 1. Xóa sản phẩm
public function delete($id) {
    try {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    } catch (PDOException $e) {
        // Nếu có foreign key constraint (có đơn hàng) → return false
        if (strpos($e->getMessage(), 'FOREIGN KEY') !== false) {
            return false;
        }
        throw $e;
    }
}

public function hasPurchaseHistory($id) {
    $query = "SELECT COUNT(*) as total FROM purchase_details WHERE product_id = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? ((int)$row['total'] > 0) : false;
}

public function deleteProduct($id) {
    return $this->delete($id);
}

public function deleteOrHideProduct($id) {
    // Nếu đã nhập hàng (purchase_details) → ẩn sản phẩm
    if ($this->hasPurchaseHistory($id)) {
        return $this->updateStatus($id, 0);
    }
    
    // Chưa nhập hàng → thử xoá hẳn
    $deleteResult = $this->delete($id);
    
    // Nếu xoá thất bại (vì có order_details referencing) → ẩn sản phẩm
    if (!$deleteResult) {
        return $this->updateStatus($id, 0);
    }
    
    return true;
}

// 2. Thay đổi trạng thái (Ẩn/Hiện)
public function updateStatus($id, $status) {
    $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':status', $status, PDO::PARAM_INT);
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
// Lấy danh sách nhập hàng theo ngày cụ thể
// 1. Hàm lấy hoặc tạo phiếu mới cho ngày hôm nay (Gom tất cả nhập hàng vào 1 ID duy nhất trong ngày)
public function addPurchaseReceipt($admin_id, $product_id, $qty, $price) {
    $receipt_id = $this->createReceipt($admin_id);
    if (!$receipt_id) {
        return false;
    }

    return $this->addImportDetail($receipt_id, $product_id, $qty, $price);
}

public function createReceipt($admin_id, $receipt_date = null) {
    $sql = "INSERT INTO purchase_receipts (admin_id, receipt_date, status) VALUES (?, ?, 1)";
    $stmt = $this->conn->prepare($sql);
    $dateTime = $receipt_date ? date('Y-m-d H:i:s', strtotime($receipt_date)) : date('Y-m-d H:i:s');
    $stmt->execute([$admin_id, $dateTime]);
    return $this->conn->lastInsertId();
}

public function getOpenReceiptsByDate($date) {
    if (empty($date)) $date = date('Y-m-d');

    $sql = "SELECT id, receipt_date FROM purchase_receipts WHERE DATE(receipt_date) = :search_date AND status = 1 ORDER BY id DESC";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['search_date' => date('Y-m-d', strtotime(str_replace('/', '-', $date)))]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getReceiptsByDate($date) {
    if (empty($date)) $date = date('Y-m-d');

    $sql = "SELECT pr.id, pr.receipt_date, pr.status, a.username AS admin_name, COUNT(pd.id) AS item_count, SUM(pd.import_quantity * pd.import_price) AS total_amount
            FROM purchase_receipts pr
            LEFT JOIN purchase_details pd ON pr.id = pd.receipt_id
            LEFT JOIN users a ON pr.admin_id = a.id
            WHERE DATE(pr.receipt_date) = :search_date
            GROUP BY pr.id, pr.receipt_date, pr.status, a.username
            ORDER BY pr.id DESC";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['search_date' => date('Y-m-d', strtotime(str_replace('/', '-', $date)))]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getReceiptById($receipt_id) {
    $sql = "SELECT * FROM purchase_receipts WHERE id = :receipt_id LIMIT 1";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['receipt_id' => $receipt_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function getReceiptItems($receipt_id) {
    $sql = "SELECT pd.id as detail_id, pd.product_id, pd.import_quantity as quantity, pd.import_price,
                   p.name as product_name, p.unit
            FROM purchase_details pd
            JOIN products p ON pd.product_id = p.id
            WHERE pd.receipt_id = :receipt_id
            ORDER BY pd.id ASC";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['receipt_id' => $receipt_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function completeReceipt($receipt_id) {
    $receipt = $this->getReceiptById($receipt_id);
    if (!$receipt || $receipt['status'] != 1) {
        return false;
    }

    $sql = "SELECT pd.product_id, pd.import_quantity, pd.import_price, p.stock, p.gia_von, p.loi_nhuan
            FROM purchase_details pd
            JOIN products p ON pd.product_id = p.id
            WHERE pd.receipt_id = :receipt_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['receipt_id' => $receipt_id]);
    $details = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($details)) {
        return false;
    }

    $grouped = [];
    foreach ($details as $detail) {
        $pid = $detail['product_id'];
        if (!isset($grouped[$pid])) {
            $grouped[$pid] = [
                'stock' => (int)$detail['stock'],
                'cost' => (float)$detail['gia_von'],
                'margin' => (float)$detail['loi_nhuan'],
                'total_qty' => 0,
                'total_amount' => 0,
            ];
        }
        $grouped[$pid]['total_qty'] += (int)$detail['import_quantity'];
        $grouped[$pid]['total_amount'] += ((int)$detail['import_quantity'] * (float)$detail['import_price']);
    }

    try {
        $this->conn->beginTransaction();

        foreach ($grouped as $pid => $data) {
            $new_stock = $data['stock'] + $data['total_qty'];
            if ($new_stock <= 0) {
                continue;
            }
            $new_cost = (($data['stock'] * $data['cost']) + $data['total_amount']) / $new_stock;
            $new_selling_price = $new_cost * (1 + $data['margin'] / 100);

            $sqlUpdateProd = "UPDATE products SET stock = ?, gia_von = ?, selling_price = ? WHERE id = ?";
            $stmtUpdateProd = $this->conn->prepare($sqlUpdateProd);
            $stmtUpdateProd->execute([$new_stock, $new_cost, $new_selling_price, $pid]);
        }

        $sqlUpdateReceipt = "UPDATE purchase_receipts SET status = 2 WHERE id = ?";
        $stmtReceipt = $this->conn->prepare($sqlUpdateReceipt);
        $stmtReceipt->execute([$receipt_id]);

        $this->conn->commit();
        return true;
    } catch (Exception $e) {
        $this->conn->rollBack();
        return false;
    }
}

public function addImportDetail($receipt_id, $product_id, $qty, $price) {
    $sqlDetail = "INSERT INTO purchase_details (receipt_id, product_id, import_quantity, import_price) VALUES (?, ?, ?, ?)";
    $stmtDetail = $this->conn->prepare($sqlDetail);
    return $stmtDetail->execute([$receipt_id, $product_id, $qty, $price]);
}

// 3. Hàm lấy danh sách để hiện lên bảng (JOIN 2 bảng lại mới ra dữ liệu)
public function getImportsByDate($date) {
    // Nếu date rỗng thì lấy ngày hiện tại
    if (empty($date)) $date = date('Y-m-d');

    $sql = "SELECT pd.id as detail_id, pr.id as receipt_id, pr.receipt_date, pr.status as receipt_status,
                   pd.import_quantity as quantity, pd.import_price,
                   p.id as product_id, p.name as product_name, p.unit
            FROM purchase_receipts pr
            JOIN purchase_details pd ON pr.id = pd.receipt_id
            JOIN products p ON pd.product_id = p.id
            WHERE DATE(pr.receipt_date) = :search_date
            ORDER BY pd.id DESC";
            
    $stmt = $this->conn->prepare($sql);
    // Đảm bảo $date truyền vào có định dạng Y-m-d
    $formatted_date = date('Y-m-d', strtotime(str_replace('/', '-', $date)));
    $stmt->execute(['search_date' => $formatted_date]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getImportDetailById($detail_id) {
    $sql = "SELECT pd.id as detail_id, pd.receipt_id, pd.product_id, pd.import_quantity as quantity,
                   pd.import_price, p.name as product_name, p.unit, pr.status as receipt_status
            FROM purchase_details pd
            JOIN products p ON pd.product_id = p.id
            JOIN purchase_receipts pr ON pd.receipt_id = pr.id
            WHERE pd.id = :detail_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['detail_id' => $detail_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function updateImportDetail($detail_id, $qty, $price) {
    $existing = $this->getImportDetailById($detail_id);
    if (!$existing || $existing['receipt_status'] != 1) {
        return false;
    }

    $sql = "UPDATE purchase_details SET import_quantity = ?, import_price = ? WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute([$qty, $price, $detail_id]);
}

public function deleteImportDetail($detail_id) {
    $existing = $this->getImportDetailById($detail_id);
    if (!$existing || $existing['receipt_status'] != 1) {
        return false;
    }

    $sql = "DELETE FROM purchase_details WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute([$detail_id]);
}
// Lấy danh sách đánh giá của 1 sản phẩm
public function getReviewsByProductId($product_id) {
    $sql = "SELECT r.*, u.username 
            FROM product_reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.product_id = :p_id AND r.status = 1
            ORDER BY r.created_at DESC";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['p_id' => $product_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Tính điểm trung bình (Ví dụ: 4.5 sao)
public function getAverageRating($product_id) {
    $sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews 
            FROM product_reviews 
            WHERE product_id = :p_id AND status = 1";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['p_id' => $product_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
// public function addImportDetail($receipt_id, $product_id, $qty, $price) {
//     try {
//         // 1. Lưu vào bảng purchase_details (Dùng chung receipt_id)
//         $sqlDetail = "INSERT INTO purchase_details (receipt_id, product_id, import_quantity, import_price) VALUES (?, ?, ?, ?)";
//         $this->conn->prepare($sqlDetail)->execute([$receipt_id, $product_id, $qty, $price]);

//         // 2. Tính toán giá vốn & Cập nhật kho (Giữ nguyên logic bình quân gia quyền của mày)
//         $product = $this->getProductByIdAdmin($product_id);
//         $new_stock = $product['stock'] + $qty;
//         $new_cost = (($product['stock'] * $product['gia_von']) + ($qty * $price)) / $new_stock;
//         $new_selling_price = $new_cost * (1 + $product['loi_nhuan'] / 100);

//         $sqlUp = "UPDATE products SET stock = ?, gia_von = ?, selling_price = ? WHERE id = ?";
//         return $this->conn->prepare($sqlUp)->execute([$new_stock, $new_cost, $new_selling_price, $product_id]);
//     } catch (Exception $e) { return false; }
// }
}