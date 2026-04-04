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
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Cho phép đổi số lượng hiển thị
        $offset = ($page - 1) * $limit;

        // Gọi Model lấy dữ liệu
        $products = $this->productModel->getProductsPagedAdmin($offset, $limit, $search, $category_id);
        $totalProducts = $this->productModel->countAllAdmin($search, $category_id); 
        $totalPages = ceil($totalProducts / $limit);
        
        $categories = $this->productModel->getAllCategories();
        include '../app/views/admin/product.php';
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
            $gia_von = $_POST['gia_von'];
            $loi_nhuan = $_POST['loi_nhuan'];
            $selling_price = $gia_von + ($gia_von * $loi_nhuan / 100);

            // Giữ ảnh cũ hoặc thay ảnh mới
            $image = $_POST['current_image'];
            if (!empty($_FILES['image']['name'])) {
                $image = time() . '_' . $_FILES['image']['name'];
                move_uploaded_file($_FILES['image']['tmp_name'], "public/images/" . $image);
            }

            $data = [
                'id' => $id,
                'name' => $_POST['name'],
                'category_id' => $_POST['category_id'],
                'description' => $_POST['description'],
                'gia_von' => $gia_von,
                'loi_nhuan' => $loi_nhuan,
                'selling_price' => $selling_price,
                'stock' => $_POST['stock'],
                'status' => $_POST['status'],
                'image' => $image
            ];

            if ($this->productModel->updateProduct($data)) {
                header("Location: admin.php?url=products&msg=update_success");
            }
        }
    }

    // 5. Xóa 1 sản phẩm
    public function delete() {
        $id = $_GET['id'] ?? 0;
        if ($this->productModel->deleteProduct($id)) {
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
                    $this->productModel->deleteProduct($id);
                } elseif ($type === 'hide') {
                    $this->productModel->updateStatus($id, 0); // Giả sử model có hàm updateStatus
                }
            }
            header("Location: admin.php?url=products&msg=bulk_success");
        }
    }
}