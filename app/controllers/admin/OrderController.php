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
        $status = $_POST['status'];
        
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