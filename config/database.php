<?php
class Database {
    private $host = "localhost";
    private $db_name = "webtiembanh";
    private $username = "root";
    private $password = "";
    public $conn;

 public function getConnection() {
    $this->conn = null;
    try {
        // Thêm ";charset=utf8mb4" và thiết lập init command để đảm bảo kết nối dùng UTF-8
        $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ];
        $this->conn = new PDO($dsn, $this->username, $this->password, $options);
    } catch(PDOException $exception) {
        echo "Connection error: " . $exception->getMessage();
    }
    return $this->conn;
}

}