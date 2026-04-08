<?php
class ProductController {
    private $productModel;

    public function __construct($productModel) {
        $this->productModel = $productModel;
    }

    // 1. Hiển thị danh sách sản phẩm (có tìm kiếm & phân trang)
    public function index() {
        $search = $_GET['search'] ?? '';
        $category_id = $_GET['category_id'] ?? ''; // Thêm lọc theo danh mục
        $min_cost = isset($_GET['min_cost']) ? $_GET['min_cost'] : '';
        $max_cost = isset($_GET['max_cost']) ? $_GET['max_cost'] : '';
        $min_margin = isset($_GET['min_margin']) ? $_GET['min_margin'] : '';
        $max_margin = isset($_GET['max_margin']) ? $_GET['max_margin'] : '';
        $min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
        $max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Cho phép đổi số lượng hiển thị
        $offset = ($page - 1) * $limit;

        // Gọi Model lấy dữ liệu
        $products = $this->productModel->getProductsPagedAdmin($offset, $limit, $search, $category_id, $min_cost, $max_cost, $min_margin, $max_margin, $min_price, $max_price);
        $totalProducts = $this->productModel->countAllAdmin($search, $category_id, $min_cost, $max_cost, $min_margin, $max_margin, $min_price, $max_price);
        $totalPages = ceil($totalProducts / $limit);
        
        $categories = $this->productModel->getAllCategories();
        include '../app/views/admin/product.php';
    }

    public function priceManagement() {
        $search = $_GET['search'] ?? '';
        $category_id = $_GET['category_id'] ?? '';
        $min_cost = isset($_GET['min_cost']) ? $_GET['min_cost'] : '';
        $max_cost = isset($_GET['max_cost']) ? $_GET['max_cost'] : '';
        $min_margin = isset($_GET['min_margin']) ? $_GET['min_margin'] : '';
        $max_margin = isset($_GET['max_margin']) ? $_GET['max_margin'] : '';
        $min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
        $max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;

        $products = $this->productModel->getProductsPagedAdmin($offset, $limit, $search, $category_id, $min_cost, $max_cost, $min_margin, $max_margin, $min_price, $max_price);
        $totalProducts = $this->productModel->countAllAdmin($search, $category_id, $min_cost, $max_cost, $min_margin, $max_margin, $min_price, $max_price);
        $totalPages = ceil($totalProducts / $limit);
        $categories = $this->productModel->getAllCategories();
        include '../app/views/admin/price_management.php';
    }

    public function updateProfit() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? 0;
            $gia_von = (float)($_POST['gia_von'] ?? 0);
            $loi_nhuan = (float)($_POST['loi_nhuan'] ?? 0);
            $selling_price = $gia_von + ($gia_von * $loi_nhuan / 100);

            if ($this->productModel->updateProductPricing($id, $gia_von, $loi_nhuan, $selling_price)) {
                header("Location: admin.php?url=price_management&msg=profit_updated");
                exit();
            }
        }
    }

    // 2. Thêm sản phẩm mới
public function add() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $gia_von = (float)$_POST['gia_von'];
        $loi_nhuan = (float)$_POST['loi_nhuan'];
        $selling_price = $gia_von + ($gia_von * $loi_nhuan / 100);

        // Xử lý ảnh
        $image = 'default.jpg';
// Sửa trong hàm add() VÀ update()
if (!empty($_FILES['image']['name'])) {
    $image = time() . '_' . $_FILES['image']['name'];
    // Lưu vào thư mục images/ (nằm cùng cấp với admin.php)
    move_uploaded_file($_FILES['image']['tmp_name'], "images/" . $image); 
}

        $data = [
            'name'                => $_POST['name'],
            'category_id'         => $_POST['category_id'],
            'description'         => $_POST['description'] ?? '',
            'unit'                => $_POST['unit'] ?? 'Cái',
            'image'               => $image,
            'gia_von'             => $gia_von,
            'loi_nhuan'           => $loi_nhuan,
            'selling_price'       => $selling_price,
            'stock'               => $_POST['stock'] ?? 0,
            'low_stock_threshold' => $_POST['low_stock_threshold'] ?? 5,
            'status'              => $_POST['status'] ?? 1
        ];

        if ($this->productModel->insertProduct($data)) {
            header("Location: admin.php?url=products&msg=success");
            exit();
        } else {
            echo "Có lỗi xảy ra khi lưu vào database.";
        }
    }
}
    // 3. Load trang Sửa sản phẩm
    public function edit() {
        $id = $_GET['id'] ?? 0;
        $product = $this->productModel->getProductByIdAdmin($id);
        if (!$product) {
            header("Location: admin.php?url=products");
            exit();
        }
        $categories = $this->productModel->getAllCategories(); 
        include '../app/views/admin/edit_product.php';
    }

    // 4. Xử lý Cập nhật sản phẩm
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $gia_von = (float)($_POST['gia_von'] ?? 0);
            $loi_nhuan = (float)($_POST['loi_nhuan'] ?? 0);
            $selling_price = $gia_von + ($gia_von * $loi_nhuan / 100);

            // Giữ ảnh cũ hoặc thay ảnh mới
            $currentImage = $_POST['current_image'] ?? 'default.jpg';
            $image = $currentImage;
            $removeImage = isset($_POST['remove_image']) && $_POST['remove_image'] === '1';

            if (!empty($_FILES['image']['name'])) {
                $image = time() . '_' . $_FILES['image']['name'];
                move_uploaded_file($_FILES['image']['tmp_name'], "images/" . $image);
            } elseif ($removeImage) {
                if (!empty($currentImage) && $currentImage !== 'default.jpg' && file_exists("images/" . $currentImage)) {
                    @unlink("images/" . $currentImage);
                }
                $image = 'default.jpg';
            }

            $data = [
                'id' => $id,
                'name' => $_POST['name'] ?? '',
                'category_id' => $_POST['category_id'] ?? 0,
                'description' => $_POST['description'] ?? '',
                'unit' => $_POST['unit'] ?? 'Cái',
                'gia_von' => $gia_von,
                'loi_nhuan' => $loi_nhuan,
                'selling_price' => $selling_price,
                'stock' => (int)($_POST['stock'] ?? 0),
                'low_stock_threshold' => (int)($_POST['low_stock_threshold'] ?? 5),
                'status' => isset($_POST['status']) ? (int)$_POST['status'] : 0,
                'image' => $image
            ];

            if ($this->productModel->updateProduct($data)) {
                header("Location: admin.php?url=products&msg=update_success");
                exit();
            }
        }
    }

    // 5. Xóa 1 sản phẩm
    public function delete() {
        $id = $_GET['id'] ?? 0;
        if ($this->productModel->deleteOrHideProduct($id)) {
            header("Location: admin.php?url=products&msg=delete_success");
        }
    }

    // 6. Tác vụ gom nhóm (Xóa nhiều, Ẩn nhiều)
    public function bulk_action() {
        $type = $_GET['type'] ?? '';
        $ids = explode(',', $_GET['ids'] ?? '');
        
        if (!empty($ids) && !empty($type)) {
            foreach ($ids as $id) {
                if ($type === 'delete') {
                    $this->productModel->deleteOrHideProduct($id);
                } elseif ($type === 'hide') {
                    $this->productModel->updateStatus($id, 0);
                }
            }
            header("Location: admin.php?url=products&msg=bulk_success");
            exit();
        }
    }
    // 7. Giao diện Nhập hàng All-in-one
public function import_product() {
    $search_date = $_GET['search_date'] ?? date('Y-m-d');
    $viewReceiptId = isset($_GET['view']) ? (int) $_GET['view'] : 0;

    $products = $this->productModel->getAllProductsAdmin();
    $dailyImports = $this->productModel->getImportsByDate($search_date);
    $receipts = $this->productModel->getReceiptsByDate($search_date);

    $viewReceipt = null;
    $viewItems = [];
    if ($viewReceiptId > 0) {
        $viewReceipt = $this->productModel->getReceiptById($viewReceiptId);
        if ($viewReceipt) {
            $viewItems = $this->productModel->getReceiptItems($viewReceiptId);
        }
    }

    include '../app/views/admin/import_product.php';
}

// 8. Xử lý lưu phiếu nhập
public function process_import() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $admin_id = $_SESSION['admin_id'];
        $product_id = $_POST['product_id'];
        $quantity = (int)$_POST['quantity'];
        $import_price = (float)$_POST['import_price'];

        if ($this->productModel->addPurchaseReceipt($admin_id, $product_id, $quantity, $import_price)) {
            header("Location: admin.php?url=import_product&search_date=" . date('Y-m-d') . "&msg=success");
            exit();
        } else {
            echo "Lỗi nhập hàng.";
        }
    }
}
public function process_import_all() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_ids'])) {
        $admin_id = $_SESSION['admin_id'];
        $product_ids = $_POST['product_ids'];
        $quantities = $_POST['quantities'];
        $prices = $_POST['prices'];

        $receipt_id = $this->productModel->createReceipt($admin_id);

        $successCount = 0;
        for ($i = 0; $i < count($product_ids); $i++) {
            if ($this->productModel->addImportDetail($receipt_id, $product_ids[$i], $quantities[$i], $prices[$i])) {
                $successCount++;
            }
        }

        header("Location: admin.php?url=import_product&search_date=" . urlencode(date('Y-m-d')) . "&msg=success");
        exit();
    }
}

public function editImport() {
    $id = $_GET['id'] ?? 0;
    $search_date = $_GET['search_date'] ?? date('Y-m-d');
    $editImport = $this->productModel->getImportDetailById($id);
    if (!$editImport || $editImport['receipt_status'] != 1) {
        header("Location: admin.php?url=import_product&search_date=" . urlencode($search_date) . "&msg=cannot_edit_completed");
        exit();
    }
    $products = $this->productModel->getAllProductsAdmin();
    $dailyImports = $this->productModel->getImportsByDate($search_date);
    include '../app/views/admin/import_product.php';
}

public function updateImport() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = $_POST['id'];
        $quantity = (int)$_POST['quantity'];
        $import_price = (float)$_POST['import_price'];
        $search_date = $_POST['search_date'] ?? date('Y-m-d');

        if ($this->productModel->updateImportDetail($id, $quantity, $import_price)) {
            header("Location: admin.php?url=import_product&search_date=" . urlencode($search_date) . "&msg=update_success");
        } else {
            echo "Cập nhật thất bại.";
        }
    }
}

public function deleteImport() {
    $id = $_GET['id'] ?? 0;
    $search_date = $_GET['search_date'] ?? date('Y-m-d');

    if ($this->productModel->deleteImportDetail($id)) {
        header("Location: admin.php?url=import_product&search_date=" . urlencode($search_date) . "&msg=delete_success");
        exit();
    } else {
        echo "Xóa thất bại hoặc phiếu đã hoàn thành.";
    }
}

public function completeImport() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $receipt_id = (int) ($_POST['receipt_id'] ?? 0);
        $search_date = $_POST['search_date'] ?? date('Y-m-d');

        if ($this->productModel->completeReceipt($receipt_id)) {
            header("Location: admin.php?url=import_product&search_date=" . urlencode($search_date) . "&msg=completed");
            exit();
        }
        echo "Hoàn thành phiếu thất bại.";
    }
}
}