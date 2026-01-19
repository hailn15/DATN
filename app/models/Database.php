<?php
// app/models/Database.php

class Database {
    private static $instance = null;
    private $conn;

    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    private function __construct() {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ];
    
        try {
            $this->conn = new PDO($dsn, $this->user, $this->pass, $options);
            // error_log("Database connection successful to " . $this->dbname); // Ghi log thành công (tùy chọn)
        } catch (PDOException $e) {
            // GHI LOG LỖI KẾT NỐI
            error_log("FATAL DATABASE CONNECTION ERROR: " . $e->getMessage() . " (DSN: {$dsn}, User: {$this->user})");
            // HIỂN THỊ THÔNG BÁO LỖI RÕ RÀNG CHO USER (KHI DEBUG)
            // TRONG PRODUCTION, BẠN NÊN HIỂN THỊ MỘT THÔNG BÁO CHUNG CHUNG HƠN
            die("Không thể kết nối đến Cơ sở dữ liệu. Vui lòng kiểm tra cấu hình và thông tin kết nối. Chi tiết lỗi: " . $e->getMessage());
        }
    }
    

    // Singleton pattern: Đảm bảo chỉ có một instance của Database
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Lấy đối tượng PDO connection
    public function getConnection() {
        return $this->conn;
    }

    // Ngăn chặn clone và unserialize để duy trì Singleton
    private function __clone() {}
    public function __wakeup() {}
}
?>