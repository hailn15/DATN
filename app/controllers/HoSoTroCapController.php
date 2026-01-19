<?php
// app/controllers/HoSoTroCapController.php

class HoSoTroCapController extends BaseController {

    private $hoSoTroCapModel;
    private $doiTuongModel; 
    private $mucTroCapModel;

    public function __construct() {
        parent::__construct();
        $this->checkAuth(); 

        $this->hoSoTroCapModel = new HoSoTroCap($this->db);
        $this->doiTuongModel = new DoiTuong($this->db); 
        $this->mucTroCapModel = new MucTroCapHangThang($this->db);
    }

    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 15;
        $offset = ($page - 1) * $limit;
        
        $filters = [
            'searchTerm' => isset($_GET['search']) ? trim($_GET['search']) : '',
            'doi_tuong_id' => isset($_GET['doi_tuong_id']) && is_numeric($_GET['doi_tuong_id']) ? (int)$_GET['doi_tuong_id'] : null,
            'trang_thai' => isset($_GET['trang_thai']) ? trim($_GET['trang_thai']) : ''
        ];

        $result = $this->hoSoTroCapModel->getAll($filters, $limit, $offset);
        $hoSoList = $result['data'];
        $totalRecords = $result['total'];
        $totalPages = ceil($totalRecords / $limit);

        $pagination = [
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'limit' => $limit,
            'totalRecords' => $totalRecords,
            'filters' => $filters 
        ];
        
        $trangThaiOptions = $this->hoSoTroCapModel->getTrangThaiOptions();
        
        $doiTuongContext = null;
        if ($filters['doi_tuong_id']) {
            $doiTuongContext = $this->doiTuongModel->findById($filters['doi_tuong_id']);
        }

        $this->view('ho_so_tro_cap/index', [
            'title' => 'Danh sách Hồ sơ Trợ cấp Hàng tháng' . ($doiTuongContext ? ' cho ' . htmlspecialchars($doiTuongContext['ho_ten']) : ''),
            'hoSoList' => $hoSoList,
            'pagination' => $pagination,
            'trangThaiOptions' => $trangThaiOptions,
            'currentFilters' => $filters,
            'doiTuongContext' => $doiTuongContext
        ]);
    }

    public function create($doi_tuong_id = null) {
        if (!is_numeric($doi_tuong_id) || $doi_tuong_id <= 0) {
            $this->setFlashMessage('error', 'ID đối tượng không hợp lệ để tạo hồ sơ.');
            $this->redirect('doi-tuong/index'); 
            return;
        }

        $doiTuong = $this->doiTuongModel->findById($doi_tuong_id);
        if (!$doiTuong) {
            $this->setFlashMessage('error', 'Không tìm thấy đối tượng.');
            $this->redirect('doi-tuong/index');
            return;
        }

        // <<< THÊM MỚI: KIỂM TRA TRẠNG THÁI DUYỆT CỦA ĐỐI TƯỢNG >>>
        if ($doiTuong['trang_thai_ho_so_dt'] !== 'da_duyet_thong_tin') {
            // Lấy tên trạng thái để hiển thị cho thân thiện
            $allTrangThaiDt = $this->doiTuongModel->getTrangThaiHoSoDtOptions();
            $tenTrangThaiHienTai = $allTrangThaiDt[$doiTuong['trang_thai_ho_so_dt']] ?? 'Chưa xác định';
            
            $message = 'Đối tượng "' . htmlspecialchars($doiTuong['ho_ten']) . '" chưa được duyệt thông tin (Trạng thái hiện tại: ' . $tenTrangThaiHienTai . '). Không thể tạo hồ sơ trợ cấp.';
            $this->setFlashMessage('error', $message);
            
            // Chuyển hướng về trang chỉnh sửa của chính đối tượng đó để người dùng dễ xử lý
            $this->redirect('doi-tuong/edit/' . $doi_tuong_id);
            return;
        }
        // <<< KẾT THÚC KHỐI KIỂM TRA >>>

        $trangThaiOptions = $this->hoSoTroCapModel->getTrangThaiOptions();
        $mucTroCapOptions = $this->mucTroCapModel->getAllMucTroCapForSelect();

        if (empty($mucTroCapOptions)) {
            $this->setFlashMessage('warning', 'Chưa có Mức trợ cấp hàng tháng nào được định nghĩa. Vui lòng thêm Mức trợ cấp trước.');
        }

        $this->view('ho_so_tro_cap/create', [
            'title' => 'Thêm Hồ sơ Trợ cấp cho: ' . htmlspecialchars($doiTuong['ho_ten']),
            'doiTuong' => $doiTuong,
            'trangThaiOptions' => $trangThaiOptions,
            'mucTroCapOptions' => $mucTroCapOptions,
            'defaultTrangThai' => 'cho_xem_xet',
            'oldData' => $_SESSION['old_form_data']['ho_so_tro_cap_create'] ?? [],
            'errors' => $_SESSION['form_errors']['ho_so_tro_cap_create'] ?? []
        ]);
        unset($_SESSION['old_form_data']['ho_so_tro_cap_create'], $_SESSION['form_errors']['ho_so_tro_cap_create']);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('ho-so-tro-cap/index');
            return;
        }

        $data = $_POST;
        $doi_tuong_id = $data['doi_tuong_id'] ?? null;
        $currentUser = $this->getCurrentUser();
        $data['nguoi_lap_id'] = $currentUser['id'] ?? null;

        $errors = $this->validateHoSoData($data);

        if (!empty($errors)) {
            $this->setFlashMessage('error', 'Thêm mới hồ sơ thất bại. Vui lòng kiểm tra lại thông tin.');
            $_SESSION['old_form_data']['ho_so_tro_cap_create'] = $data;
            $_SESSION['form_errors']['ho_so_tro_cap_create'] = $errors;
            $this->redirect('ho-so-tro-cap/create/' . $doi_tuong_id);
            return;
        }

        $result = $this->hoSoTroCapModel->create($data);

        if ($result) {
            $this->setFlashMessage('success', 'Thêm mới hồ sơ trợ cấp thành công!');
            // ĐÚNG: Đã chuyển về trang danh sách (có thể lọc theo đối tượng)
            $this->redirect('ho-so-tro-cap/index?doi_tuong_id=' . $doi_tuong_id);
        } 
        else {
            $this->setFlashMessage('error', 'Thêm mới hồ sơ trợ cấp thất bại.');
            $_SESSION['old_form_data']['ho_so_tro_cap_create'] = $data;
            $this->redirect('ho-so-tro-cap/create/' . $doi_tuong_id);
        }
    }

    public function edit($id) {
        if (!is_numeric($id) || $id <= 0) {
            $this->setFlashMessage('error', 'ID hồ sơ không hợp lệ.');
            $this->redirect('ho-so-tro-cap/index');
            return;
        }

        $hoSo = $this->hoSoTroCapModel->findById($id);

        if (!$hoSo) {
            $this->setFlashMessage('error', 'Không tìm thấy hồ sơ trợ cấp.');
            $this->redirect('ho-so-tro-cap/index');
            return;
        }
        
        $trangThaiOptions = $this->hoSoTroCapModel->getTrangThaiOptions();
        $mucTroCapOptions = $this->mucTroCapModel->getAllMucTroCapForSelect();

        if ($hoSo && !empty($hoSo['muc_tro_cap_id'])) {
            $isCurrentMucInOptions = array_search($hoSo['muc_tro_cap_id'], array_column($mucTroCapOptions, 'id')) !== false;
            if (!$isCurrentMucInOptions) {
                $currentSelectedMuc = $this->mucTroCapModel->findById($hoSo['muc_tro_cap_id']);
                if ($currentSelectedMuc) {
                    array_unshift($mucTroCapOptions, $currentSelectedMuc);
                }
            }
        }

        $this->view('ho_so_tro_cap/edit', [
            'title' => 'Chỉnh sửa Hồ sơ Trợ cấp: ' . htmlspecialchars($hoSo['ma_ho_so']),
            'hoSo' => $hoSo,
            'trangThaiOptions' => $trangThaiOptions,
            'mucTroCapOptions' => $mucTroCapOptions,
            'oldData' => $_SESSION['old_form_data']['ho_so_tro_cap_edit_'.$id] ?? $hoSo,
            'errors' => $_SESSION['form_errors']['ho_so_tro_cap_edit_'.$id] ?? []
        ]);
        unset($_SESSION['old_form_data']['ho_so_tro_cap_edit_'.$id], $_SESSION['form_errors']['ho_so_tro_cap_edit_'.$id]);
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_numeric($id) || $id <= 0) {
            $this->redirect('ho-so-tro-cap/index');
            return;
        }
        
        $hoSoGoc = $this->hoSoTroCapModel->findById($id);
        if (!$hoSoGoc) {
            $this->setFlashMessage('error', 'Không tìm thấy hồ sơ để cập nhật.');
            $this->redirect('ho-so-tro-cap/index');
            return;
        }

        $data = $_POST;
        $errors = $this->validateHoSoData($data, $id);
        
        if (!empty($errors)) {
            $this->setFlashMessage('error', 'Cập nhật hồ sơ thất bại. Vui lòng kiểm tra lại thông tin.');
            $_SESSION['old_form_data']['ho_so_tro_cap_edit_'.$id] = $data;
            $_SESSION['form_errors']['ho_so_tro_cap_edit_'.$id] = $errors;
            $this->redirect('ho-so-tro-cap/edit/' . $id);
            return;
        }
        
        $result = $this->hoSoTroCapModel->update($id, $data);

        if ($result) {
            $this->setFlashMessage('success', 'Cập nhật hồ sơ trợ cấp thành công!');
            // ĐÃ SỬA: Chuyển về trang danh sách (có thể lọc theo đối tượng)
            $redirect_param = !empty($hoSoGoc['doi_tuong_id']) ? '?doi_tuong_id=' . $hoSoGoc['doi_tuong_id'] : '';
            $this->redirect('ho-so-tro-cap/index' . $redirect_param);
        } else {
            $this->setFlashMessage('error', 'Cập nhật hồ sơ trợ cấp thất bại.');
            $_SESSION['old_form_data']['ho_so_tro_cap_edit_'.$id] = $data;
            $this->redirect('ho-so-tro-cap/edit/' . $id);
        }
    }
    
    private function validateHoSoData($data, $excludeId = null) {
        $errors = [];
        $currentUser = $this->getCurrentUser();
        
        // Mã hồ sơ (chỉ validate khi tạo mới)
        if (!$excludeId) {
            if (empty($data['ma_ho_so'])) {
                $errors['ma_ho_so'] = 'Mã hồ sơ không được để trống.';
            } elseif ($this->hoSoTroCapModel->maHoSoExists($data['ma_ho_so'])) {
                $errors['ma_ho_so'] = 'Mã hồ sơ đã tồn tại.';
            }
        }

        if (empty($data['muc_tro_cap_id']) || !is_numeric($data['muc_tro_cap_id'])) {
            $errors['muc_tro_cap_id'] = 'Vui lòng chọn một Mức trợ cấp.';
        } else {
            $selectedMucTroCap = $this->mucTroCapModel->findById($data['muc_tro_cap_id']);
            if (!$selectedMucTroCap || (!$excludeId && $selectedMucTroCap['trang_thai_ap_dung'] !== 'dang_ap_dung')) {
                $errors['muc_tro_cap_id'] = 'Mức trợ cấp không hợp lệ hoặc không còn áp dụng.';
            } else {
                // Luôn gán lại số tiền từ mức đã chọn để đảm bảo nhất quán
                $data['muc_tro_cap_hang_thang'] = $selectedMucTroCap['so_tien_ap_dung'];
            }
        }
        
        if (empty($data['ngay_de_nghi_huong'])) {
            $errors['ngay_de_nghi_huong'] = 'Ngày tiếp nhận hưởng không được để trống.';
        }

        if (!empty($data['ngay_ket_thuc_huong']) && !empty($data['ngay_bat_dau_huong']) && strtotime($data['ngay_ket_thuc_huong']) < strtotime($data['ngay_bat_dau_huong'])) {
            $errors['ngay_ket_thuc_huong'] = 'Ngày kết thúc không được nhỏ hơn ngày bắt đầu.';
        }

        $trangThaiOptions = $this->hoSoTroCapModel->getTrangThaiOptions();
        if (empty($data['trang_thai']) || !array_key_exists($data['trang_thai'], $trangThaiOptions)) {
            $errors['trang_thai'] = 'Trạng thái hồ sơ không hợp lệ.';
        } else {
            if ($data['trang_thai'] === 'da_phe_duyet_dang_huong') {
                if (empty($data['nguoi_duyet_id'])) $data['nguoi_duyet_id'] = $currentUser['id'] ?? null;
                if (empty($data['ngay_duyet'])) $data['ngay_duyet'] = date('Y-m-d');
                
                if (empty($data['nguoi_duyet_id'])) $errors['nguoi_duyet_id'] = 'Cần người duyệt khi phê duyệt.';
                if (empty($data['ngay_duyet'])) $errors['ngay_duyet'] = 'Cần ngày duyệt khi phê duyệt.';
            }
             if (in_array($data['trang_thai'], ['tam_dung_huong', 'da_dung_huong', 'khong_du_dieu_kien']) && empty($data['ly_do_thay_doi_trang_thai'])) {
                 $errors['ly_do_thay_doi_trang_thai'] = 'Cần nhập lý do khi chọn trạng thái "' . $trangThaiOptions[$data['trang_thai']] . '".';
             }
        }

        return $errors;
    }

    public function show($id) {
        if (!is_numeric($id) || $id <= 0) {
            $this->setFlashMessage('error', 'ID hồ sơ không hợp lệ.');
            $this->redirect('ho-so-tro-cap/index');
            return;
        }

        $hoSo = $this->hoSoTroCapModel->findById($id);

        if (!$hoSo) {
            $this->setFlashMessage('error', 'Không tìm thấy hồ sơ trợ cấp.');
            $this->redirect('ho-so-tro-cap/index');
            return;
        }
        $trangThaiOptions = $this->hoSoTroCapModel->getTrangThaiOptions();

        $this->view('ho_so_tro_cap/show', [
            'title' => 'Chi tiết Hồ sơ: ' . htmlspecialchars($hoSo['ma_ho_so']),
            'hoSo' => $hoSo,
            'trangThaiOptions' => $trangThaiOptions
        ]);
    }

    public function destroy($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('ho-so-tro-cap/index');
            return;
        }
        if (!is_numeric($id) || $id <= 0) {
            $this->setFlashMessage('error', 'ID hồ sơ không hợp lệ.');
            $this->redirect('ho-so-tro-cap/index');
            return;
        }

        // Vẫn nên kiểm tra xem hồ sơ có tồn tại không trước khi xóa
        $hoSo = $this->hoSoTroCapModel->findById($id);
        if (!$hoSo) {
            $this->setFlashMessage('error', 'Không tìm thấy hồ sơ để xóa.');
            $this->redirect('ho-so-tro-cap/index');
            return;
        }
        
        $result = $this->hoSoTroCapModel->delete($id);

        if ($result) {
            $this->setFlashMessage('success', 'Xóa hồ sơ trợ cấp thành công!');
        } else {
            $this->setFlashMessage('error', 'Xóa hồ sơ trợ cấp thất bại. Hồ sơ có thể có dữ liệu chi trả liên quan.');
        }

        // <<< THAY ĐỔI: Luôn chuyển hướng về trang danh sách chung >>>
        $this->redirect('ho-so-tro-cap/index');
    }

    // /**
    //  * Xử lý yêu cầu xuất danh sách hồ sơ ra file CSV.
    //  * Không cần thư viện bên ngoài.
    //  */
    // public function exportCsv() {
    //     // Kiểm tra quyền truy cập
    //     $this->checkAuth();

    //     // 1. Thiết lập HTTP headers để trình duyệt tải về file CSV
    //     $filename = 'DanhSach_HoSoTroCap_' . date('d-m-Y') . '.csv';
    //     header('Content-Type: text/csv; charset=utf-8');
    //     header('Content-Disposition: attachment; filename="' . $filename . '"');

    //     // 2. Mở output stream của PHP để ghi dữ liệu
    //     $output = fopen('php://output', 'w');
        
    //     // **QUAN TRỌNG: Thêm BOM (Byte Order Mark) để Excel mở tiếng Việt có dấu đúng**
    //     // Đây là một "mẹo" để Excel trên Windows nhận diện đúng encoding UTF-8
    //     fputs($output, "\xEF\xBB\xBF");

    //     // 3. Ghi dòng tiêu đề vào file CSV
    //     $headers = [
    //         'STT', 'Mã Hồ sơ', 'Họ tên Đối tượng', 'Mã Đối tượng',
    //         'Tên Mức Trợ Cấp', 'Ngày Bắt đầu Hưởng', 'Ngày Kết thúc Hưởng',
    //         'Số tiền hưởng (VNĐ)', 'Trạng thái', 'Ngày duyệt', 'Người duyệt',
    //         'Lý do thay đổi', 'Ghi chú'
    //     ];
    //     fputcsv($output, $headers);

    //     // 4. Lấy các bộ lọc từ URL (giống hệt hàm index)
    //     $filters = [
    //         'searchTerm' => isset($_GET['search']) ? trim($_GET['search']) : '',
    //         'doi_tuong_id' => isset($_GET['doi_tuong_id']) && is_numeric($_GET['doi_tuong_id']) ? (int)$_GET['doi_tuong_id'] : null,
    //         'trang_thai' => isset($_GET['trang_thai']) ? trim($_GET['trang_thai']) : ''
    //     ];

    //     // 5. Lấy toàn bộ dữ liệu đã lọc từ Model
    //     $hoSoList = $this->hoSoTroCapModel->getAllForExport($filters);
        
    //     // Lấy danh sách trạng thái để dịch
    //     $trangThaiOptions = $this->hoSoTroCapModel->getTrangThaiOptions();

    //     // 6. Lặp qua dữ liệu và ghi từng dòng vào file CSV
    //     foreach ($hoSoList as $index => $hs) {
    //         $rowData = [
    //             $index + 1,
    //             $hs['ma_ho_so'] ?? '',
    //             $hs['ten_doi_tuong'] ?? '',
    //             $hs['ma_doi_tuong'] ?? '',
    //             $hs['ten_muc_tro_cap'] ?? '',
    //             !empty($hs['ngay_bat_dau_huong']) ? date('d/m/Y', strtotime($hs['ngay_bat_dau_huong'])) : '',
    //             !empty($hs['ngay_ket_thuc_huong']) ? date('d/m/Y', strtotime($hs['ngay_ket_thuc_huong'])) : '',
    //             $hs['muc_tro_cap_hang_thang'] ?? 0,
    //             $trangThaiOptions[$hs['trang_thai']] ?? $hs['trang_thai'], // Dịch trạng thái
    //             !empty($hs['ngay_duyet']) ? date('d/m/Y', strtotime($hs['ngay_duyet'])) : '',
    //             $hs['ten_nguoi_duyet'] ?? '',
    //             $hs['ly_do_thay_doi_trang_thai'] ?? '',
    //             $hs['ghi_chu_hs'] ?? '',
    //         ];
            
    //         fputcsv($output, $rowData);
    //     }

    //     // 7. Đóng stream và dừng script
    //     fclose($output);
    //     exit();
    // }
    public function exportCSV()
    {
        // Kiểm tra quyền truy cập
        $this->checkAuth();

        // Lấy các bộ lọc từ URL
        $filters = [
            'searchTerm' => $_GET['search'] ?? '',
            'doi_tuong_id' => isset($_GET['doi_tuong_id']) && is_numeric($_GET['doi_tuong_id']) ? (int)$_GET['doi_tuong_id'] : null,
            'trang_thai' => $_GET['trang_thai'] ?? '',
        ];

        // Lấy dữ liệu từ model
        $hoSoList = $this->hoSoTroCapModel->getAllForExport($filters);
        $trangThaiOptions = $this->hoSoTroCapModel->getTrangThaiOptions();

        // Tên file
        $filename = 'DanhSach_HoSoTroCap_' . date('d-m-Y') . '.xls';

        // Headers để trình duyệt hiểu đây là file Excel
        header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Cache-Control: max-age=0");

        // Thêm BOM để hỗ trợ tiếng Việt
        echo "\xEF\xBB\xBF";

        // Mở bảng HTML
        echo '<table border="1">';
        echo '<thead><tr>';
        echo '<th>STT</th>';
        echo '<th>Mã Hồ sơ</th>';
        echo '<th>Họ tên Đối tượng</th>';
        echo '<th>Mã Đối tượng</th>';
        echo '<th>Tên Mức Trợ Cấp</th>';
        echo '<th>Ngày Bắt đầu Hưởng</th>';
        echo '<th>Ngày Kết thúc Hưởng</th>';
        echo '<th>Số tiền hưởng (VNĐ)</th>';
        echo '<th>Trạng thái</th>';
        echo '<th>Ngày duyệt</th>';
        echo '<th>Người duyệt</th>';
        echo '<th>Lý do thay đổi</th>';
        echo '<th>Ghi chú</th>';
        echo '</tr></thead>';

        echo '<tbody>';
        foreach ($hoSoList as $index => $hs) {
            echo '<tr>';
            echo '<td>' . ($index + 1) . '</td>';
            echo '<td>' . htmlspecialchars($hs['ma_ho_so'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($hs['ten_doi_tuong'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($hs['ma_doi_tuong'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($hs['ten_muc_tro_cap'] ?? '') . '</td>';
            echo '<td>' . (!empty($hs['ngay_bat_dau_huong']) ? date('d/m/Y', strtotime($hs['ngay_bat_dau_huong'])) : '') . '</td>';
            echo '<td>' . (!empty($hs['ngay_ket_thuc_huong']) ? date('d/m/Y', strtotime($hs['ngay_ket_thuc_huong'])) : '') . '</td>';
            echo '<td>' . number_format($hs['muc_tro_cap_hang_thang'] ?? 0) . '</td>';
            echo '<td>' . htmlspecialchars($trangThaiOptions[$hs['trang_thai']] ?? $hs['trang_thai']) . '</td>';
            echo '<td>' . (!empty($hs['ngay_duyet']) ? date('d/m/Y', strtotime($hs['ngay_duyet'])) : '') . '</td>';
            echo '<td>' . htmlspecialchars($hs['ten_nguoi_duyet'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($hs['ly_do_thay_doi_trang_thai'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($hs['ghi_chu_hs'] ?? '') . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';

        exit();
    }

}