<?php
// app/controllers/DoiTuongController.php

class DoiTuongController extends BaseController {

    private $doiTuongModel;
    private $thonXomModel; 
    private $loaiDoiTuongModel;

    public function __construct() {
        parent::__construct();
        $this->checkAuth(); // Yêu cầu đăng nhập
        
        $this->doiTuongModel = new DoiTuong($this->db);
        $this->thonXomModel = new ThonXom($this->db); 
    }

    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        $limit = 15; 
        $offset = ($page - 1) * $limit;

        // <<< THAY ĐỔI: Tạo mảng $filters để chứa tất cả bộ lọc
        $filters = [
            'searchTerm' => isset($_GET['search']) ? trim($_GET['search']) : '',
            'loai_doi_tuong_id' => isset($_GET['loai_doi_tuong_id']) && is_numeric($_GET['loai_doi_tuong_id']) ? (int)$_GET['loai_doi_tuong_id'] : null,
            'trang_thai_ho_so_dt' => isset($_GET['trang_thai_ho_so_dt']) ? trim($_GET['trang_thai_ho_so_dt']) : '','trang_thai_doi_tuong' => isset($_GET['trang_thai_doi_tuong']) ? trim($_GET['trang_thai_doi_tuong']) : ''
        ];

        // <<< THAY ĐỔI: Truyền mảng $filters vào model
        $result = $this->doiTuongModel->getAll($filters, $limit, $offset);
        $doiTuongList = $result['data'];
        $totalRecords = $result['total'];
        $totalPages = ceil($totalRecords / $limit);

        // <<< THAY ĐỔI: Truyền toàn bộ mảng filters vào pagination
        $pagination = [
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'limit' => $limit,
            'totalRecords' => $totalRecords,
            'filters' => $filters 
        ];

        // <<< THÊM MỚI: Lấy danh sách các tùy chọn cho bộ lọc
        $loaiDoiTuongOptions = $this->doiTuongModel->getAllLoaiDoiTuong();
        $trangThaiOptions = $this->doiTuongModel->getTrangThaiHoSoDtOptions();
        // <<< THAY ĐỔI: Truyền thêm dữ liệu cho view
        $this->view('doi_tuong/index', [
            'title' => 'DANH SÁCH ĐỐI TƯỢNG CHÍNH SÁCH XÃ HỘI',
            'doiTuongList' => $doiTuongList,
            'pagination' => $pagination,
            'loaiDoiTuongOptions' => $loaiDoiTuongOptions, // Mới
            'trangThaiOptions' => $trangThaiOptions,     // Mới
            'currentFilters' => $filters                   // Mới
        ]);
    }

    /**
     * Hiển thị form thêm mới đối tượng
     */
    public function create() {
        $loaiDoiTuongList = $this->doiTuongModel->getAllLoaiDoiTuong();
        $thonList = $this->thonXomModel->getAllThonXomForSelect(); 

        $oldData = $_SESSION['old_form_data']['doi_tuong_create'] ?? [];
        $errors = $_SESSION['form_errors']['doi_tuong_create'] ?? [];
        unset($_SESSION['old_form_data']['doi_tuong_create']);
        unset($_SESSION['form_errors']['doi_tuong_create']);

        $this->view('doi_tuong/create', [
            'title' => 'Thêm mới hồ sơ đối tượng',
            'loaiDoiTuongList' => $loaiDoiTuongList,
            'thonList' => $thonList,
            'oldData' => $oldData,
            'errors' => $errors,
            'action' => 'create'
        ]);
    }
    private function handleMultiFileUpload($fileInput, $uploadDir = 'uploads/minhchung/') {
    $savedPaths = [];

    // Đảm bảo thư mục tồn tại
    $fullDir = __DIR__ . '/../../public/' . $uploadDir;
    if (!file_exists($fullDir)) {
        mkdir($fullDir, 0777, true);
    }

    // Kiểm tra nếu có file
    if (!isset($_FILES[$fileInput])) return $savedPaths;

    $files = $_FILES[$fileInput];
    for ($i = 0; $i < count($files['name']); $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_OK) {
            $tmpName = $files['tmp_name'][$i];
            $originalName = basename($files['name'][$i]);
            $uniqueName = uniqid() . '_' . preg_replace('/\s+/', '_', $originalName);
            $targetPath = $uploadDir . $uniqueName;

            if (move_uploaded_file($tmpName, __DIR__ . '/../../public/' . $targetPath)) {
                $savedPaths[] = BASE_URL . '/' . $targetPath;
            }
        }
    }

    return $savedPaths;
}

    /**
     * Lưu trữ đối tượng mới vào CSDL
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('doi-tuong/index');
            return;
        }

        $data = $_POST;
        $errors = $this->validateDoiTuongData($data);

        if (!empty($errors)) {
            $_SESSION['old_form_data']['doi_tuong_create'] = $data;
            $_SESSION['form_errors']['doi_tuong_create'] = $errors;
            $this->setFlashMessage('error', 'Thêm mới thất bại. Vui lòng kiểm tra lại các trường được đánh dấu đỏ.');
            $this->redirect('doi-tuong/create');
            return;
        }

        $currentUser = $this->getCurrentUser();
        $data['nguoi_tiep_nhan_dt_id'] = $currentUser['id'] ?? null; 

        $result = $this->doiTuongModel->create($data);

        if ($result) {
            $this->setFlashMessage('success', 'Thêm mới đối tượng thành công!');
            // ĐÚNG: Đã chuyển về trang danh sách
            $this->redirect('doi-tuong/index');
        } else {
            $this->setFlashMessage('error', 'Thêm mới đối tượng thất bại do lỗi hệ thống. Vui lòng thử lại.');
            $_SESSION['old_form_data']['doi_tuong_create'] = $data;
            $this->redirect('doi-tuong/create');
        }
        // Xử lý upload file mới
        $uploadedFiles = $this->handleMultiFileUpload('minh_chung_path');

        // Nếu update, giữ lại file cũ
        $existingFiles = [];
        if (isset($oldData['minh_chung_path']) && is_string($oldData['minh_chung_path'])) {
            $existingFiles = json_decode($oldData['minh_chung_path'], true);
        }

        // Gộp file mới và cũ
        $allFiles = array_merge($existingFiles, $uploadedFiles);

        // Lưu vào database (lưu dưới dạng JSON)
        $data['minh_chung_path'] = json_encode($allFiles, JSON_UNESCAPED_SLASHES);

    }

    /**
     * Hiển thị form chỉnh sửa thông tin đối tượng
     */
    public function edit($id) {
        if (!is_numeric($id) || $id <= 0) {
            $this->setFlashMessage('error', 'ID đối tượng không hợp lệ.');
            $this->redirect('doi-tuong/index');
            return;
        }

        $doiTuong = $this->doiTuongModel->findById($id);
        if (!$doiTuong) {
             $this->setFlashMessage('error', 'Không tìm thấy đối tượng.');
             $this->redirect('doi-tuong/index');
             return;
        }

        $loaiDoiTuongList = $this->doiTuongModel->getAllLoaiDoiTuong();
        $thonList = $this->thonXomModel->getAllThonXomForSelect();

        $sessionKey = 'doi_tuong_edit_'.$id;
        $oldData = $_SESSION['old_form_data'][$sessionKey] ?? $doiTuong;
        $errors = $_SESSION['form_errors'][$sessionKey] ?? [];
        unset($_SESSION['old_form_data'][$sessionKey]);
        unset($_SESSION['form_errors'][$sessionKey]);

        $this->view('doi_tuong/edit', [
            'title' => 'Chỉnh sửa Đối tượng: ' . htmlspecialchars($doiTuong['ho_ten']),
            'doiTuong' => $doiTuong,
            'loaiDoiTuongList' => $loaiDoiTuongList,
            'thonList' => $thonList,
            'oldData' => $oldData,
            'errors' => $errors
        ]);
    }

    /**
     * Cập nhật thông tin đối tượng vào CSDL
     */
    // public function update($id) {
    //     if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_numeric($id) || $id <= 0) {
    //         $this->redirect('doi-tuong/index');
    //         return;
    //     }
        
    //     $data = $_POST;
    //     $errors = $this->validateDoiTuongData($data, $id);
    //     $sessionKey = 'doi_tuong_edit_'.$id;

    //     if (!empty($errors)) {
    //         $_SESSION['old_form_data'][$sessionKey] = $data;
    //         $_SESSION['form_errors'][$sessionKey] = $errors;
    //         $this->setFlashMessage('error', 'Cập nhật thất bại. Vui lòng kiểm tra lại các trường được đánh dấu đỏ.');
    //         $this->redirect('doi-tuong/edit/' . $id);
    //         return;
    //     }

    //     $result = $this->doiTuongModel->update($id, $data);

    //     if ($result) {
    //         $this->setFlashMessage('success', 'Cập nhật thông tin đối tượng thành công!');
    //         // ĐÃ SỬA: Chuyển về trang danh sách sau khi cập nhật thành công
    //         $this->redirect('doi-tuong/index');
    //     } else {
    //          $this->setFlashMessage('error', 'Cập nhật thông tin đối tượng thất bại do lỗi hệ thống. Vui lòng thử lại.');
    //          $_SESSION['old_form_data'][$sessionKey] = $data;
    //          $this->redirect('doi-tuong/edit/' . $id);
    //     }
    // }
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_numeric($id) || $id <= 0) {
            $this->redirect('doi-tuong/index');
            return;
        }

        $data = $_POST;
        $errors = $this->validateDoiTuongData($data, $id);
        $sessionKey = 'doi_tuong_edit_' . $id;

        // Xử lý upload nhiều file (minh_chung_path)
        $uploadedPaths = [];
        if (!empty($_FILES['minh_chung_path']) && is_array($_FILES['minh_chung_path']['name'])) {
            $uploadDir = 'public/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($_FILES['minh_chung_path']['name'] as $index => $name) {
                if ($_FILES['minh_chung_path']['error'][$index] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES['minh_chung_path']['tmp_name'][$index];
                    $fileName = time() . '_' . basename($name);
                    $destination = $uploadDir . $fileName;

                    if (move_uploaded_file($tmpName, $destination)) {
                        $uploadedPaths[] = BASE_URL . '/' . $destination;
                    }
                }
            }
        }

        // Nếu có file mới thì lưu, không thì giữ lại dữ liệu cũ
        if (!empty($uploadedPaths)) {
            $data['minh_chung_path'] = json_encode($uploadedPaths, JSON_UNESCAPED_SLASHES);
        } else {
            // Giữ lại giá trị cũ nếu không upload file mới
            $existingRecord = $this->doiTuongModel->findById($id);
            $data['minh_chung_path'] = $existingRecord['minh_chung_path'] ?? null;
        }

        if (!empty($errors)) {
            $_SESSION['old_form_data'][$sessionKey] = $data;
            $_SESSION['form_errors'][$sessionKey] = $errors;
            $this->setFlashMessage('error', 'Cập nhật thất bại. Vui lòng kiểm tra lại các trường được đánh dấu đỏ.');
            $this->redirect('doi-tuong/edit/' . $id);
            return;
        }
        if (!empty($_FILES['minh_chung_path']) && is_array($_FILES['minh_chung_path']['name'])) {
    $uploadedPaths = [];

    // Lặp qua từng file
    foreach ($_FILES['minh_chung_path']['name'] as $i => $fileName) {
        if ($_FILES['minh_chung_path']['error'][$i] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['minh_chung_path']['tmp_name'][$i];
            $newName = time() . '_' . basename($fileName);
            $uploadPath = 'public/uploads' . $newName;

            if (move_uploaded_file($tmpName, $uploadPath)) {
                $uploadedPaths[] = $uploadPath;
            }
        }
    }

    // 👇 CHỖ QUAN TRỌNG: chuyển mảng thành JSON trước khi lưu
    $data['minh_chung_path'] = json_encode($uploadedPaths, JSON_UNESCAPED_SLASHES);
}
        

        $result = $this->doiTuongModel->update($id, $data);
// var_dump($data['minh_chung_path']); die();
        if ($result) {
            $this->setFlashMessage('success', 'Cập nhật thông tin đối tượng thành công!');
            $this->redirect('doi-tuong/index');
        } else {
            $this->setFlashMessage('error', 'Cập nhật thông tin đối tượng thất bại do lỗi hệ thống. Vui lòng thử lại.');
            $_SESSION['old_form_data'][$sessionKey] = $data;
            $this->redirect('doi-tuong/edit/' . $id);
        }
    }

    /**
     * Xóa đối tượng (Yêu cầu phương thức POST)
     */
    public function destroy($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
             $this->redirect('doi-tuong/index');
             return;
        }
        if (!is_numeric($id) || $id <= 0) {
           $this->setFlashMessage('error', 'ID đối tượng không hợp lệ.');
           $this->redirect('doi-tuong/index');
           return;
        }
        
        $doiTuong = $this->doiTuongModel->findById($id); 
        if (!$doiTuong) {
            $this->setFlashMessage('error', 'Không tìm thấy đối tượng để xóa.');
            $this->redirect('doi-tuong/index');
            return;
        }

        $result = $this->doiTuongModel->delete($id);

        if ($result) {
            $this->setFlashMessage('success', "Xóa đối tượng \"".htmlspecialchars($doiTuong['ho_ten'])."\" thành công!");
        } else {
            $this->setFlashMessage('error', "Xóa đối tượng \"".htmlspecialchars($doiTuong['ho_ten'])."\" thất bại. Đối tượng có thể đang có hồ sơ hoặc dữ liệu liên quan.");
        }
        // ĐÚNG: Đã chuyển về trang danh sách
        $this->redirect('doi-tuong/index');
   }
   
    /**
     * Hàm private để validate dữ liệu cho store và update
     */
    private function validateDoiTuongData($data, $excludeId = null) {
        $errors = [];
        if (empty(trim($data['ho_ten']))) {
            $errors['ho_ten'] = 'Họ tên không được để trống.';
        }
        if (empty(trim($data['ma_doi_tuong']))) {
            $errors['ma_doi_tuong'] = 'Mã đối tượng không được để trống.';
        } else if ($this->doiTuongModel->maDoiTuongExists(trim($data['ma_doi_tuong']), $excludeId)) {
             $errors['ma_doi_tuong'] = 'Mã đối tượng đã tồn tại.';
        }
        if (!empty(trim($data['cccd'])) && $this->doiTuongModel->cccdExists(trim($data['cccd']), $excludeId)) {
             $errors['cccd'] = 'Số CCCD/CMND đã tồn tại.';
        }
        if (!preg_match('/^\d{12}$/', $data['cccd'])) {
            $errors['cccd'] = 'Số định danh phải gồm đúng 12 chữ số.';
        }
        if (!empty($data['ngay_sinh'])) {
            try {
                $dateObj = new DateTime($data['ngay_sinh']);
                if ($dateObj > new DateTime()) {
                    $errors['ngay_sinh'] = 'Ngày sinh không được lớn hơn ngày hiện tại.';
                }
            } catch (Exception $e) {
                $errors['ngay_sinh'] = 'Ngày sinh không hợp lệ.';
            }
        }
        if (empty($data['thon_id'])) {
           $errors['thon_id'] = 'Vui lòng chọn thôn/xóm.';
        }
        if (!empty($data['loai_doi_tuong_id']) && in_array($data['loai_doi_tuong_id'], ['2', '3'])) {
            if (empty($data['trang_thai_ho_so_dt'])) {
                $errors['trang_thai_ho_so_dt'] = 'Vui lòng cập nhật trạng thái hồ sơ khi đối tượng đã chết hoặc mất tích.';
            }
        }
        return $errors;
    }

    /**
     * Tìm kiếm đối tượng và trả về JSON cho AJAX
     */
    public function searchJson() {
        $this->checkAuth();
        $searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

        if (strlen($searchTerm) < 2) {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit;
        }
        
        $results = $this->doiTuongModel->searchForSelection($searchTerm, 10);

        header('Content-Type: application/json');
        echo json_encode($results);
        exit;
    }

    /**
     * Tìm kiếm đối tượng đủ điều kiện (chưa có hồ sơ trợ cấp đang hoạt động/chờ)
     * và trả về JSON cho AJAX.
     */
    public function searchJsonEligibleHstc() {
        $this->checkAuth();
        $searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

        if (strlen($searchTerm) < 2) {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit;
        }
        
        // Gọi đến hàm mới trong Model
        $results = $this->doiTuongModel->searchForSelectionEligibleForHstc($searchTerm, 10);

        header('Content-Type: application/json');
        echo json_encode($results);
        exit;
    }

    // <<< THÊM MỚI: Action tìm kiếm đối tượng đủ điều kiện cho Hồ sơ Chăm sóc CĐ
    /**
     * Tìm kiếm đối tượng đủ điều kiện (chưa có hồ sơ chăm sóc CĐ đang hoạt động/chờ)
     * và trả về JSON cho AJAX.
     */
    public function searchJsonEligibleHscc() {
        $this->checkAuth();
        $searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

        if (strlen($searchTerm) < 2) {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit;
        }
        
        // Gọi đến hàm mới trong Model
        $results = $this->doiTuongModel->searchForSelectionEligibleForHscc($searchTerm, 10);

        header('Content-Type: application/json');
        echo json_encode($results);
        exit;
    }
    // public function exportCsv() {
    //     // Kiểm tra quyền truy cập
    //     $this->checkAuth();

    //     // 1. Thiết lập HTTP headers để trình duyệt tải về file CSV
    //     $filename = 'DanhSach_HoSoChamSocCD_' . date('d-m-Y') . '.csv';
    //     header('Content-Type: text/csv; charset=utf-8');
    //     header('Content-Disposition: attachment; filename="' . $filename . '"');

    //     // 2. Mở output stream của PHP để ghi dữ liệu
    //     $output = fopen('php://output', 'w');
        
    //     // Thêm BOM (Byte Order Mark) để Excel mở tiếng Việt có dấu đúng
    //     fputs($output, "\xEF\xBB\xBF");

    //     // 3. Ghi dòng tiêu đề vào file CSV
    //     $headers = [
    //         'STT', 'Mã Hồ sơ', 'Tên Đối tượng', 'Mã Đối tượng', 'Loại hình chăm sóc', 
    //         'Người chăm sóc', 'Nội dung đề nghị', 'Hình thức cụ thể', 'Kinh phí dự kiến (VNĐ)',
    //         'Nguồn kinh phí', 'Ngày đề nghị', 'Ngày bắt đầu', 'Ngày kết thúc dự kiến',
    //         'Trạng thái', 'Người lập', 'Người duyệt', 'Ngày duyệt', 'Lý do thay đổi', 'Ghi chú'
    //     ];
    //     fputcsv($output, $headers);

    //     // 4. Lấy các bộ lọc từ URL
    //     $filters = [
    //         'searchTerm' => isset($_GET['search']) ? trim($_GET['search']) : '',
    //         'doi_tuong_id' => isset($_GET['doi_tuong_id']) && is_numeric($_GET['doi_tuong_id']) ? (int)$_GET['doi_tuong_id'] : null,
    //         'trang_thai' => isset($_GET['trang_thai']) ? trim($_GET['trang_thai']) : ''
    //     ];

    //     // 5. Lấy toàn bộ dữ liệu đã lọc từ Model
    //     $hoSoList = $this->hoSoChamSocModel->getAllForExport($filters);
        
    //     // Lấy danh sách trạng thái để dịch
    //     $trangThaiOptions = $this->hoSoChamSocModel->getTrangThaiOptions();

    //     // 6. Lặp qua dữ liệu và ghi từng dòng vào file CSV
    //     foreach ($hoSoList as $index => $hs) {
    //         $rowData = [
    //             $index + 1,
    //             $hs['ma_ho_so_cs'] ?? '',
    //             $hs['ten_doi_tuong'] ?? '',
    //             $hs['ma_doi_tuong'] ?? '',
    //             $hs['ten_loai_hinh_cham_soc'] ?? '',
    //             $hs['ten_nguoi_cham_soc'] ?? '',
    //             $hs['noi_dung_de_nghi'] ?? '',
    //             $hs['hinh_thuc_cham_soc_cu_the'] ?? '',
    //             $hs['kinh_phi_du_kien'] ?? 0,
    //             $hs['nguon_kinh_phi'] ?? '',
    //             !empty($hs['ngay_de_nghi_cs']) ? date('d/m/Y', strtotime($hs['ngay_de_nghi_cs'])) : '',
    //             !empty($hs['ngay_bat_dau_cham_soc']) ? date('d/m/Y', strtotime($hs['ngay_bat_dau_cham_soc'])) : '',
    //             !empty($hs['ngay_ket_thuc_du_kien_cs']) ? date('d/m/Y', strtotime($hs['ngay_ket_thuc_du_kien_cs'])) : '',
    //             $trangThaiOptions[$hs['trang_thai_hs_cs']] ?? $hs['trang_thai_hs_cs'],
    //             $hs['ten_nguoi_lap'] ?? '',
    //             $hs['ten_nguoi_xet_duyet'] ?? '',
    //             !empty($hs['ngay_xet_duyet_hs_cs']) ? date('d/m/Y', strtotime($hs['ngay_xet_duyet_hs_cs'])) : '',
    //             $hs['ly_do_thay_doi_trang_thai_cs'] ?? '',
    //             $hs['ghi_chu_hs_cs'] ?? '',
    //         ];
            
    //         fputcsv($output, $rowData);
    //     }

    //     // 7. Đóng stream và dừng script
    //     fclose($output);
    //     exit();
    // }
}