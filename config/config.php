<?php
define('DB_HOST', 'localhost'); 
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'qldoituongchinhsach');

// Khi chạy với php -S -t public, thư mục public là gốc, nên BASE_URL để trống
define('BASE_URL', '');

// Cấu hình khác
define('APP_NAME', 'AN SINH XÃ HỘI');
define('DEFAULT_CONTROLLER', 'HomeController');
define('DEFAULT_ACTION', 'index');

// Bật/tắt hiển thị lỗi PHP (chỉ bật khi đang phát triển)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Thiết lập múi giờ
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Khởi động session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>