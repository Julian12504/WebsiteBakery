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
        // Thêm ";charset=utf8" vào cuối chuỗi DSN
        $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8", $this->username, $this->password);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $exception) {
        echo "Connection error: " . $exception->getMessage();
    }
    return $this->conn;
}

}