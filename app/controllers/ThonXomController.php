<?php
// app/controllers/ThonXomController.php

class ThonXomController extends BaseController {

    private $thonXomModel;

    public function __construct() {
        parent::__construct();
        $this->checkAuth(); // Yêu cầu người dùng phải đăng nhập

        $this->thonXomModel = new ThonXom($this->db);
    }

    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $filters = [
            'searchTerm' => isset($_GET['search']) ? trim($_GET['search']) : '',
        ];

        $result = $this->thonXomModel->getAll($filters['searchTerm'], $limit, $offset);
        $thonXomList = $result['data'];
        $totalRecords = $result['total'];
        $totalPages = ceil($totalRecords / $limit);

        $pagination = [
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'limit' => $limit,
            'totalRecords' => $totalRecords,
            'filters' => $filters
        ];
        
        $this->view('thon_xom/index', [
            'title' => 'QUẢN LÝ ĐỊA PHƯƠNG',
            'thonXomList' => $thonXomList,
            'pagination' => $pagination,
            'currentController' => 'ThonXomController', // Giữ nguyên để sidebar hoạt động
            'currentFilters' => $filters
        ]);
    }

    public function create() {
        $this->view('thon_xom/create', [
            'title' => 'Thêm mới Thôn',
            'currentController' => 'ThonXomController', // Giữ nguyên
            'oldData' => $_SESSION['old_form_data']['thon_xom_create'] ?? [],
            'errors' => $_SESSION['form_errors']['thon_xom_create'] ?? []
        ]);
        unset($_SESSION['old_form_data']['thon_xom_create']);
        unset($_SESSION['form_errors']['thon_xom_create']);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('thon-xom/index');
            return;
        }

        $data = $_POST;
        $errors = [];

        if (empty($data['ten_thon'])) {
            $errors['ten_thon'] = 'Tên thôn không được để trống.';
        } elseif ($this->thonXomModel->tenThonExists($data['ten_thon'])) {
            $errors['ten_thon'] = 'Tên thôn đã tồn tại.';
        }
        if (!empty($data['ma_thon']) && $this->thonXomModel->maThonExists($data['ma_thon'])) {
            $errors['ma_thon'] = 'Mã thôn đã tồn tại.';
        }

        if (!empty($errors)) {
            // Sử dụng setFlashMessage từ BaseController cho lỗi chung nếu muốn,
            // hoặc chỉ dựa vào $errors cho các lỗi cụ thể của trường.
            // Ở đây, chúng ta đang dùng session errors cho các trường, nên không cần flash message chung cho validation.
            // $this->setFlashMessage('error', 'Thêm mới thất bại. Vui lòng kiểm tra lại thông tin.');
            $_SESSION['old_form_data']['thon_xom_create'] = $data;
            $_SESSION['form_errors']['thon_xom_create'] = $errors;
            $this->redirect('thon-xom/create');
            return;
        }

        $result = $this->thonXomModel->create($data);

        if ($result) {
            $this->setFlashMessage('success', 'Thêm mới thôn/xóm thành công!');
            $this->redirect('thon-xom/index');
        } else {
            $this->setFlashMessage('error', 'Thêm mới thôn/xóm thất bại.');
            $_SESSION['old_form_data']['thon_xom_create'] = $data; 
            $this->redirect('thon-xom/create');
        }
    }

    public function edit($id) {
        if (!is_numeric($id) || $id <= 0) {
            $this->setFlashMessage('error', 'ID thôn/xóm không hợp lệ.');
            $this->redirect('thon-xom/index');
            return;
        }

        $thonXom = $this->thonXomModel->findById($id);
        if (!$thonXom) {
             $this->setFlashMessage('error', 'Không tìm thấy thôn/xóm.');
             $this->redirect('thon-xom/index');
             return;
        }

        $this->view('thon_xom/edit', [
            'title' => 'Chỉnh sửa thông tin thôn: ' . htmlspecialchars($thonXom['ten_thon']),
            'thonXom' => $thonXom,
            'currentController' => 'ThonXomController', // Giữ nguyên
            'oldData' => $_SESSION['old_form_data']['thon_xom_edit_'.$id] ?? $thonXom,
            'errors' => $_SESSION['form_errors']['thon_xom_edit_'.$id] ?? []
        ]);
        unset($_SESSION['old_form_data']['thon_xom_edit_'.$id]);
        unset($_SESSION['form_errors']['thon_xom_edit_'.$id]);
    }

    public function update($id) {
        if (!is_numeric($id) || $id <= 0) {
           $this->setFlashMessage('error', 'ID thôn/xóm không hợp lệ.');
           $this->redirect('thon-xom/index');
           return;
       }
       $existingThonXom = $this->thonXomModel->findById($id);
       if (!$existingThonXom) {
            $this->setFlashMessage('error', 'Không tìm thấy thôn/xóm để cập nhật.');
            $this->redirect('thon-xom/index');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('thon-xom/edit/' . $id);
            return;
        }

        $data = $_POST;
        $errors = [];

        if (empty($data['ten_thon'])) {
             $errors['ten_thon'] = 'Tên thôn không được để trống.';
        } elseif ($this->thonXomModel->tenThonExists($data['ten_thon'], $id)) {
              $errors['ten_thon'] = 'Tên thôn đã tồn tại.';
        }
        if (!empty($data['ma_thon']) && $this->thonXomModel->maThonExists($data['ma_thon'], $id)) {
              $errors['ma_thon'] = 'Mã thôn đã tồn tại.';
        }

        if (!empty($errors)) {
            // $this->setFlashMessage('error', 'Cập nhật thất bại. Vui lòng kiểm tra lại thông tin.');
            $_SESSION['old_form_data']['thon_xom_edit_'.$id] = $data;
            $_SESSION['form_errors']['thon_xom_edit_'.$id] = $errors;
            $this->redirect('thon-xom/edit/' . $id);
            return;
        }

        $result = $this->thonXomModel->update($id, $data);

        if ($result) {
            $this->setFlashMessage('success', 'Cập nhật thông tin thôn/xóm thành công!');
            $this->redirect('thon-xom/index/' . $id); 
        } else {
            $this->setFlashMessage('error', 'Cập nhật thông tin thôn/xóm thất bại.');
            $_SESSION['old_form_data']['thon_xom_edit_'.$id] = $data; 
            $this->redirect('thon-xom/edit/' . $id);
        }
    }

    public function destroy($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
             $this->setFlashMessage('error', 'Yêu cầu không hợp lệ.');
             $this->redirect('thon-xom/index');
             return;
        }
        if (!is_numeric($id) || $id <=0) {
           $this->setFlashMessage('error', 'ID thôn/xóm không hợp lệ.');
           $this->redirect('thon-xom/index');
           return;
       }
        
        $thonXom = $this->thonXomModel->findById($id); 
        if (!$thonXom) {
            $this->setFlashMessage('error', 'Không tìm thấy thôn/xóm để xóa.');
            $this->redirect('thon-xom/index');
            return;
        }

        $result = $this->thonXomModel->delete($id);

        if ($result) {
            $this->setFlashMessage('success', "Xóa thôn/xóm \"".htmlspecialchars($thonXom['ten_thon'])."\" thành công!");
        } else {
            $this->setFlashMessage('error', "Xóa thôn/xóm \"".htmlspecialchars($thonXom['ten_thon'])."\" thất bại. Có thể thôn/xóm đang được sử dụng.");
        }
        $this->redirect('thon-xom/index');
    }
}
?>