<?php
// app/controllers/BaseController.php

// Đảm bảo Database class đã được nạp nếu chưa có autoload cho models
// require_once __DIR__ . '/../models/Database.php';

abstract class BaseController {

    protected $db;

    public function __construct() {
        // Lấy instance kết nối DB
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Load view và truyền dữ liệu vào layout
     * @param string $view Tên file view (vd: 'doi_tuong/index')
     * @param array $data Dữ liệu cần truyền cho view
     * @param string $layout Tên file layout (vd: 'main', 'auth_layout'). Mặc định là 'main'.
     */
    protected function view($view, $data = [], $layout = 'main') { // Thêm tham số $layout, mặc định là 'main'
        // Truyền tên class controller hiện tại vào data cho layout
        $data['currentController'] = get_class($this); // Ví dụ: HomeController, DoiTuongController

        // Giải nén mảng $data thành các biến riêng lẻ
        extract($data);

        $viewPath = '../app/views/' . $view . '.php';

        if (file_exists($viewPath)) {
            ob_start();
            require $viewPath;
            $content = ob_get_clean(); // $content này sẽ được nhúng vào layout

            // Sử dụng biến $layout để xác định file layout
            $layoutPath = '../app/views/layouts/' . $layout . '.php';
            if (file_exists($layoutPath)) {
                require $layoutPath; // Load layout và $content sẽ được hiển thị bên trong nó
            } else {
                 die("Layout file '{$layoutPath}' không tồn tại.");
            }

        } else {
            die("View file '{$viewPath}' không tồn tại.");
        }
    }

    protected function redirect($path) {
        $location = url($path);
        $location = filter_var($location, FILTER_SANITIZE_URL);
        header('Location: ' . $location);
        exit();
    }

    protected function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            $this->setFlashMessage('error', 'Vui lòng đăng nhập để tiếp tục.');
            $this->redirect('auth/login');
        }
    }

    protected function getCurrentUser() {
        return getCurrentUser(); // Gọi hàm toàn cục đã định nghĩa ở functions.php
    }

    protected function setFlashMessage($type, $message) {
        $_SESSION['flash_message'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    protected function getFlashMessage() {
        if (isset($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            return $message;
        }
        return null;
    }
}
?>