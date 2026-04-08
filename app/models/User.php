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
    public $phone;
    public $address_default;
    public $role;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Hàm xử lý Đăng nhập
// Hàm lấy thông tin chi tiết của 1 người dùng qua ID
public function getUserById($id) {
    $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
public function login($username, $password) {
    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Dùng password_verify nếu bạn có mã hóa, hoặc so sánh trực tiếp nếu để text thuần
    if ($user && $password == $user['password']) {
        return $user; 
    }
    return false;
}
public function checklogin($username, $password) {
    $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Kiểm tra nếu có user và giải mã mật khẩu
    if ($user && password_verify($password, $user['password'])) {
        return $user; // Trả về mảng thông tin user nếu đúng
    }
    return false; // Sai tài khoản hoặc mật khẩu
}

    // 1. Kiểm tra Email tồn tại
public function emailExists($email) {
    $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}

// 2. Kiểm tra Số điện thoại tồn tại
public function phoneExists($phone) {
    $query = "SELECT id FROM " . $this->table_name . " WHERE phone = :phone LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':phone', $phone);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}
public function usernameExists($username) {
    $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}
// 3. Đăng ký người dùng mới
public function register($full_name, $username, $email, $password, $phone, $address_default) {
    $query = "INSERT INTO " . $this->table_name . " (full_name, username, email, password, phone, address_default, role, status) 
              VALUES (:full_name, :username, :email, :password, :phone, :address_default, '0', 1)";
    
    $stmt = $this->conn->prepare($query);
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $stmt->bindParam(':full_name', $full_name);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':address_default', $address_default);
    return $stmt->execute();
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

public function getAllUsers() {
    $query = "SELECT id, full_name, username, email, phone, role, status FROM " . $this->table_name . " ORDER BY role DESC, id DESC";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function createUser($full_name, $username, $email, $password, $phone, $role = 0) {
    $query = "INSERT INTO " . $this->table_name . " (full_name, username, email, password, phone, role, status) 
              VALUES (:full_name, :username, :email, :password, :phone, :role, 1)";
    $stmt = $this->conn->prepare($query);
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $stmt->bindParam(':full_name', $full_name);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':role', $role, PDO::PARAM_INT);
    return $stmt->execute();
}

public function resetPassword($id, $password) {
    $query = "UPDATE " . $this->table_name . " SET password = :password WHERE id = :id";
    $stmt = $this->conn->prepare($query);
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}

public function updateStatus($id, $status) {
    $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':status', $status, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}

}
?>