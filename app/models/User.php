<?php
class User {
    private $conn;
    private $table_name = "users";

    // Các thuộc tính khớp với 11 cột trong database của bạn
    public $id;
    public $username;
    public $password;
    public $email;
    public $full_name;
    public $role;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Hàm xử lý Đăng nhập
    public function login($username, $password) {
        // Truy vấn kiểm tra username và trạng thái hoạt động (status = 1)
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username AND status = 1 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        
        // Làm sạch dữ liệu đầu vào
        $username = htmlspecialchars(strip_tags($username));
        $stmt->bindParam(':username', $username);
        
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Kiểm tra mật khẩu (Ở đây tui để so sánh trực tiếp vì data mẫu bạn nhập tay là text thuần)
            // Lưu ý: Nếu sau này dùng password_hash() thì phải dùng password_verify()
            if ($password == $row['password']) {
                return $row; // Trả về toàn bộ thông tin người dùng
            }
        }
        return false; // Sai tài khoản hoặc mật khẩu
    }

    // Hàm Đăng ký (Để dùng cho trang register sau này)
    public function register($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                (username, password, email, full_name, role, status) 
                VALUES (:username, :password, :email, :full_name, 'customer', 1)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':full_name', $data['full_name']);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
// Hàm cập nhật thông tin người dùng vào Database
public function update($data) {
    $query = "UPDATE " . $this->table_name . " 
              SET full_name = :full_name, 
                  email = :email, 
                  phone = :phone, 
                  address_default = :address_default 
              WHERE id = :id";
    
    $stmt = $this->conn->prepare($query);
    
    // Bind các giá trị
    $stmt->bindParam(':full_name', $data['full_name']);
    $stmt->bindParam(':email', $data['email']);
    $stmt->bindParam(':phone', $data['phone']);
    $stmt->bindParam(':address_default', $data['address_default']);
    $stmt->bindParam(':id', $data['id']);
    
    if($stmt->execute()) {
        return true;
    }
    return false;
}
}
?>