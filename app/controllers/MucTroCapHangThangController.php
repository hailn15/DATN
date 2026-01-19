<?php
// app/controllers/MucTroCapHangThangController.php

class MucTroCapHangThangController extends BaseController {

    private $mucTroCapModel;
    // private $vanBanChinhSachModel; // Giữ lại nếu bạn cần

    public function __construct() {
        parent::__construct();
        $this->checkAuth(); 
        // if ($this->getCurrentUser()['role'] !== 'admin') {
        //     $this->setFlashMessage('error', 'Bạn không có quyền truy cập vào mục này.');
        //     $this->redirect('home/index');
        // }

        $this->mucTroCapModel = new MucTroCapHangThang($this->db);
        // require_once '../app/models/VanBanChinhSach.php';
        // $this->vanBanChinhSachModel = new VanBanChinhSach($this->db);
    }

    public function index() {
        // ... (Giữ nguyên phần index)
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $filters = [
            'searchTerm' => isset($_GET['search']) ? trim($_GET['search']) : '',
            'trang_thai_ap_dung' => isset($_GET['trang_thai_ap_dung']) ? trim($_GET['trang_thai_ap_dung']) : ''
        ];

        $result = $this->mucTroCapModel->getAll($filters['searchTerm'], $filters['trang_thai_ap_dung'], $limit, $offset);
        $mucTroCapList = $result['data'];
        $totalRecords = $result['total'];
        $totalPages = ceil($totalRecords / $limit);

        $pagination = [
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'limit' => $limit,
            'totalRecords' => $totalRecords,
            'filters' => $filters
        ];
        
        $trangThaiOptions = $this->mucTroCapModel->getTrangThaiApDungOptions();

        $this->view('muc_tro_cap_hang_thang/index', [
            'title' => 'QUẢN LÝ MỨC TRỢ CẤP HÀNG THÁNG',
            'mucTroCapList' => $mucTroCapList,
            'pagination' => $pagination,
            'trangThaiOptions' => $trangThaiOptions,
            'currentFilters' => $filters
        ]);
    }

    public function create() {
        // ... (Giữ nguyên)
        $trangThaiOptions = $this->mucTroCapModel->getTrangThaiApDungOptions();
        // $vanBanOptions = $this->vanBanChinhSachModel->getAllForSelect(); 

        $this->view('muc_tro_cap_hang_thang/create', [
            'title' => 'Thêm Mức Trợ Cấp Hàng Tháng',
            'trangThaiOptions' => $trangThaiOptions,
            // 'vanBanOptions' => $vanBanOptions,
            'oldData' => $_SESSION['old_form_data']['muc_tro_cap_create'] ?? [],
            'errors' => $_SESSION['form_errors']['muc_tro_cap_create'] ?? []
        ]);
        unset($_SESSION['old_form_data']['muc_tro_cap_create']);
        unset($_SESSION['form_errors']['muc_tro_cap_create']);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('muc-tro-cap-hang-thang/index');
            return;
        }

        $data = $_POST;
        $errors = [];

        if (empty($data['ma_muc'])) {
            $errors['ma_muc'] = 'Mã mức không được để trống.';
        } elseif ($this->mucTroCapModel->maMucExists($data['ma_muc'])) {
            $errors['ma_muc'] = 'Mã mức đã tồn tại.';
        }
        if (empty($data['ten_muc'])) {
            $errors['ten_muc'] = 'Tên mức không được để trống.';
        }

        // Validation cho muc_chuan và he_so
        if (isset($data['muc_chuan']) && $data['muc_chuan'] !== '' && (!is_numeric($data['muc_chuan']) || (float)$data['muc_chuan'] < 0)) {
            $errors['muc_chuan'] = 'Mức chuẩn (nếu nhập) phải là số không âm.';
        }
        if (isset($data['he_so']) && $data['he_so'] !== '' && (!is_numeric($data['he_so']) || (float)$data['he_so'] < 0)) {
            $errors['he_so'] = 'Hệ số (nếu nhập) phải là số không âm.';
        }
        
        // Validation cho so_tien_ap_dung: Bắt buộc nếu không thể tính từ muc_chuan và he_so
        $mucChuanValid = isset($data['muc_chuan']) && is_numeric($data['muc_chuan']) && (float)$data['muc_chuan'] > 0;
        $heSoValid = isset($data['he_so']) && is_numeric($data['he_so']) && (float)$data['he_so'] > 0;

        if (!($mucChuanValid && $heSoValid)) { // Nếu không thể tính
            if (empty($data['so_tien_ap_dung']) || !is_numeric($data['so_tien_ap_dung']) || (float)$data['so_tien_ap_dung'] < 0) {
                $errors['so_tien_ap_dung'] = 'Số tiền áp dụng phải là số không âm (hoặc Mức chuẩn và Hệ số phải hợp lệ để tự tính).';
            }
        } elseif (isset($data['so_tien_ap_dung']) && $data['so_tien_ap_dung'] !== '' && (!is_numeric($data['so_tien_ap_dung']) || (float)$data['so_tien_ap_dung'] < 0)) {
            // Trường hợp người dùng nhập tay so_tien_ap_dung dù muc_chuan, he_so có thể valid
             $errors['so_tien_ap_dung'] = 'Số tiền áp dụng (nếu nhập tay) phải là số không âm.';
        }


        if (!empty($data['van_ban_chinh_sach_id']) && !is_numeric($data['van_ban_chinh_sach_id'])) {
            $errors['van_ban_chinh_sach_id'] = 'Văn bản chính sách không hợp lệ.';
        }

        if (!empty($errors)) {
            $this->setFlashMessage('error', 'Thêm mới thất bại. Vui lòng kiểm tra lại thông tin.');
            $_SESSION['old_form_data']['muc_tro_cap_create'] = $data;
            $_SESSION['form_errors']['muc_tro_cap_create'] = $errors;
            $this->redirect('muc-tro-cap-hang-thang/create');
            return;
        }

        $result = $this->mucTroCapModel->create($data);

        if ($result) {
            $this->setFlashMessage('success', 'Thêm mới mức trợ cấp thành công!');
            $this->redirect('muc-tro-cap-hang-thang/index');
        } else {
            $this->setFlashMessage('error', 'Thêm mới mức trợ cấp thất bại.');
            $_SESSION['old_form_data']['muc_tro_cap_create'] = $data;
            $this->redirect('muc-tro-cap-hang-thang/create');
        }
    }

    public function edit($id) {
        // ... (Giữ nguyên)
        if (!is_numeric($id) || $id <= 0) {
            $this->setFlashMessage('error', 'ID mức trợ cấp không hợp lệ.');
            $this->redirect('muc-tro-cap-hang-thang/index');
            return;
        }

        $mucTroCap = $this->mucTroCapModel->findById($id);
        if (!$mucTroCap) {
            $this->setFlashMessage('error', 'Không tìm thấy mức trợ cấp.');
            $this->redirect('muc-tro-cap-hang-thang/index');
            return;
        }

        $trangThaiOptions = $this->mucTroCapModel->getTrangThaiApDungOptions();
        // $vanBanOptions = $this->vanBanChinhSachModel->getAllForSelect();

        $this->view('muc_tro_cap_hang_thang/edit', [
            'title' => 'Chỉnh sửa Mức Trợ Cấp: ' . htmlspecialchars($mucTroCap['ten_muc']),
            'mucTroCap' => $mucTroCap,
            'trangThaiOptions' => $trangThaiOptions,
            // 'vanBanOptions' => $vanBanOptions,
            'oldData' => $_SESSION['old_form_data']['muc_tro_cap_edit_'.$id] ?? $mucTroCap,
            'errors' => $_SESSION['form_errors']['muc_tro_cap_edit_'.$id] ?? []
        ]);
        unset($_SESSION['old_form_data']['muc_tro_cap_edit_'.$id]);
        unset($_SESSION['form_errors']['muc_tro_cap_edit_'.$id]);
    }

    public function update($id) {
        if (!is_numeric($id) || $id <= 0) {
            $this->setFlashMessage('error', 'ID mức trợ cấp không hợp lệ.');
            $this->redirect('muc-tro-cap-hang-thang/index');
            return;
        }
        if (!$this->mucTroCapModel->findById($id)) {
            $this->setFlashMessage('error', 'Không tìm thấy mức trợ cấp để cập nhật.');
            $this->redirect('muc-tro-cap-hang-thang/index');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('muc-tro-cap-hang-thang/edit/' . $id);
            return;
        }

        $data = $_POST;
        $errors = [];

        if (empty($data['ma_muc'])) {
            $errors['ma_muc'] = 'Mã mức không được để trống.';
        } elseif ($this->mucTroCapModel->maMucExists($data['ma_muc'], $id)) {
            $errors['ma_muc'] = 'Mã mức đã tồn tại.';
        }
        if (empty($data['ten_muc'])) {
            $errors['ten_muc'] = 'Tên mức không được để trống.';
        }
        
        // Validation cho muc_chuan và he_so
        if (isset($data['muc_chuan']) && $data['muc_chuan'] !== '' && (!is_numeric($data['muc_chuan']) || (float)$data['muc_chuan'] < 0)) {
            $errors['muc_chuan'] = 'Mức chuẩn (nếu nhập) phải là số không âm.';
        }
        if (isset($data['he_so']) && $data['he_so'] !== '' && (!is_numeric($data['he_so']) || (float)$data['he_so'] < 0)) {
            $errors['he_so'] = 'Hệ số (nếu nhập) phải là số không âm.';
        }

        // Validation cho so_tien_ap_dung
        $mucChuanValid = isset($data['muc_chuan']) && is_numeric($data['muc_chuan']) && (float)$data['muc_chuan'] > 0;
        $heSoValid = isset($data['he_so']) && is_numeric($data['he_so']) && (float)$data['he_so'] > 0;

        if (!($mucChuanValid && $heSoValid)) {
            if (empty($data['so_tien_ap_dung']) || !is_numeric($data['so_tien_ap_dung']) || (float)$data['so_tien_ap_dung'] < 0) {
                $errors['so_tien_ap_dung'] = 'Số tiền áp dụng phải là số không âm (hoặc Mức chuẩn và Hệ số phải hợp lệ để tự tính).';
            }
        } elseif (isset($data['so_tien_ap_dung']) && $data['so_tien_ap_dung'] !== '' && (!is_numeric($data['so_tien_ap_dung']) || (float)$data['so_tien_ap_dung'] < 0)) {
             $errors['so_tien_ap_dung'] = 'Số tiền áp dụng (nếu nhập tay) phải là số không âm.';
        }

        if (!empty($data['van_ban_chinh_sach_id']) && !is_numeric($data['van_ban_chinh_sach_id'])) {
            $errors['van_ban_chinh_sach_id'] = 'Văn bản chính sách không hợp lệ.';
        }


        if (!empty($errors)) {
            $this->setFlashMessage('error', 'Cập nhật thất bại. Vui lòng kiểm tra lại thông tin.');
            $_SESSION['old_form_data']['muc_tro_cap_edit_'.$id] = $data;
            $_SESSION['form_errors']['muc_tro_cap_edit_'.$id] = $errors;
            $this->redirect('muc-tro-cap-hang-thang/edit/' . $id);
            return;
        }

        $result = $this->mucTroCapModel->update($id, $data);

        if ($result) {
            $this->setFlashMessage('success', 'Cập nhật mức trợ cấp thành công!');
            $this->redirect('muc-tro-cap-hang-thang/index/' . $id);
        } else {
            $this->setFlashMessage('error', 'Cập nhật mức trợ cấp thất bại. Xem logs để biết chi tiết.');
            $_SESSION['old_form_data']['muc_tro_cap_edit_'.$id] = $data;
            $this->redirect('muc-tro-cap-hang-thang/edit/' . $id);
        }
    }

    public function destroy($id) {
        // ... (Giữ nguyên)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Yêu cầu không hợp lệ.');
            $this->redirect('muc-tro-cap-hang-thang/index');
            return;
        }
         if (!is_numeric($id) || $id <=0) {
            $this->setFlashMessage('error', 'ID mức trợ cấp không hợp lệ.');
            $this->redirect('muc-tro-cap-hang-thang/index');
            return;
        }

        $result = $this->mucTroCapModel->delete($id);

        if ($result) {
            $this->setFlashMessage('success', 'Xóa mức trợ cấp thành công!');
        } else {
            $this->setFlashMessage('error', 'Xóa mức trợ cấp thất bại. Mức này có thể đang được sử dụng hoặc có lỗi xảy ra.');
        }
        $this->redirect('muc-tro-cap-hang-thang/index');
    }
}
?>