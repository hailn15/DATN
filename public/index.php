<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Load cấu hình
require_once '../config/config.php';

// Load các hàm tiện ích (nếu có)
require_once '../core/functions.php'; // Đảm bảo getCurrentUser() sẽ được load từ đây

// Load bộ định tuyến
require_once '../core/Router.php';

// Load Base Controller (để các controller khác kế thừa)
require_once '../app/controllers/BaseController.php';

// Tự động load Model khi cần
spl_autoload_register(function ($className) {
    $file = '../app/models/' . $className . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Tự động load Controller khi router gọi
spl_autoload_register(function ($className) {
    $file = '../app/controllers/' . $className . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});


// Khởi tạo và chạy Router
$router = new Router();
$router->dispatch();

?>