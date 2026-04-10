<?php
class OrderController {
    private $orderModel;

    public function __construct($model) {
        $this->orderModel = $model;
    }

    public function index() {
        // Lọc đơn hàng theo phường, thời gian hoặc tình trạng (Yêu cầu II)
        $status = $_GET['status'] ?? null;
        $from_date = $_GET['from_date'] ?? null;
        $to_date = $_GET['to_date'] ?? null;
        
        $orders = $this->orderModel->getAllOrdersAdmin($status, $from_date, $to_date);
        include '../app/views/admin/orders.php';
    }

  public function updateStatus() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = $_POST['id'];
        $status = intval($_POST['status']);
        
        // Lấy trạng thái hiện tại của đơn hàng
        $order = $this->orderModel->getOrderById($id);
        $currentStatus = intval($order['status']);
        
        // Validation: Kiểm tra chuyển trạng thái hợp lệ
        $isValid = false;
        
        // Trạng thái cuối (không thay đổi)
        if ($currentStatus === 5 || $currentStatus === 4 || $currentStatus === 3) {
            // Không thay đổi
            echo "<script>alert('❌ Trạng thái này không thể thay đổi!'); history.back();</script>";
            exit();
        }
        
        // Status 0 (Chờ xác nhận): có thể chọn 0, 1, 4
        if ($currentStatus === 0 && in_array($status, [0, 1, 4])) {
            $isValid = true;
        }
        
        // Status 1 (Chờ lấy hàng): có thể chọn 1, 2, 4
        if ($currentStatus === 1 && in_array($status, [1, 2, 4])) {
            $isValid = true;
        }
        
        // Status 2 (Chờ giao hàng): có thể chọn 2, 5, 4
        if ($currentStatus === 2 && in_array($status, [2, 5, 4])) {
            $isValid = true;
        }
        
        if (!$isValid) {
            echo "<script>alert('❌ Chuyển trạng thái không hợp lệ!'); history.back();</script>";
            exit();
        }
        
        // Dòng này sẽ hết lỗi sau khi bạn thêm hàm vào Model
        $this->orderModel->updateStatus($id, $status); 
        
        header("Location: admin.php?url=orders&msg=status_updated");
        exit();
    }
}

    public function orderDetail() {
    // 1. Lấy ID từ URL
    $id = $_GET['id'] ?? 0;
    
    // 2. Gọi Model lấy dữ liệu (Đảm bảo $this->orderModel đã được khởi tạo trong __construct)
    $order = $this->orderModel->getOrderById($id); 
    $orderItems = $this->orderModel->getOrderItems($id); 

    // 3. Nếu không tìm thấy đơn hàng thì về danh sách
    if (!$order) {
        header("Location: admin.php?url=orders&msg=not_found");
        exit();
    }

    // 4. Truyền biến vào view
    extract(compact('order', 'orderItems'));
    
    // 5. Load file giao diện
    include '../app/views/admin/order_detail.php';
}
}