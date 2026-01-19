<?php
// app/controllers/NguoiChamSocController.php

class NguoiChamSocController extends BaseController {
    private $nguoiChamSocModel;

    public function __construct() {
        parent::__construct();
        $this->checkAuth();
        $this->nguoiChamSocModel = new NguoiChamSoc($this->db);
    }

    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 15;
        $offset = ($page - 1) * $limit;
        $filters = ['searchTerm' => isset($_GET['search']) ? trim($_GET['search']) : ''];

        $result = $this->nguoiChamSocModel->getAll($filters, $limit, $offset);
        
        $this->view('nguoi_cham_soc/index', [
            'title' => 'Danh sách Người Chăm Sóc',
            'nguoiChamSocList' => $result['data'],
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => ceil($result['total'] / $limit),
                'totalRecords' => $result['total'],
                'limit' => $limit,
                'filters' => $filters,
                'action' => 'index'
            ],
            'currentFilters' => $filters
        ]);
    }

    public function create() {
        $this->view('nguoi_cham_soc/create', [
            'title' => 'Thêm mới Người Chăm Sóc',
            'ncs' => [],
            'errors' => $_SESSION['form_errors']['ncs_create'] ?? [],
            'oldData' => $_SESSION['old_form_data']['ncs_create'] ?? []
        ]);
        unset($_SESSION['form_errors']['ncs_create'], $_SESSION['old_form_data']['ncs_create']);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('nguoi-cham-soc/index');
            return;
        }

        $data = $_POST;
        $errors = $this->validateData($data);

        if (!empty($errors)) {
            $_SESSION['form_errors']['ncs_create'] = $errors;
            $_SESSION['old_form_data']['ncs_create'] = $data;
            $this->setFlashMessage('error', 'Thêm mới thất bại. Vui lòng kiểm tra lại lỗi.');
            $this->redirect('nguoi-cham-soc/create');
            return;
        }

        if ($this->nguoiChamSocModel->create($data)) {
            $this->setFlashMessage('success', 'Thêm mới người chăm sóc thành công.');
            // ĐÃ SỬA: Chuyển về trang danh sách sau khi thêm mới thành công
            $this->redirect('nguoi-cham-soc/index');
        } else {
            $_SESSION['old_form_data']['ncs_create'] = $data;
            $this->setFlashMessage('error', 'Có lỗi xảy ra khi lưu vào CSDL.');
            $this->redirect('nguoi-cham-soc/create');
        }
    }

    public function edit($id) {
        $ncs = $this->nguoiChamSocModel->findById($id);
        if (!$ncs) {
            $this->setFlashMessage('error', 'Không tìm thấy người chăm sóc.');
            $this->redirect('nguoi-cham-soc/index');
            return;
        }

        $this->view('nguoi_cham_soc/edit', [
            'title' => 'Chỉnh sửa Người Chăm Sóc',
            'ncs' => $_SESSION['old_form_data']['ncs_edit_'.$id] ?? $ncs,
            'errors' => $_SESSION['form_errors']['ncs_edit_'.$id] ?? [],
            'id' => $id
        ]);
        unset($_SESSION['form_errors']['ncs_edit_'.$id], $_SESSION['old_form_data']['ncs_edit_'.$id]);
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('nguoi-cham-soc/index');
            return;
        }

        $data = $_POST;
        $errors = $this->validateData($data, $id);

        if (!empty($errors)) {
            $_SESSION['form_errors']['ncs_edit_'.$id] = $errors;
            $_SESSION['old_form_data']['ncs_edit_'.$id] = $data;
            $this->setFlashMessage('error', 'Cập nhật thất bại. Vui lòng kiểm tra lại lỗi.');
            $this->redirect('nguoi-cham-soc/edit/' . $id);
            return;
        }

        if ($this->nguoiChamSocModel->update($id, $data)) {
            $this->setFlashMessage('success', 'Cập nhật thông tin người chăm sóc thành công.');
            // ĐÃ SỬA: Chuyển về trang danh sách sau khi cập nhật thành công
            $this->redirect('nguoi-cham-soc/index');
        } else {
            $_SESSION['old_form_data']['ncs_edit_'.$id] = $data;
            $this->setFlashMessage('error', 'Có lỗi xảy ra khi cập nhật CSDL.');
            $this->redirect('nguoi-cham-soc/edit/' . $id);
        }
    }

    public function destroy($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
             $this->redirect('nguoi-cham-soc/index');
             return;
        }
        
        if (!is_numeric($id) || $id <= 0) {
            $this->setFlashMessage('error', 'ID người chăm sóc không hợp lệ.');
            $this->redirect('nguoi-cham-soc/index');
            return;
        }

        if ($this->nguoiChamSocModel->delete($id)) {
            $this->setFlashMessage('success', 'Xóa người chăm sóc thành công.');
        } else {
            $this->setFlashMessage('error', 'Xóa người chăm sóc thất bại. Có thể do ràng buộc dữ liệu.');
        }
        // ĐÚNG: Đã chuyển về trang danh sách
        $this->redirect('nguoi-cham-soc/index');
    }

    private function validateData($data, $excludeId = null) {
        $errors = [];
        if (empty(trim($data['ho_ten']))) {
            $errors['ho_ten'] = 'Họ tên không được để trống.';
        }
        if (empty(trim($data['cccd']))) {
            $errors['cccd'] = 'Số định danh không được để trống.';
        }
        if (!empty($data['ma_nguoi_cham_soc']) && $this->nguoiChamSocModel->maNCSExists($data['ma_nguoi_cham_soc'], $excludeId)) {
            $errors['ma_nguoi_cham_soc'] = 'Mã người chăm sóc đã tồn tại.';
        }
        if (!empty($data['cccd'])) {
            if (!preg_match('/^[0-9]{12}$/', $data['cccd'])) {
                 $errors['cccd'] = 'Số CCCD không hợp lệ (yêu cầu 12 chữ số).';
            } elseif ($this->nguoiChamSocModel->cccdExists($data['cccd'], $excludeId)) {
                $errors['cccd'] = 'Số CCCD đã tồn tại.';
            }
        }
        return $errors;
    }
}