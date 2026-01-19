<?php
// app/controllers/HoSoChamSocCongDongController.php

class HoSoChamSocCongDongController extends BaseController {

    private $hoSoChamSocModel;
    private $doiTuongModel;
    private $loaiHinhCSModel;
    private $nguoiChamSocModel;

    public function __construct() {
        parent::__construct();
        $this->checkAuth();

        $this->hoSoChamSocModel = new HoSoChamSocCongDong($this->db);
        $this->doiTuongModel = new DoiTuong($this->db);
        $this->loaiHinhCSModel = new LoaiHinhChamSocCD($this->db);
        $this->nguoiChamSocModel = new NguoiChamSoc($this->db);
        
        
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

        $result = $this->hoSoChamSocModel->getAll($filters, $limit, $offset);
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

        $trangThaiOptions = $this->hoSoChamSocModel->getTrangThaiOptions();
        $doiTuongContext = null;
        if ($filters['doi_tuong_id']) {
            $doiTuongContext = $this->doiTuongModel->findById($filters['doi_tuong_id']);
        }

        $this->view('ho_so_cham_soc_cong_dong/index', [
            'title' => 'Danh sách đối tượng được chăm sóc' . ($doiTuongContext ? ' cho ' . htmlspecialchars($doiTuongContext['ho_ten']) : ''),
            'hoSoList' => $hoSoList,
            'pagination' => $pagination,
            'trangThaiOptions' => $trangThaiOptions,
            'currentFilters' => $filters,
            'doiTuongContext' => $doiTuongContext
        ]);
    }

    public function create($doi_tuong_id = null) {
        if (!is_numeric($doi_tuong_id) || $doi_tuong_id <= 0) {
            $this->setFlashMessage('error', 'ID đối tượng không hợp lệ.');
            $this->redirect('doi-tuong/index');
            return;
        }

        $doiTuong = $this->doiTuongModel->findById($doi_tuong_id);
        if (!$doiTuong) {
            $this->setFlashMessage('error', 'Không tìm thấy đối tượng.');
            $this->redirect('doi-tuong/index');
            return;
        }

        if ($doiTuong['trang_thai_ho_so_dt'] !== 'da_duyet_thong_tin') {
            // Lấy tên trạng thái để hiển thị cho thân thiện
            $allTrangThaiDt = $this->doiTuongModel->getTrangThaiHoSoDtOptions();
            $tenTrangThaiHienTai = $allTrangThaiDt[$doiTuong['trang_thai_ho_so_dt']] ?? 'Chưa xác định';
            
            $message = 'Đối tượng "' . htmlspecialchars($doiTuong['ho_ten']) . '" chưa được duyệt thông tin (Trạng thái hiện tại: ' . $tenTrangThaiHienTai . '). Không thể tạo hồ sơ chăm sóc cộng đồng.';
            $this->setFlashMessage('error', $message);
            
            // Chuyển hướng về trang chỉnh sửa của chính đối tượng đó để người dùng dễ xử lý
            $this->redirect('doi-tuong/edit/' . $doi_tuong_id);
            return;
        }

        $trangThaiOptions = $this->hoSoChamSocModel->getTrangThaiOptions();
        $loaiHinhCSOptions = $this->loaiHinhCSModel->getAllLoaiHinhChamSocCDForSelect();
        $nguoiChamSocOptions = $this->nguoiChamSocModel->getAllForSelect();

        $this->view('ho_so_cham_soc_cong_dong/create', [
            'title' => 'Thêm Hồ sơ Chăm sóc CĐ cho: ' . htmlspecialchars($doiTuong['ho_ten']),
            'doiTuong' => $doiTuong,
            'trangThaiOptions' => $trangThaiOptions,
            'loaiHinhCSOptions' => $loaiHinhCSOptions,
            'nguoiChamSocOptions' => $nguoiChamSocOptions,
            'defaultTrangThai' => 'cho_xem_xet',
            'oldData' => $_SESSION['old_form_data']['ho_so_cham_soc_create'] ?? [],
            'errors' => $_SESSION['form_errors']['ho_so_cham_soc_create'] ?? []
        ]);
        unset($_SESSION['old_form_data']['ho_so_cham_soc_create']);
        unset($_SESSION['form_errors']['ho_so_cham_soc_create']);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('ho-so-cham-soc-cong-dong/index');
            return;
        }

        $data = $_POST;
        $doi_tuong_id = $data['doi_tuong_id'] ?? null;
        $currentUser = $this->getCurrentUser();
        $data['nguoi_lap_hs_cs_id'] = $currentUser['id'] ?? null;

        $errors = $this->validateHoSoData($data);

        if (!empty($errors)) {
            $this->setFlashMessage('error', 'Thêm mới thất bại. Vui lòng kiểm tra lại lỗi.');
            $_SESSION['old_form_data']['ho_so_cham_soc_create'] = $data;
            $_SESSION['form_errors']['ho_so_cham_soc_create'] = $errors;
            $this->redirect('ho-so-cham-soc-cong-dong/create/' . $doi_tuong_id);
            return;
        }
        
        $result = $this->hoSoChamSocModel->create($data);

        if ($result) {
            $this->setFlashMessage('success', 'Thêm mới hồ sơ chăm sóc cộng đồng thành công!');
            // ĐÚNG: Đã chuyển về trang danh sách (có thể lọc theo đối tượng)
            $this->redirect('ho-so-cham-soc-cong-dong/index?doi_tuong_id=' . $doi_tuong_id);
        } else {
            $this->setFlashMessage('error', 'Thêm mới hồ sơ chăm sóc cộng đồng thất bại.');
            $_SESSION['old_form_data']['ho_so_cham_soc_create'] = $data;
            $this->redirect('ho-so-cham-soc-cong-dong/create/' . $doi_tuong_id);
        }
    }

    public function edit($id) {
        if (!is_numeric($id) || $id <= 0) {
            $this->setFlashMessage('error', 'ID hồ sơ không hợp lệ.');
            $this->redirect('ho-so-cham-soc-cong-dong/index');
            return;
        }

        $hoSo = $this->hoSoChamSocModel->findById($id);

        if (!$hoSo) {
            $this->setFlashMessage('error', 'Không tìm thấy hồ sơ.');
            $this->redirect('ho-so-cham-soc-cong-dong/index');
            return;
        }
        
        $trangThaiOptions = $this->hoSoChamSocModel->getTrangThaiOptions();
        $loaiHinhCSOptions = $this->loaiHinhCSModel->getAllLoaiHinhChamSocCDForSelect();
        $nguoiChamSocOptions = $this->nguoiChamSocModel->getAllForSelect();

        if (!empty($hoSo['loai_hinh_cham_soc_cd_id'])) {
            $isCurrentLHInOptions = array_search($hoSo['loai_hinh_cham_soc_cd_id'], array_column($loaiHinhCSOptions, 'id')) !== false;
            if (!$isCurrentLHInOptions) {
                $currentSelectedLH = $this->loaiHinhCSModel->findById($hoSo['loai_hinh_cham_soc_cd_id']);
                if ($currentSelectedLH) {
                    array_unshift($loaiHinhCSOptions, $currentSelectedLH);
                }
            }
        }
        
        $this->view('ho_so_cham_soc_cong_dong/edit', [
            'title' => 'Chỉnh sửa Hồ sơ Chăm sóc CĐ: ' . htmlspecialchars($hoSo['ma_ho_so_cs']),
            'hoSo' => $hoSo, 
            'trangThaiOptions' => $trangThaiOptions,
            'loaiHinhCSOptions' => $loaiHinhCSOptions,
            'nguoiChamSocOptions' => $nguoiChamSocOptions,
            'oldData' => $_SESSION['old_form_data']['ho_so_cham_soc_edit_'.$id] ?? $hoSo, 
            'errors' => $_SESSION['form_errors']['ho_so_cham_soc_edit_'.$id] ?? []
        ]);
        unset($_SESSION['old_form_data']['ho_so_cham_soc_edit_'.$id], $_SESSION['form_errors']['ho_so_cham_soc_edit_'.$id]);
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_numeric($id) || $id <= 0) {
            $this->redirect('ho-so-cham-soc-cong-dong/index');
            return;
        }

        $hoSoGoc = $this->hoSoChamSocModel->findById($id);
        if (!$hoSoGoc) {
            $this->setFlashMessage('error', 'Không tìm thấy hồ sơ để cập nhật.');
            $this->redirect('ho-so-cham-soc-cong-dong/index');
            return;
        }

        $data = $_POST;
        $errors = $this->validateHoSoData($data, $id);

        if (!empty($errors)) {
            $this->setFlashMessage('error', 'Cập nhật thất bại. Vui lòng kiểm tra lại lỗi.');
            $_SESSION['old_form_data']['ho_so_cham_soc_edit_'.$id] = $data;
            $_SESSION['form_errors']['ho_so_cham_soc_edit_'.$id] = $errors;
            $this->redirect('ho-so-cham-soc-cong-dong/edit/' . $id);
            return;
        }
        
        if ($this->hoSoChamSocModel->update($id, $data)) {
            $this->setFlashMessage('success', 'Cập nhật hồ sơ thành công!');
            // ĐÃ SỬA: Chuyển về trang danh sách sau khi cập nhật
            $redirect_param = !empty($hoSoGoc['doi_tuong_id']) ? '?doi_tuong_id=' . $hoSoGoc['doi_tuong_id'] : '';
            $this->redirect('ho-so-cham-soc-cong-dong/index' . $redirect_param);
        } else {
            $this->setFlashMessage('error', 'Cập nhật hồ sơ thất bại.');
            $_SESSION['old_form_data']['ho_so_cham_soc_edit_'.$id] = $data;
            $this->redirect('ho-so-cham-soc-cong-dong/edit/' . $id);
        }
    }

    private function validateHoSoData($data, $excludeId = null) {
        $errors = [];
        // Mã hồ sơ (chỉ validate khi tạo mới)
        if (!$excludeId) {
            if (empty($data['ma_ho_so_cs'])) {
                $errors['ma_ho_so_cs'] = 'Mã hồ sơ không được để trống.';
            } elseif ($this->hoSoChamSocModel->maHoSoExists($data['ma_ho_so_cs'])) {
                $errors['ma_ho_so_cs'] = 'Mã hồ sơ đã tồn tại.';
            }
        }
        
        if (empty($data['loai_hinh_cham_soc_cd_id']) || !is_numeric($data['loai_hinh_cham_soc_cd_id'])) {
            $errors['loai_hinh_cham_soc_cd_id'] = 'Vui lòng chọn Loại hình chăm sóc.';
        }
        
        if (!empty($data['nguoi_cham_soc_id']) && !is_numeric($data['nguoi_cham_soc_id'])) {
            $errors['nguoi_cham_soc_id'] = 'Người chăm sóc được chọn không hợp lệ.';
        }
        
        if (empty($data['ngay_de_nghi_cs'])) {
            $errors['ngay_de_nghi_cs'] = 'Ngày đề nghị không được để trống.';
        }

        if (empty($data['noi_dung_de_nghi'])) {
            $errors['noi_dung_de_nghi'] = 'Nội dung đề nghị không được để trống.';
        }
        return $errors;
    }

    public function show($id) {
        if (!is_numeric($id) || $id <=0) {
            $this->setFlashMessage('error', 'ID hồ sơ không hợp lệ.');
            $this->redirect('ho-so-cham-soc-cong-dong/index');
            return;
        }

        $hoSo = $this->hoSoChamSocModel->findById($id);

        if (!$hoSo) {
            $this->setFlashMessage('error', 'Không tìm thấy hồ sơ chăm sóc cộng đồng.');
            $this->redirect('ho-so-cham-soc-cong-dong/index');
            return;
        }
        $trangThaiOptions = $this->hoSoChamSocModel->getTrangThaiOptions();
        $nguoiChamSocTen = $this->getTenNguoiChamSoc($hoSo['nguoi_cham_soc_id'] ?? null);

        $this->view('ho_so_cham_soc_cong_dong/show', [
            'title' => 'Chi tiết Hồ sơ Chăm sóc CĐ: ' . htmlspecialchars($hoSo['ma_ho_so_cs']),
            'hoSo' => $hoSo,
            'trangThaiOptions' => $trangThaiOptions,
            'nguoiChamSocTen' => $nguoiChamSocTen,
        ]);
        
    }

    public function destroy($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Yêu cầu không hợp lệ.');
            $this->redirect('ho-so-cham-soc-cong-dong/index');
            return;
        }
         if (!is_numeric($id) || $id <=0) {
            $this->setFlashMessage('error', 'ID hồ sơ không hợp lệ.');
            $this->redirect('ho-so-cham-soc-cong-dong/index');
            return;
        }
        
        // <<< SỬA ĐỔI: Tìm hồ sơ trước để lấy thông tin cần thiết >>>
        $hoSo = $this->hoSoChamSocModel->findById($id);
        if (!$hoSo) {
            $this->setFlashMessage('error', 'Không tìm thấy hồ sơ để xóa.');
            $this->redirect('ho-so-cham-soc-cong-dong/index');
            return;
        }

        // <<< SỬA ĐỔI: Lưu lại ID đối tượng trước khi xóa >>>
        $doi_tuong_id = $hoSo['doi_tuong_id'];

        $result = $this->hoSoChamSocModel->delete($id);

        if ($result) {
            $this->setFlashMessage('success', 'Xóa hồ sơ chăm sóc cộng đồng thành công!');
        } else {
            $this -> setFlashMessage('error', 'Xóa hồ sơ chăm sóc cộng đồng thất bại.');
        }
        
        // <<< SỬA ĐỔI: Xây dựng URL chuyển hướng có điều kiện >>>
        $redirectUrl = 'ho-so-cham-soc-cong-dong/index';
        if (!empty($doi_tuong_id)) {
            $redirectUrl .= '?doi_tuong_id=' . $doi_tuong_id;
        }
        $this->redirect($redirectUrl);
    }

    /**
     * Xử lý yêu cầu xuất danh sách hồ sơ chăm sóc cộng đồng ra file CSV.
     */
    public function exportCsv() {
        // Kiểm tra quyền truy cập
        $this->checkAuth();

        // 1. Thiết lập HTTP headers để trình duyệt tải về file CSV
        $filename = 'DanhSach_HoSoChamSocCD_' . date('d-m-Y') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // 2. Mở output stream của PHP để ghi dữ liệu
        $output = fopen('php://output', 'w');
        
        // Thêm BOM (Byte Order Mark) để Excel mở tiếng Việt có dấu đúng
        fputs($output, "\xEF\xBB\xBF");

        // 3. Ghi dòng tiêu đề vào file CSV
        $headers = [
            'STT', 'Mã Hồ sơ', 'Tên Đối tượng', 'Mã Đối tượng', 'Loại hình chăm sóc', 
            'Người chăm sóc', 'Nội dung đề nghị', 'Hình thức cụ thể', 'Kinh phí dự kiến (VNĐ)',
            'Nguồn kinh phí', 'Ngày đề nghị', 'Ngày bắt đầu', 'Ngày kết thúc dự kiến',
            'Trạng thái', 'Người lập', 'Người duyệt', 'Ngày duyệt', 'Lý do thay đổi', 'Ghi chú'
        ];
        fputcsv($output, $headers);

        // 4. Lấy các bộ lọc từ URL
        $filters = [
            'searchTerm' => isset($_GET['search']) ? trim($_GET['search']) : '',
            'doi_tuong_id' => isset($_GET['doi_tuong_id']) && is_numeric($_GET['doi_tuong_id']) ? (int)$_GET['doi_tuong_id'] : null,
            'trang_thai' => isset($_GET['trang_thai']) ? trim($_GET['trang_thai']) : ''
        ];

        // 5. Lấy toàn bộ dữ liệu đã lọc từ Model
        $hoSoList = $this->hoSoChamSocModel->getAllForExport($filters);
        
        // Lấy danh sách trạng thái để dịch
        $trangThaiOptions = $this->hoSoChamSocModel->getTrangThaiOptions();

        // 6. Lặp qua dữ liệu và ghi từng dòng vào file CSV
        foreach ($hoSoList as $index => $hs) {
            $rowData = [
                $index + 1,
                $hs['ma_ho_so_cs'] ?? '',
                $hs['ten_doi_tuong'] ?? '',
                $hs['ma_doi_tuong'] ?? '',
                $hs['ten_loai_hinh_cham_soc'] ?? '',
                $hs['ten_nguoi_cham_soc'] ?? '',
                $hs['noi_dung_de_nghi'] ?? '',
                $hs['hinh_thuc_cham_soc_cu_the'] ?? '',
                $hs['kinh_phi_du_kien'] ?? 0,
                $hs['nguon_kinh_phi'] ?? '',
                !empty($hs['ngay_de_nghi_cs']) ? date('d/m/Y', strtotime($hs['ngay_de_nghi_cs'])) : '',
                !empty($hs['ngay_bat_dau_cham_soc']) ? date('d/m/Y', strtotime($hs['ngay_bat_dau_cham_soc'])) : '',
                !empty($hs['ngay_ket_thuc_du_kien_cs']) ? date('d/m/Y', strtotime($hs['ngay_ket_thuc_du_kien_cs'])) : '',
                $trangThaiOptions[$hs['trang_thai_hs_cs']] ?? $hs['trang_thai_hs_cs'],
                $hs['ten_nguoi_lap'] ?? '',
                $hs['ten_nguoi_xet_duyet'] ?? '',
                !empty($hs['ngay_xet_duyet_hs_cs']) ? date('d/m/Y', strtotime($hs['ngay_xet_duyet_hs_cs'])) : '',
                $hs['ly_do_thay_doi_trang_thai_cs'] ?? '',
                $hs['ghi_chu_hs_cs'] ?? '',
            ];
            
            fputcsv($output, $rowData);
        }

        // 7. Đóng stream và dừng script
        fclose($output);
        exit();
    }
    protected function getTenNguoiChamSoc(?int $nguoiChamSocId): string {
        if (empty($nguoiChamSocId)) {
            return 'Không có';
        }

        // Gọi model để lấy chi tiết người chăm sóc từ ID
        $ncs = $this->nguoiChamSocModel->findById($nguoiChamSocId);

        if (!$ncs) {
            return 'Không có';
        }

        $ten = htmlspecialchars($ncs['ho_ten'], ENT_QUOTES, 'UTF-8');

        if (!empty($ncs['cccd'])) {
            $ten .= ' (' . htmlspecialchars($ncs['cccd'], ENT_QUOTES, 'UTF-8') . ')';
        }

        return $ten;
    }

}