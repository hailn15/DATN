<?php
// app/controllers/LoaiHinhHoTroKhanCapController.php

class LoaiHinhHoTroKhanCapController extends BaseController {

    private $loaiHinhKCModel;
    // private $vanBanChinhSachModel; 

    public function __construct() {
        parent::__construct();
        $this->checkAuth(); 
        // if ($this->getCurrentUser()['role'] !== 'admin') {
        //     $this->setFlashMessage('error', 'Bạn không có quyền truy cập vào mục này.');
        //     $this->redirect('home/index');
        // }

        $this->loaiHinhKCModel = new LoaiHinhHoTroKhanCap($this->db);
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

        $result = $this->loaiHinhKCModel->getAll($filters['searchTerm'], $filters['trang_thai_ap_dung'], $limit, $offset);
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
        
        $trangThaiOptions = $this->loaiHinhKCModel->getTrangThaiApDungOptions();

        $this->view('loai_hinh_ho_tro_khan_cap/index', [
            'title' => 'QUẢN LÝ LOẠI HÌNH HỖ TRỢ KHẨN CẤP',
            'loaiHinhList' => $loaiHinhList,
            'pagination' => $pagination,
            'trangThaiOptions' => $trangThaiOptions,
            'currentFilters' => $filters
        ]);
    }

    public function create() {
        $trangThaiOptions = $this->loaiHinhKCModel->getTrangThaiApDungOptions();
        // $vanBanOptions = $this->vanBanChinhSachModel->getAllForSelect();

        $this->view('loai_hinh_ho_tro_khan_cap/create', [
            'title' => 'Thêm Loại Hình Hỗ Trợ Khẩn Cấp',
            'trangThaiOptions' => $trangThaiOptions,
            // 'vanBanOptions' => $vanBanOptions,
            'oldData' => $_SESSION['old_form_data']['lhkc_create'] ?? [],
            'errors' => $_SESSION['form_errors']['lhkc_create'] ?? []
        ]);
        unset($_SESSION['old_form_data']['lhkc_create']);
        unset($_SESSION['form_errors']['lhkc_create']);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('loai-hinh-ho-tro-khan-cap/index');
            return;
        }

        $data = $_POST;
        $errors = [];

        if (empty($data['ma_loai_hinh'])) {
            $errors['ma_loai_hinh'] = 'Mã loại hình không được để trống.';
        } elseif ($this->loaiHinhKCModel->maLoaiHinhExists($data['ma_loai_hinh'])) {
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

        // Giá trị hỗ trợ tiền mặt là không bắt buộc (có thể chỉ hỗ trợ hiện vật)
        // Nhưng nếu nhập thì phải là số không âm.
        if (isset($data['gia_tri_ho_tro_dinh_muc']) && $data['gia_tri_ho_tro_dinh_muc'] !== '') {
            if (!is_numeric($data['gia_tri_ho_tro_dinh_muc']) || (float)$data['gia_tri_ho_tro_dinh_muc'] < 0) {
                 $errors['gia_tri_ho_tro_dinh_muc'] = 'Giá trị hỗ trợ tiền mặt (nếu nhập) phải là số không âm.';
            }
        } elseif ($mucChuanValid && $heSoValid) {
            // Nếu có mức chuẩn và hệ số, sẽ tự tính, không cần validate gia_tri_ho_tro_dinh_muc ở đây nữa
        }


        if (!empty($data['van_ban_chinh_sach_id']) && !is_numeric($data['van_ban_chinh_sach_id'])) {
            $errors['van_ban_chinh_sach_id'] = 'Văn bản chính sách không hợp lệ.';
        }

        if (!empty($errors)) {
            $this->setFlashMessage('error', 'Thêm mới thất bại. Vui lòng kiểm tra lại thông tin.');
            $_SESSION['old_form_data']['lhkc_create'] = $data;
            $_SESSION['form_errors']['lhkc_create'] = $errors;
            $this->redirect('loai-hinh-ho-tro-khan-cap/create');
            return;
        }

        $result = $this->loaiHinhKCModel->create($data);

        if ($result) {
            $this->setFlashMessage('success', 'Thêm mới loại hình hỗ trợ KC thành công!');
            $this->redirect('loai-hinh-ho-tro-khan-cap/index');
        } else {
            $this->setFlashMessage('error', 'Thêm mới loại hình hỗ trợ KC thất bại.');
            $_SESSION['old_form_data']['lhkc_create'] = $data;
            $this->redirect('loai-hinh-ho-tro-khan-cap/create');
        }
    }

    public function edit($id) {
        if (!is_numeric($id) || $id <= 0) {
            $this->setFlashMessage('error', 'ID không hợp lệ.');
            $this->redirect('loai-hinh-ho-tro-khan-cap/index');
            return;
        }

        $loaiHinh = $this->loaiHinhKCModel->findById($id);
        if (!$loaiHinh) {
            $this->setFlashMessage('error', 'Không tìm thấy loại hình hỗ trợ KC.');
            $this->redirect('loai-hinh-ho-tro-khan-cap/index');
            return;
        }

        $trangThaiOptions = $this->loaiHinhKCModel->getTrangThaiApDungOptions();
        // $vanBanOptions = $this->vanBanChinhSachModel->getAllForSelect();

        $this->view('loai_hinh_ho_tro_khan_cap/edit', [
            'title' => 'Chỉnh sửa Loại Hình HTKC: ' . htmlspecialchars($loaiHinh['ten_loai_hinh']),
            'loaiHinh' => $loaiHinh,
            'trangThaiOptions' => $trangThaiOptions,
            // 'vanBanOptions' => $vanBanOptions,
            'oldData' => $_SESSION['old_form_data']['lhkc_edit_'.$id] ?? $loaiHinh,
            'errors' => $_SESSION['form_errors']['lhkc_edit_'.$id] ?? []
        ]);
        unset($_SESSION['old_form_data']['lhkc_edit_'.$id]);
        unset($_SESSION['form_errors']['lhkc_edit_'.$id]);
    }

    public function update($id) {
        if (!is_numeric($id) || $id <= 0) {
            $this->setFlashMessage('error', 'ID không hợp lệ.');
            $this->redirect('loai-hinh-ho-tro-khan-cap/index');
            return;
        }
        if (!$this->loaiHinhKCModel->findById($id)) {
            $this->setFlashMessage('error', 'Không tìm thấy loại hình để cập nhật.');
            $this->redirect('loai-hinh-ho-tro-khan-cap/index');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('loai-hinh-ho-tro-khan-cap/edit/' . $id);
            return;
        }

        $data = $_POST;
        $errors = [];

        if (empty($data['ma_loai_hinh'])) {
            $errors['ma_loai_hinh'] = 'Mã loại hình không được để trống.';
        } elseif ($this->loaiHinhKCModel->maLoaiHinhExists($data['ma_loai_hinh'], $id)) {
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

        if (isset($data['gia_tri_ho_tro_dinh_muc']) && $data['gia_tri_ho_tro_dinh_muc'] !== '') {
            if (!is_numeric($data['gia_tri_ho_tro_dinh_muc']) || (float)$data['gia_tri_ho_tro_dinh_muc'] < 0) {
                 $errors['gia_tri_ho_tro_dinh_muc'] = 'Giá trị hỗ trợ tiền mặt (nếu nhập) phải là số không âm.';
            }
        } elseif ($mucChuanValid && $heSoValid) {
            // OK
        }

        if (!empty($data['van_ban_chinh_sach_id']) && !is_numeric($data['van_ban_chinh_sach_id'])) {
            $errors['van_ban_chinh_sach_id'] = 'Văn bản chính sách không hợp lệ.';
        }

        if (!empty($errors)) {
            $this->setFlashMessage('error', 'Cập nhật thất bại. Vui lòng kiểm tra lại thông tin.');
            $_SESSION['old_form_data']['lhkc_edit_'.$id] = $data;
            $_SESSION['form_errors']['lhkc_edit_'.$id] = $errors;
            $this->redirect('loai-hinh-ho-tro-khan-cap/edit/' . $id);
            return;
        }

        $result = $this->loaiHinhKCModel->update($id, $data);

        if ($result) {
            $this->setFlashMessage('success', 'Cập nhật loại hình hỗ trợ khẩn cấp thành công!');
            $this->redirect('loai-hinh-ho-tro-khan-cap/index/' . $id);
        } else {
            $this->setFlashMessage('error', 'Cập nhật loại hình hỗ trợ khẩn cấp thất bại.');
            $_SESSION['old_form_data']['lhkc_edit_'.$id] = $data;
            $this->redirect('loai-hinh-ho-tro-khan-cap/edit/' . $id);
        }
    }

    public function destroy($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Yêu cầu không hợp lệ.');
            $this->redirect('loai-hinh-ho-tro-khan-cap/index');
            return;
        }
         if (!is_numeric($id) || $id <=0) {
            $this->setFlashMessage('error', 'ID không hợp lệ.');
            $this->redirect('loai-hinh-ho-tro-khan-cap/index');
            return;
        }

        $result = $this->loaiHinhKCModel->delete($id);

        if ($result) {
            $this->setFlashMessage('success', 'Xóa loại hình hỗ trợ KC thành công!');
        } else {
            $this->setFlashMessage('error', 'Xóa thất bại. Loại hình này có thể đang được sử dụng.');
        }
        $this->redirect('loai-hinh-ho-tro-khan-cap/index');
    }
}
?>