<?php
// app/controllers/AuthController.php

// Đảm bảo BaseController đã được nạp (thường thì autoload hoặc index.php đã làm)
// require_once __DIR__ . '/BaseController.php';
// Đảm bảo UserModel đã được nạp (thường thì autoload đã làm)
// require_once __DIR__ . '/../models/User.php';

class AuthController extends BaseController {

    private $userModel;

    public function __construct() {
        parent::__construct(); // Gọi constructor của BaseController để có $this->db
        $this->userModel = new User($this->db); // Khởi tạo UserModel
    }

    /**
     * Hiển thị trang đăng nhập
     */
    public function login() {
        // Nếu đã đăng nhập rồi thì chuyển hướng về trang chủ
        if ($this->getCurrentUser()) {
            $this->redirect('home/index'); // Hoặc trang dashboard tùy bạn cấu hình
            return; // Quan trọng: Dừng thực thi sau khi chuyển hướng
        }

        $data = [
            'title' => 'Đăng nhập'
            // Thêm các dữ liệu khác nếu cần cho view login
        ];
        // Gọi view với layout 'auth_layout'
        $this->view('auth/login', $data, 'auth_layout');
    }

    /**
     * Xử lý thông tin đăng nhập từ form
     */
    public function processLogin() {
        // ... (phần kiểm tra đã đăng nhập và phương thức POST)

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $this->setFlashMessage('error', 'Vui lòng nhập tên đăng nhập và mật khẩu.');
            $this->redirect('auth/login');
            return;
        }

        $user = $this->userModel->findByUsername($username);

        if ($user) {
            // *** BỔ SUNG KIỂM TRA TRẠNG THÁI TÀI KHOẢN ***
            if ($user['trang_thai'] == 0) { // 0 là trạng thái bị khóa
                $this->setFlashMessage('error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.');
                $this->redirect('auth/login');
                return;
            }

            // CẢNH BÁO: So sánh mật khẩu plain text - KHÔNG AN TOÀN!
            if ($password === $user['mat_khau']) {
                // ... (phần lưu session và redirect như cũ)
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['ten_dang_nhap'];
                $_SESSION['fullname'] = $user['ho_ten'];
                $_SESSION['role'] = $user['quyen'];

                $this->setFlashMessage('success', 'Đăng nhập thành công!');
                $this->redirect('home/index'); 
            } else {
                $this->setFlashMessage('error', 'Tên đăng nhập hoặc mật khẩu không đúng.');
                $this->redirect('auth/login');
            }
        } else {
            $this->setFlashMessage('error', 'Tên đăng nhập hoặc mật khẩu không đúng.');
            $this->redirect('auth/login');
        }
    }

    /**
     * Xử lý đăng xuất
     */
    public function logout() {
        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();

        $this->setFlashMessage('success', 'Bạn đã đăng xuất thành công.');
        $this->redirect('auth/login'); // Chuyển hướng về trang đăng nhập
    }
}
?>