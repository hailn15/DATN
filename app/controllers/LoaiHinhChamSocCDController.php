<?php
// app/controllers/LoaiHinhChamSocCDController.php

class LoaiHinhChamSocCDController extends BaseController {

    private $loaiHinhCSModel;
    // private $vanBanChinhSachModel; 

    public function __construct() {
        parent::__construct();
        $this->checkAuth(); 
        // if ($this->getCurrentUser()['role'] !== 'admin') {
        //     $this->setFlashMessage('error', 'Bạn không có quyền truy cập vào mục này.');
        //     $this->redirect('home/index');
        // }

        $this->loaiHinhCSModel = new LoaiHinhChamSocCD($this->db);
        // $this->vanBanChinhSachModel = new VanBanChinhSach($this->db); 
    }

    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $filters = [
            'searchTerm' => isset($_GET['search']) ? trim($_GET['search']) : '',
            'trang_thai_ap_dung' => isset($_GET['trang_thai_ap_dung']) ? trim($_GET['trang_thai_ap_dung']) : ''
        ];

        $result = $this->loaiHinhCSModel->getAll($filters['searchTerm'], $filters['trang_thai_ap_dung'], $limit, $offset);
        $loaiHinhList = $result['data'];
        $totalRecords = $result['total'];
        $totalPages = ceil($totalRecords / $limit);

        $pagination = [
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'limit' => $limit,
            'totalRecords' => $totalRecords,
            'filters' => $filters
        ];
        
        $trangThaiOptions = $this->loaiHinhCSModel->getTrangThaiApDungOptions();

        $this->view('loai_hinh_cham_soc_cd/index', [
            'title' => 'QUẢN LÝ LOẠI HÌNH CHĂM SÓC TẠI CỘNG ĐỒNG',
            'loaiHinhList' => $loaiHinhList,
            'pagination' => $pagination,
            'trangThaiOptions' => $trangThaiOptions,
            'currentFilters' => $filters
        ]);
    }

    public function create() {
        $trangThaiOptions = $this->loaiHinhCSModel->getTrangThaiApDungOptions();
        // $vanBanOptions = $this->vanBanChinhSachModel->getAllForSelect();

        $this->view('loai_hinh_cham_soc_cd/create', [
            'title' => 'Thêm Loại Hình Chăm Sóc Cộng Đồng',
            'trangThaiOptions' => $trangThaiOptions,
            // 'vanBanOptions' => $vanBanOptions,
            'oldData' => $_SESSION['old_form_data']['lhcs_create'] ?? [],
            'errors' => $_SESSION['form_errors']['lhcs_create'] ?? []
        ]);
        unset($_SESSION['old_form_data']['lhcs_create']);
        unset($_SESSION['form_errors']['lhcs_create']);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('loai-hinh-cham-soc-cd/index');
            return;
        }

        $data = $_POST;
        $errors = [];

        if (empty($data['ma_loai_hinh'])) {
            $errors['ma_loai_hinh'] = 'Mã loại hình không được để trống.';
        } elseif ($this->loaiHinhCSModel->maLoaiHinhExists($data['ma_loai_hinh'])) {
            $errors['ma_loai_hinh'] = 'Mã loại hình đã tồn tại.';
        }
        if (empty($data['ten_loai_hinh'])) {
            $errors['ten_loai_hinh'] = 'Tên loại hình không được để trống.';
        }

        if (isset($data['muc_chuan']) && $data['muc_chuan'] !== '' && (!is_numeric($data['muc_chuan']) || (float)$data['muc_chuan'] < 0)) {
            $errors['muc_chuan'] = 'Mức chuẩn (nếu nhập) phải là số không âm.';
        }
        if (isset($data['he_so']) && $data['he_so'] !== '' && (!is_numeric($data['he_so']) || (float)$data['he_so'] < 0)) {
            $errors['he_so'] = 'Hệ số (nếu nhập) phải là số không âm.';
        }
        
        $mucChuanValid = isset($data['muc_chuan']) && is_numeric($data['muc_chuan']) && (float)$data['muc_chuan'] > 0;
        $heSoValid = isset($data['he_so']) && is_numeric($data['he_so']) && (float)$data['he_so'] > 0;

        // Kinh phí định mức không bắt buộc, nhưng nếu nhập phải là số không âm
        if (isset($data['kinh_phi_dinh_muc_du_kien']) && $data['kinh_phi_dinh_muc_du_kien'] !== '') {
             if (!is_numeric($data['kinh_phi_dinh_muc_du_kien']) || (float)$data['kinh_phi_dinh_muc_du_kien'] < 0) {
                $errors['kinh_phi_dinh_muc_du_kien'] = 'Kinh phí định mức (nếu nhập) phải là số không âm.';
            }
        } elseif ($mucChuanValid && $heSoValid) {
            // OK, sẽ tự tính
        }

        if (!empty($data['van_ban_chinh_sach_id']) && !is_numeric($data['van_ban_chinh_sach_id'])) {
            $errors['van_ban_chinh_sach_id'] = 'Văn bản chính sách không hợp lệ.';
        }

        if (!empty($errors)) {
            $this->setFlashMessage('error', 'Thêm mới thất bại. Vui lòng kiểm tra lại thông tin.');
            $_SESSION['old_form_data']['lhcs_create'] = $data;
            $_SESSION['form_errors']['lhcs_create'] = $errors;
            $this->redirect('loai-hinh-cham-soc-cd/create');
            return;
        }

        $result = $this->loaiHinhCSModel->create($data);

        if ($result) {
            $this->setFlashMessage('success', 'Thêm mới loại hình chăm sóc CĐ thành công!');
            $this->redirect('loai-hinh-cham-soc-cd/index');
        } else {
            $this->setFlashMessage('error', 'Thêm mới loại hình chăm sóc CĐ thất bại.');
            $_SESSION['old_form_data']['lhcs_create'] = $data;
            $this->redirect('loai-hinh-cham-soc-cd/create');
        }
    }

    public function edit($id) {
        if (!is_numeric($id) || $id <= 0) {
            $this->setFlashMessage('error', 'ID không hợp lệ.');
            $this->redirect('loai-hinh-cham-soc-cd/index');
            return;
        }

        $loaiHinh = $this->loaiHinhCSModel->findById($id);
        if (!$loaiHinh) {
            $this->setFlashMessage('error', 'Không tìm thấy loại hình chăm sóc CĐ.');
            $this->redirect('loai-hinh-cham-soc-cd/index');
            return;
        }

        $trangThaiOptions = $this->loaiHinhCSModel->getTrangThaiApDungOptions();
        // $vanBanOptions = $this->vanBanChinhSachModel->getAllForSelect();

        $this->view('loai_hinh_cham_soc_cd/edit', [
            'title' => 'Chỉnh sửa Loại Hình CS CĐ: ' . htmlspecialchars($loaiHinh['ten_loai_hinh']),
            'loaiHinh' => $loaiHinh,
            'trangThaiOptions' => $trangThaiOptions,
            // 'vanBanOptions' => $vanBanOptions,
            'oldData' => $_SESSION['old_form_data']['lhcs_edit_'.$id] ?? $loaiHinh,
            'errors' => $_SESSION['form_errors']['lhcs_edit_'.$id] ?? []
        ]);
        unset($_SESSION['old_form_data']['lhcs_edit_'.$id]);
        unset($_SESSION['form_errors']['lhcs_edit_'.$id]);
    }

    public function update($id) {
        if (!is_numeric($id) || $id <= 0) {
            $this->setFlashMessage('error', 'ID không hợp lệ.');
            $this->redirect('loai-hinh-cham-soc-cd/index');
            return;
        }
        if (!$this->loaiHinhCSModel->findById($id)) {
            $this->setFlashMessage('error', 'Không tìm thấy loại hình để cập nhật.');
            $this->redirect('loai-hinh-cham-soc-cd/index');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('loai-hinh-cham-soc-cd/edit/' . $id);
            return;
        }

        $data = $_POST;
        $errors = [];

        if (empty($data['ma_loai_hinh'])) {
            $errors['ma_loai_hinh'] = 'Mã loại hình không được để trống.';
        } elseif ($this->loaiHinhCSModel->maLoaiHinhExists($data['ma_loai_hinh'], $id)) {
            $errors['ma_loai_hinh'] = 'Mã loại hình đã tồn tại.';
        }
        if (empty($data['ten_loai_hinh'])) {
            $errors['ten_loai_hinh'] = 'Tên loại hình không được để trống.';
        }
        
        if (isset($data['muc_chuan']) && $data['muc_chuan'] !== '' && (!is_numeric($data['muc_chuan']) || (float)$data['muc_chuan'] < 0)) {
            $errors['muc_chuan'] = 'Mức chuẩn (nếu nhập) phải là số không âm.';
        }
        if (isset($data['he_so']) && $data['he_so'] !== '' && (!is_numeric($data['he_so']) || (float)$data['he_so'] < 0)) {
            $errors['he_so'] = 'Hệ số (nếu nhập) phải là số không âm.';
        }

        $mucChuanValid = isset($data['muc_chuan']) && is_numeric($data['muc_chuan']) && (float)$data['muc_chuan'] > 0;
        $heSoValid = isset($data['he_so']) && is_numeric($data['he_so']) && (float)$data['he_so'] > 0;
        
        if (isset($data['kinh_phi_dinh_muc_du_kien']) && $data['kinh_phi_dinh_muc_du_kien'] !== '') {
            if(!is_numeric($data['kinh_phi_dinh_muc_du_kien']) || (float)$data['kinh_phi_dinh_muc_du_kien'] < 0) {
                $errors['kinh_phi_dinh_muc_du_kien'] = 'Kinh phí định mức (nếu nhập) phải là số không âm.';
            }
        } elseif ($mucChuanValid && $heSoValid) {
           // OK
        }

        if (!empty($data['van_ban_chinh_sach_id']) && !is_numeric($data['van_ban_chinh_sach_id'])) {
            $errors['van_ban_chinh_sach_id'] = 'Văn bản chính sách không hợp lệ.';
        }


        if (!empty($errors)) {
            $this->setFlashMessage('error', 'Cập nhật thất bại. Vui lòng kiểm tra lại thông tin.');
            $_SESSION['old_form_data']['lhcs_edit_'.$id] = $data;
            $_SESSION['form_errors']['lhcs_edit_'.$id] = $errors;
            $this->redirect('loai-hinh-cham-soc-cd/edit/' . $id);
            return;
        }

        $result = $this->loaiHinhCSModel->update($id, $data);

        if ($result) {
            $this->setFlashMessage('success', 'Cập nhật loại hình chăm sóc CĐ thành công!');
            $this->redirect('loai-hinh-cham-soc-cd/edit/' . $id);
        } else {
            $this->setFlashMessage('error', 'Cập nhật loại hình chăm sóc CĐ thất bại.');
            $_SESSION['old_form_data']['lhcs_edit_'.$id] = $data;
            $this->redirect('loai-hinh-cham-soc-cd/edit/' . $id);
        }
    }

    public function destroy($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Yêu cầu không hợp lệ.');
            $this->redirect('loai-hinh-cham-soc-cd/index');
            return;
        }
         if (!is_numeric($id) || $id <=0) {
            $this->setFlashMessage('error', 'ID không hợp lệ.');
            $this->redirect('loai-hinh-cham-soc-cd/index');
            return;
        }

        $result = $this->loaiHinhCSModel->delete($id);

        if ($result) {
            $this->setFlashMessage('success', 'Xóa loại hình chăm sóc CĐ thành công!');
        } else {
            $this->setFlashMessage('error', 'Xóa thất bại. Loại hình này có thể đang được sử dụng.');
        }
        $this->redirect('loai-hinh-cham-soc-cd/index');
    }
}
?>