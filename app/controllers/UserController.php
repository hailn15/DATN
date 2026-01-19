<?php
// app/controllers/UserController.php

class UserController extends BaseController {

    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->checkAuth();
        $this->userModel = new User($this->db);

        // Bảo vệ: Chỉ admin mới có quyền truy cập controller này
        $currentUser = $this->getCurrentUser();
        // if (!$currentUser || $currentUser['role'] !== 'admin') {
        //     $this->setFlashMessage('error', 'Bạn không có quyền truy cập trang này.');
        //     $this->redirect('home/index');
        //     exit();
        // }
    }

    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $filters = [
            'searchTerm' => $_GET['search'] ?? '',
            'quyen' => $_GET['quyen'] ?? ''
        ];

        $result = $this->userModel->getAll($filters, $limit, $offset);
        $users = $result['data'];
        $totalRecords = $result['total'];
        $totalPages = ceil($totalRecords / $limit);

        $pagination = [
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'limit' => $limit,
            'totalRecords' => $totalRecords,
            'filters' => $filters
        ];
        
        $quyenOptions = ['admin' => 'Admin', 'canbo' => 'Cán bộ', 'quantri_hethong' => 'Quản trị Hệ thống'];

        $this->view('user/index', [
            'title' => 'Quản lý Người dùng',
            'users' => $users,
            'pagination' => $pagination,
            'quyenOptions' => $quyenOptions,
            'currentFilters' => $filters
        ]);
    }

    public function create() {
        $this->view('user/create', [
            'title' => 'Thêm Người dùng mới',
            'quyenOptions' => ['admin' => 'Admin', 'canbo' => 'Cán bộ', 'quantri_hethong' => 'Quản trị Hệ thống'],
            'oldData' => $_SESSION['old_form_data']['user_create'] ?? [],
            'errors' => $_SESSION['form_errors']['user_create'] ?? []
        ]);
        unset($_SESSION['old_form_data']['user_create'], $_SESSION['form_errors']['user_create']);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('user/index');
            return;
        }

        $data = $_POST;
        $errors = $this->validateUserData($data);

        if (!empty($errors)) {
            $_SESSION['old_form_data']['user_create'] = $data;
            $_SESSION['form_errors']['user_create'] = $errors;
            $this->setFlashMessage('error', 'Thêm mới thất bại. Vui lòng kiểm tra lại lỗi.');
            $this->redirect('user/create');
            return;
        }

        // Đổi tên key để khớp với model
        $data['mat_khau_plain'] = $data['mat_khau']; 

        if ($this->userModel->create($data)) {
            $this->setFlashMessage('success', 'Tạo người dùng mới thành công!');
            $this->redirect('user/index');
        } else {
            $this->setFlashMessage('error', 'Lỗi khi tạo người dùng. Có thể tên đăng nhập đã tồn tại.');
            $_SESSION['old_form_data']['user_create'] = $data;
            $this->redirect('user/create');
        }
    }

    public function edit($id) {
        $user = $this->userModel->findById($id);
        if (!$user) {
            $this->setFlashMessage('error', 'Không tìm thấy người dùng.');
            $this->redirect('user/index');
            return;
        }
        
        // Lấy tất cả thông tin (trừ mật khẩu) để hiển thị trong form
        $fullUserData = $this->userModel->findByUsername($user['ten_dang_nhap']);
        unset($fullUserData['mat_khau']);


        $this->view('user/edit', [
            'title' => 'Chỉnh sửa Người dùng: ' . htmlspecialchars($user['ho_ten']),
            'user' => $fullUserData,
            'quyenOptions' => ['admin' => 'Admin', 'canbo' => 'Cán bộ', 'quantri_hethong' => 'Quản trị Hệ thống'],
            'errors' => $_SESSION['form_errors']['user_edit_'.$id] ?? []
        ]);
        unset($_SESSION['form_errors']['user_edit_'.$id]);
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('user/index');
            return;
        }

        $data = $_POST;
        $errors = $this->validateUserData($data, $id);

        if (!empty($errors)) {
            $_SESSION['form_errors']['user_edit_'.$id] = $errors;
            $this->setFlashMessage('error', 'Cập nhật thất bại. Vui lòng kiểm tra lại lỗi.');
            $this->redirect('user/edit/' . $id);
            return;
        }
        
        // Đổi tên key để khớp với model
        if (!empty($data['mat_khau'])) {
            $data['mat_khau_plain'] = $data['mat_khau'];
        }

        if ($this->userModel->update($id, $data)) {
            $this->setFlashMessage('success', 'Cập nhật thông tin người dùng thành công!');
            $this->redirect('user/index');
        } else {
            $this->setFlashMessage('error', 'Lỗi khi cập nhật người dùng.');
            $this->redirect('user/edit/' . $id);
        }
    }
    
    public function toggleStatus($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_numeric($id)) {
            $this->redirect('user/index');
            return;
        }

        $currentUser = $this->getCurrentUser();
        $userToToggle = $this->userModel->findById($id);

        // Ngăn admin tự khóa tài khoản của mình
        if ($currentUser['id'] == $id) {
            $this->setFlashMessage('error', 'Bạn không thể tự khóa tài khoản của chính mình.');
            $this->redirect('user/index');
            return;
        }

        // Ngăn chặn khóa admin cuối cùng
        if ($userToToggle && $userToToggle['quyen'] == 'admin') {
            if ($this->userModel->countActiveAdmins() <= 1) {
                 $this->setFlashMessage('error', 'Không thể khóa tài khoản admin cuối cùng của hệ thống.');
                 $this->redirect('user/index');
                 return;
            }
        }
        
        if ($this->userModel->toggleStatus($id)) {
            $this->setFlashMessage('success', 'Thay đổi trạng thái người dùng thành công.');
        } else {
            $this->setFlashMessage('error', 'Lỗi khi thay đổi trạng thái.');
        }
        $this->redirect('user/index');
    }

    private function validateUserData($data, $id = null) {
        $errors = [];
        if (empty($data['ten_dang_nhap'])) {
            $errors['ten_dang_nhap'] = 'Tên đăng nhập không được để trống.';
        } elseif ($id === null && $this->userModel->findByUsername($data['ten_dang_nhap'])) {
            // Chỉ kiểm tra tồn tại khi tạo mới, vì form edit không cho sửa tên đăng nhập
            $errors['ten_dang_nhap'] = 'Tên đăng nhập đã tồn tại.';
        }

        if ($id === null && empty($data['mat_khau'])) { // Mật khẩu bắt buộc khi tạo mới
            $errors['mat_khau'] = 'Mật khẩu không được để trống.';
        }

        if (empty($data['ho_ten'])) {
            $errors['ho_ten'] = 'Họ tên không được để trống.';
        }
        
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ.';
        }
        
        $quyenOptions = ['admin', 'canbo', 'quantri_hethong'];
        if (empty($data['quyen']) || !in_array($data['quyen'], $quyenOptions)) {
            $errors['quyen'] = 'Quyền không hợp lệ.';
        }

        return $errors;
    }
}