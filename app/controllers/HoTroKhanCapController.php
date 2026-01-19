<?php
// app/controllers/HoTroKhanCapController.php

class HoTroKhanCapController extends BaseController {

    private $hoTroKhanCapModel;
    private $doiTuongModel;
    private $loaiHinhKCModel; // Thêm model Loại Hình HTKC

    public function __construct() {
        parent::__construct();
        $this->checkAuth();

        $this->hoTroKhanCapModel = new HoTroKhanCap($this->db);
        $this->doiTuongModel = new DoiTuong($this->db);
        $this->loaiHinhKCModel = new LoaiHinhHoTroKhanCap($this->db); // Khởi tạo
    }

    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $filters = [
            'searchTerm' => isset($_GET['search']) ? trim($_GET['search']) : '',
            'doi_tuong_id' => isset($_GET['doi_tuong_id']) && is_numeric($_GET['doi_tuong_id']) ? (int)$_GET['doi_tuong_id'] : null,
            'trang_thai' => isset($_GET['trang_thai']) ? trim($_GET['trang_thai']) : '' // Sẽ dùng cho `trang_thai_hs_kc`
        ];

        $result = $this->hoTroKhanCapModel->getAll($filters, $limit, $offset);
        $hoTroList = $result['data'];
        $totalRecords = $result['total'];
        $totalPages = ceil($totalRecords / $limit);

        $pagination = [
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'limit' => $limit,
            'totalRecords' => $totalRecords,
            'filters' => $filters
        ];

        $trangThaiOptions = $this->hoTroKhanCapModel->getTrangThaiOptions();
        $doiTuongContext = null;
        if ($filters['doi_tuong_id']) {
            $doiTuongContext = $this->doiTuongModel->findById($filters['doi_tuong_id']);
        }

        $this->view('ho_tro_khan_cap/index', [
            'title' => 'Danh sách Hỗ trợ Khẩn cấp' . ($doiTuongContext ? ' cho ' . htmlspecialchars($doiTuongContext['ho_ten']) : ''),
            'hoTroList' => $hoTroList,
            'pagination' => $pagination,
            'trangThaiOptions' => $trangThaiOptions,
            'currentFilters' => $filters,
            'doiTuongContext' => $doiTuongContext
        ]);
    }

    public function create($doi_tuong_id = null) {
        $doiTuong = null;
        if (is_numeric($doi_tuong_id) && $doi_tuong_id > 0) {
            $doiTuong = $this->doiTuongModel->findById($doi_tuong_id);
            if (!$doiTuong) {
                $this->setFlashMessage('warning', 'Không tìm thấy đối tượng với ID ' . htmlspecialchars($doi_tuong_id) . '. Bạn có thể tạo hỗ trợ khẩn cấp không liên kết đối tượng.');
                $doi_tuong_id = null; 
            }
        }

        $trangThaiOptions = $this->hoTroKhanCapModel->getTrangThaiOptions();
        $loaiHinhKCOptions = $this->loaiHinhKCModel->getAllLoaiHinhHoTroKCForSelect(); // Lấy các loại hình

        if (empty($loaiHinhKCOptions)) {
            $this->setFlashMessage('warning', 'Chưa có Loại hình hỗ trợ khẩn cấp nào được định nghĩa. Vui lòng thêm trước.');
            $currentUser = $this->getCurrentUser();
             if ($currentUser && $currentUser['role'] === 'admin') {
                 $this->redirect('loai-hinh-ho-tro-khan-cap/index');
                 return;
            }
        }
        // $nguoiXuLyOptions = ... (Nếu cần)

        $this->view('ho_tro_khan_cap/create', [
            'title' => 'Tạo Hỗ trợ Khẩn cấp' . ($doiTuong ? ' cho ' . htmlspecialchars($doiTuong['ho_ten']) : ''),
            'doiTuong' => $doiTuong, 
            'trangThaiOptions' => $trangThaiOptions,
            'loaiHinhKCOptions' => $loaiHinhKCOptions, // Truyền cho view
            // 'nguoiXuLyOptions' => $nguoiXuLyOptions,
            'defaultTrangThai' => 'cho_xem_xet',
            'oldData' => $_SESSION['old_form_data']['ho_tro_khan_cap_create'] ?? [],
            'errors' => $_SESSION['form_errors']['ho_tro_khan_cap_create'] ?? [],
            'action' => 'create'
        ]);
        unset($_SESSION['old_form_data']['ho_tro_khan_cap_create']);
        unset($_SESSION['form_errors']['ho_tro_khan_cap_create']);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('ho-tro-khan-cap/index');
            return;
        }

        $data = $_POST;
        $doi_tuong_id_from_form = $data['doi_tuong_id'] ?? null;
        $currentUser = $this->getCurrentUser();
        $data['nguoi_lap_hs_kc_id'] = $currentUser['id'] ?? null;

        $errors = [];

        if (empty($data['ma_ho_so_kc'])) {
            // Có thể để trống mã hồ sơ KC và sinh tự động nếu muốn
            // $errors['ma_ho_so_kc'] = 'Mã hồ sơ khẩn cấp không được để trống.';
        } elseif ($this->hoTroKhanCapModel->maHoSoKCExists($data['ma_ho_so_kc'])) {
            $errors['ma_ho_so_kc'] = 'Mã hồ sơ khẩn cấp đã tồn tại.';
        }

        if (empty($data['ho_ten_nguoi_nhan'])) {
            $errors['ho_ten_nguoi_nhan'] = 'Họ tên người nhận không được để trống.';
        }
        if (empty($data['ly_do_ho_tro'])) {
            $errors['ly_do_ho_tro'] = 'Lý do hỗ trợ không được để trống.';
        }

        // Validation cho loai_hinh_ho_tro_kc_id
        if (empty($data['loai_hinh_ho_tro_kc_id']) || !is_numeric($data['loai_hinh_ho_tro_kc_id'])) {
            $errors['loai_hinh_ho_tro_kc_id'] = 'Vui lòng chọn Loại hình hỗ trợ khẩn cấp.';
        } else {
            $selectedLoaiHinhKC = $this->loaiHinhKCModel->findById($data['loai_hinh_ho_tro_kc_id']);
            if (!$selectedLoaiHinhKC || $selectedLoaiHinhKC['trang_thai_ap_dung'] !== 'dang_ap_dung') {
                $errors['loai_hinh_ho_tro_kc_id'] = 'Loại hình hỗ trợ đã chọn không hợp lệ hoặc không còn áp dụng.';
            } else {
                // Tự động gán giá trị/hiện vật nếu người dùng không nhập
                if (empty($data['gia_tri_ho_tro_tien_mat']) && !empty($selectedLoaiHinhKC['gia_tri_ho_tro_dinh_muc'])) {
                    $data['gia_tri_ho_tro_tien_mat'] = $selectedLoaiHinhKC['gia_tri_ho_tro_dinh_muc'];
                }
                if (empty($data['mo_ta_hien_vat_ho_tro']) && !empty($selectedLoaiHinhKC['mo_ta_hien_vat_dinh_muc'])) {
                    $data['mo_ta_hien_vat_ho_tro'] = $selectedLoaiHinhKC['mo_ta_hien_vat_dinh_muc'];
                }
            }
        }
        if (isset($data['gia_tri_ho_tro_tien_mat']) && $data['gia_tri_ho_tro_tien_mat'] !== '' && !is_numeric($data['gia_tri_ho_tro_tien_mat'])) {
            $errors['gia_tri_ho_tro_tien_mat'] = 'Giá trị hỗ trợ (tiền mặt) phải là số.';
        }

        $trangThaiOptions = $this->hoTroKhanCapModel->getTrangThaiOptions();
         if (empty($data['trang_thai_hs_kc']) || !array_key_exists($data['trang_thai_hs_kc'], $trangThaiOptions)) {
            $errors['trang_thai_hs_kc'] = 'Trạng thái không hợp lệ.';
        } else {
            if ($data['trang_thai_hs_kc'] === 'da_ho_tro') {
                 if (empty($data['nguoi_xu_ly_hs_kc_id']) && empty($data['ngay_xu_ly_ht'])) {
                    $data['nguoi_xu_ly_hs_kc_id'] = $currentUser['id'] ?? null;
                    $data['ngay_xu_ly_ht'] = date('Y-m-d');
                } elseif (empty($data['nguoi_xu_ly_hs_kc_id'])) {
                     $errors['nguoi_xu_ly_hs_kc_id'] = 'Cần người xử lý khi đã hỗ trợ.';
                } elseif (empty($data['ngay_xu_ly_ht'])) {
                     $errors['ngay_xu_ly_ht'] = 'Cần ngày xử lý khi đã hỗ trợ.';
                }
            }
            if (in_array($data['trang_thai_hs_kc'], ['khong_du_dieu_kien', 'huy_bo']) && empty($data['ly_do_tu_choi_huy_bo_kc'])) {
                $errors['ly_do_tu_choi_huy_bo_kc'] = 'Cần nhập lý do khi chọn trạng thái "' . $trangThaiOptions[$data['trang_thai_hs_kc']] . '".';
            }
        }


        if (!empty($errors)) {
            $this->setFlashMessage('error', 'Tạo hỗ trợ khẩn cấp thất bại. Vui lòng kiểm tra lại thông tin.');
            $_SESSION['old_form_data']['ho_tro_khan_cap_create'] = $data;
            $_SESSION['form_errors']['ho_tro_khan_cap_create'] = $errors;
            $redirect_doi_tuong_id = !empty($doi_tuong_id_from_form) ? '/' . $doi_tuong_id_from_form : '';
            $this->redirect('ho-tro-khan-cap/create' . $redirect_doi_tuong_id);
            return;
        }
        
        $result = $this->hoTroKhanCapModel->create($data);

        if ($result) {
            $this->setFlashMessage('success', 'Tạo hỗ trợ khẩn cấp thành công!');
            $redirect_param = !empty($doi_tuong_id_from_form) ? '?doi_tuong_id=' . $doi_tuong_id_from_form : '';
            $this->redirect('ho-tro-khan-cap/index' . $redirect_param);
        } else {
            $this->setFlashMessage('error', 'Tạo hỗ trợ khẩn cấp thất bại.');
            $_SESSION['old_form_data']['ho_tro_khan_cap_create'] = $data;
            $redirect_doi_tuong_id = !empty($doi_tuong_id_from_form) ? '/' . $doi_tuong_id_from_form : '';
            $this->redirect('ho-tro-khan-cap/create' . $redirect_doi_tuong_id);
        }
    }

    public function edit($id) {
        if (!is_numeric($id) || $id <= 0) {
            $this->setFlashMessage('error', 'ID hỗ trợ không hợp lệ.');
            $this->redirect('ho-tro-khan-cap/index');
            return;
        }

        $hoTro = $this->hoTroKhanCapModel->findById($id);

        if (!$hoTro) {
            $this->setFlashMessage('error', 'Không tìm thấy hỗ trợ khẩn cấp.');
            $this->redirect('ho-tro-khan-cap/index');
            return;
        }

        $doiTuong = null;
        if (!empty($hoTro['doi_tuong_id'])) {
            $doiTuong = $this->doiTuongModel->findById($hoTro['doi_tuong_id']);
        }
        $trangThaiOptions = $this->hoTroKhanCapModel->getTrangThaiOptions();
        $loaiHinhKCOptions = $this->loaiHinhKCModel->getAllLoaiHinhHoTroKCForSelect();
        // Xử lý nếu loại hình hiện tại không còn "đang áp dụng"
        if ($hoTro && !empty($hoTro['loai_hinh_ho_tro_kc_id'])) {
            $isCurrentLHInOptions = false;
            foreach ($loaiHinhKCOptions as $option) {
                if ($option['id'] == $hoTro['loai_hinh_ho_tro_kc_id']) {
                    $isCurrentLHInOptions = true;
                    break;
                }
            }
            if (!$isCurrentLHInOptions) {
                $currentSelectedLH = $this->loaiHinhKCModel->findById($hoTro['loai_hinh_ho_tro_kc_id']);
                if ($currentSelectedLH) {
                    array_unshift($loaiHinhKCOptions, [
                        'id' => $currentSelectedLH['id'],
                        'ten_loai_hinh' => $currentSelectedLH['ten_loai_hinh'] . ($currentSelectedLH['trang_thai_ap_dung'] !== 'dang_ap_dung' ? ' (Ngưng áp dụng)' : ''),
                        'gia_tri_ho_tro_dinh_muc' => $currentSelectedLH['gia_tri_ho_tro_dinh_muc'],
                        'mo_ta_hien_vat_dinh_muc' => $currentSelectedLH['mo_ta_hien_vat_dinh_muc']
                    ]);
                }
            }
        }
        // $nguoiXuLyOptions = ...
        
        $this->view('ho_tro_khan_cap/edit', [
            'title' => 'Chỉnh sửa Hỗ trợ Khẩn cấp: ' . ($hoTro['ma_ho_so_kc'] ? htmlspecialchars($hoTro['ma_ho_so_kc']) : 'ID ' . $hoTro['id']),
            'hoTro' => $hoTro,
            'doiTuong' => $doiTuong,
            'trangThaiOptions' => $trangThaiOptions,
            'loaiHinhKCOptions' => $loaiHinhKCOptions,
            // 'nguoiXuLyOptions' => $nguoiXuLyOptions,
            'oldData' => $_SESSION['old_form_data']['ho_tro_khan_cap_edit_'.$id] ?? $hoTro,
            'errors' => $_SESSION['form_errors']['ho_tro_khan_cap_edit_'.$id] ?? []
        ]);
        unset($_SESSION['old_form_data']['ho_tro_khan_cap_edit_'.$id]);
        unset($_SESSION['form_errors']['ho_tro_khan_cap_edit_'.$id]);
    }

    public function update($id) {
         if (!is_numeric($id) || $id <= 0) {
            $this->setFlashMessage('error', 'ID hỗ trợ không hợp lệ.');
            $this->redirect('ho-tro-khan-cap/index');
            return;
        }
        $hoTroGoc = $this->hoTroKhanCapModel->findById($id);
        if (!$hoTroGoc) {
            $this->setFlashMessage('error', 'Không tìm thấy hỗ trợ để cập nhật.');
            $this->redirect('ho-tro-khan-cap/index');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('ho-tro-khan-cap/edit/' . $id);
            return;
        }

        $data = $_POST;
        $currentUser = $this->getCurrentUser();
        // $data['nguoi_lap_hs_kc_id'] = $hoTroGoc['nguoi_lap_hs_kc_id'];

        $errors = [];

        if (isset($data['ma_ho_so_kc']) && $data['ma_ho_so_kc'] !== $hoTroGoc['ma_ho_so_kc'] && $this->hoTroKhanCapModel->maHoSoKCExists($data['ma_ho_so_kc'], $id)) {
            $errors['ma_ho_so_kc'] = 'Mã hồ sơ khẩn cấp đã tồn tại.';
        }

        if (empty($data['ho_ten_nguoi_nhan'])) {
            $errors['ho_ten_nguoi_nhan'] = 'Họ tên người nhận không được để trống.';
        }
        if (empty($data['ly_do_ho_tro'])) {
            $errors['ly_do_ho_tro'] = 'Lý do hỗ trợ không được để trống.';
        }

        // Validation cho loai_hinh_ho_tro_kc_id
        if (empty($data['loai_hinh_ho_tro_kc_id']) || !is_numeric($data['loai_hinh_ho_tro_kc_id'])) {
            $errors['loai_hinh_ho_tro_kc_id'] = 'Vui lòng chọn Loại hình hỗ trợ khẩn cấp.';
        } else {
            $selectedLoaiHinhKC = $this->loaiHinhKCModel->findById($data['loai_hinh_ho_tro_kc_id']);
            if (!$selectedLoaiHinhKC) {
                $errors['loai_hinh_ho_tro_kc_id'] = 'Loại hình hỗ trợ đã chọn không hợp lệ.';
            } else {
                // Nếu Loại Hình thay đổi, cập nhật giá trị/hiện vật nếu người dùng chưa tự nhập
                if ($data['loai_hinh_ho_tro_kc_id'] != $hoTroGoc['loai_hinh_ho_tro_kc_id']) {
                    if (empty($data['gia_tri_ho_tro_tien_mat']) && !empty($selectedLoaiHinhKC['gia_tri_ho_tro_dinh_muc'])) {
                        $data['gia_tri_ho_tro_tien_mat'] = $selectedLoaiHinhKC['gia_tri_ho_tro_dinh_muc'];
                    }
                    if (empty($data['mo_ta_hien_vat_ho_tro']) && !empty($selectedLoaiHinhKC['mo_ta_hien_vat_dinh_muc'])) {
                        $data['mo_ta_hien_vat_ho_tro'] = $selectedLoaiHinhKC['mo_ta_hien_vat_dinh_muc'];
                    }
                }
            }
        }
        if (isset($data['gia_tri_ho_tro_tien_mat']) && $data['gia_tri_ho_tro_tien_mat'] !== '' && !is_numeric($data['gia_tri_ho_tro_tien_mat'])) {
            $errors['gia_tri_ho_tro_tien_mat'] = 'Giá trị hỗ trợ (tiền mặt) phải là số.';
        }

        $trangThaiOptions = $this->hoTroKhanCapModel->getTrangThaiOptions();
        if (empty($data['trang_thai_hs_kc']) || !array_key_exists($data['trang_thai_hs_kc'], $trangThaiOptions)) {
            $errors['trang_thai_hs_kc'] = 'Trạng thái không hợp lệ.';
        } else {
            if ($data['trang_thai_hs_kc'] === 'da_ho_tro') {
                 if (empty($data['nguoi_xu_ly_hs_kc_id'])) $data['nguoi_xu_ly_hs_kc_id'] = $currentUser['id'] ?? null;
                 if (empty($data['ngay_xu_ly_ht'])) $data['ngay_xu_ly_ht'] = date('Y-m-d');

                 if(empty($data['nguoi_xu_ly_hs_kc_id'])) $errors['nguoi_xu_ly_hs_kc_id'] = 'Cần người xử lý khi đã hỗ trợ.';
                 if(empty($data['ngay_xu_ly_ht'])) $errors['ngay_xu_ly_ht'] = 'Cần ngày xử lý khi đã hỗ trợ.';
            }
            if (in_array($data['trang_thai_hs_kc'], ['khong_du_dieu_kien', 'huy_bo']) && empty($data['ly_do_tu_choi_huy_bo_kc'])) {
                $errors['ly_do_tu_choi_huy_bo_kc'] = 'Cần nhập lý do khi chọn trạng thái "' . $trangThaiOptions[$data['trang_thai_hs_kc']] . '".';
            }
        }

        if (!empty($errors)) {
            $this->setFlashMessage('error', 'Cập nhật hỗ trợ khẩn cấp thất bại. Vui lòng kiểm tra lại thông tin.');
            $_SESSION['old_form_data']['ho_tro_khan_cap_edit_'.$id] = $data;
            $_SESSION['form_errors']['ho_tro_khan_cap_edit_'.$id] = $errors;
            $this->redirect('ho-tro-khan-cap/edit/' . $id);
            return;
        }
        
        $result = $this->hoTroKhanCapModel->update($id, $data);

        if ($result) {
            $this->setFlashMessage('success', 'Cập nhật hỗ trợ khẩn cấp thành công!');
            $this->redirect('ho-tro-khan-cap/index/' . $id);
        } else {
            $this->setFlashMessage('error', 'Cập nhật hỗ trợ khẩn cấp thất bại.');
            $_SESSION['old_form_data']['ho_tro_khan_cap_edit_'.$id] = $data;
            $this->redirect('ho-tro-khan-cap/edit/' . $id);
        }
    }

    public function show($id) {
        if (!is_numeric($id) || $id <=0) {
            $this->setFlashMessage('error', 'ID hỗ trợ không hợp lệ.');
            $this->redirect('ho-tro-khan-cap/index');
            return;
        }

        $hoTro = $this->hoTroKhanCapModel->findById($id);

        if (!$hoTro) {
            $this->setFlashMessage('error', 'Không tìm thấy hỗ trợ khẩn cấp.');
            $this->redirect('ho-tro-khan-cap/index');
            return;
        }
        $trangThaiOptions = $this->hoTroKhanCapModel->getTrangThaiOptions();

        $this->view('ho_tro_khan_cap/show', [
            'title' => 'Chi tiết Hỗ trợ Khẩn cấp: ' . ($hoTro['ma_ho_so_kc'] ? htmlspecialchars($hoTro['ma_ho_so_kc']) : 'ID ' . $hoTro['id']),
            'hoTro' => $hoTro,
            'trangThaiOptions' => $trangThaiOptions
        ]);
    }

    public function destroy($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Yêu cầu không hợp lệ.');
            $this->redirect('ho-tro-khan-cap/index');
            return;
        }
        if (!is_numeric($id) || $id <=0) {
            $this->setFlashMessage('error', 'ID hỗ trợ không hợp lệ.');
            $this->redirect('ho-tro-khan-cap/index');
            return;
        }
        $hoTro = $this->hoTroKhanCapModel->findById($id); 
        
        $result = $this->hoTroKhanCapModel->delete($id);

        if ($result) {
            $this->setFlashMessage('success', 'Xóa hỗ trợ khẩn cấp thành công!');
        } else {
            $this->setFlashMessage('error', 'Xóa hỗ trợ khẩn cấp thất bại.');
        }
        $redirect_param = ($hoTro && !empty($hoTro['doi_tuong_id'])) ? '?doi_tuong_id=' . $hoTro['doi_tuong_id'] : '';
        $this->redirect('ho-tro-khan-cap/index' . $redirect_param);
    }

     /**
     * Xử lý yêu cầu xuất danh sách hỗ trợ khẩn cấp ra file CSV.
     */
    // public function exportCsv() {
    //     // Kiểm tra quyền truy cập
    //     $this->checkAuth();

    //     // 1. Thiết lập HTTP headers để trình duyệt tải về file CSV
    //     $filename = 'DanhSach_HoTroKhanCap_' . date('d-m-Y') . '.csv';
    //     header('Content-Type: text/csv; charset=utf-8');
    //     header('Content-Disposition: attachment; filename="' . $filename . '"');

    //     // 2. Mở output stream của PHP để ghi dữ liệu
    //     $output = fopen('php://output', 'w');
        
    //     // Thêm BOM (Byte Order Mark) để Excel mở tiếng Việt có dấu đúng
    //     fputs($output, "\xEF\xBB\xBF");

    //     // 3. Ghi dòng tiêu đề vào file CSV
    //     $headers = [
    //         'STT', 'Mã Hồ sơ KC', 'Họ tên người nhận', 'CCCD người nhận', 'SĐT người nhận',
    //         'Địa chỉ người nhận', 'Đối tượng liên quan (Tên)', 'Đối tượng liên quan (Mã)',
    //         'Loại hình hỗ trợ', 'Lý do hỗ trợ', 'Hình thức hỗ trợ cụ thể', 
    //         'Giá trị tiền mặt (VNĐ)', 'Mô tả hiện vật', 'Ngày đề xuất', 'Ngày xử lý',
    //         'Người lập', 'Người xử lý', 'Trạng thái', 'Lý do từ chối/hủy', 'Ghi chú'
    //     ];
    //     fputcsv($output, $headers);

    //     // 4. Lấy các bộ lọc từ URL
    //     $filters = [
    //         'searchTerm' => isset($_GET['search']) ? trim($_GET['search']) : '',
    //         'doi_tuong_id' => isset($_GET['doi_tuong_id']) && is_numeric($_GET['doi_tuong_id']) ? (int)$_GET['doi_tuong_id'] : null,
    //         'trang_thai' => isset($_GET['trang_thai']) ? trim($_GET['trang_thai']) : ''
    //     ];

    //     // 5. Lấy toàn bộ dữ liệu đã lọc từ Model
    //     $hoTroList = $this->hoTroKhanCapModel->getAllForExport($filters);
        
    //     // Lấy danh sách trạng thái để dịch
    //     $trangThaiOptions = $this->hoTroKhanCapModel->getTrangThaiOptions();

    //     // 6. Lặp qua dữ liệu và ghi từng dòng vào file CSV
    //     foreach ($hoTroList as $index => $ht) {
    //         $rowData = [
    //             $index + 1,
    //             $ht['ma_ho_so_kc'] ?? '',
    //             $ht['ho_ten_nguoi_nhan'] ?? '',
    //             // Thêm ' trước số CCCD để Excel không hiểu sai thành số khoa học
    //             isset($ht['cccd_nguoi_nhan']) ? "'" . $ht['cccd_nguoi_nhan'] : '', 
    //             isset($ht['sdt_nguoi_nhan']) ? "'" . $ht['sdt_nguoi_nhan'] : '',
    //             $ht['dia_chi_nguoi_nhan'] ?? '',
    //             $ht['ten_doi_tuong_lien_quan'] ?? '',
    //             $ht['ma_doi_tuong'] ?? '',
    //             $ht['ten_loai_hinh_ho_tro'] ?? '',
    //             $ht['ly_do_ho_tro'] ?? '',
    //             $ht['hinh_thuc_ho_tro_cu_the'] ?? '',
    //             $ht['gia_tri_ho_tro_tien_mat'] ?? 0,
    //             $ht['mo_ta_hien_vat_ho_tro'] ?? '',
    //             !empty($ht['ngay_de_xuat_ht']) ? date('d/m/Y', strtotime($ht['ngay_de_xuat_ht'])) : '',
    //             !empty($ht['ngay_xu_ly_ht']) ? date('d/m/Y', strtotime($ht['ngay_xu_ly_ht'])) : '',
    //             $ht['ten_nguoi_lap'] ?? '',
    //             $ht['ten_nguoi_xu_ly'] ?? '',
    //             $trangThaiOptions[$ht['trang_thai_hs_kc']] ?? $ht['trang_thai_hs_kc'],
    //             $ht['ly_do_tu_choi_huy_bo_kc'] ?? '',
    //             $ht['ghi_chu_hs_kc'] ?? '',
    //         ];
            
    //         fputcsv($output, $rowData);
    //     }

    //     // 7. Đóng stream và dừng script
    //     fclose($output);
    //     exit();
    // }
    public function exportExcel()
    {
        // Kiểm tra quyền truy cập
        $this->checkAuth();

        // Lấy bộ lọc từ URL
        $filters = [
            'searchTerm' => isset($_GET['search']) ? trim($_GET['search']) : '',
            'doi_tuong_id' => isset($_GET['doi_tuong_id']) && is_numeric($_GET['doi_tuong_id']) ? (int)$_GET['doi_tuong_id'] : null,
            'trang_thai' => isset($_GET['trang_thai']) ? trim($_GET['trang_thai']) : ''
        ];

        // Lấy dữ liệu từ model
        $hoTroList = $this->hoTroKhanCapModel->getAllForExport($filters);
        $trangThaiOptions = $this->hoTroKhanCapModel->getTrangThaiOptions();

        // Tên file
        $filename = 'DanhSach_HoTroKhanCap_' . date('d-m-Y') . '.xls';

        // Headers cho trình duyệt tải về
        header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Cache-Control: max-age=0");

        // Thêm BOM hỗ trợ UTF-8 tiếng Việt
        echo "\xEF\xBB\xBF";

        // Mở bảng HTML
        echo '<table border="1">';
        echo '<thead><tr>';
        echo '<th>STT</th>';
        echo '<th>Mã Hồ sơ KC</th>';
        echo '<th>Họ tên người nhận</th>';
        echo '<th>CCCD người nhận</th>';
        echo '<th>SĐT người nhận</th>';
        echo '<th>Địa chỉ người nhận</th>';
        echo '<th>Đối tượng liên quan (Tên)</th>';
        echo '<th>Đối tượng liên quan (Mã)</th>';
        echo '<th>Loại hình hỗ trợ</th>';
        echo '<th>Lý do hỗ trợ</th>';
        echo '<th>Hình thức hỗ trợ cụ thể</th>';
        echo '<th>Giá trị tiền mặt (VNĐ)</th>';
        echo '<th>Mô tả hiện vật</th>';
        echo '<th>Ngày đề xuất</th>';
        echo '<th>Ngày xử lý</th>';
        echo '<th>Người lập</th>';
        echo '<th>Người xử lý</th>';
        echo '<th>Trạng thái</th>';
        echo '<th>Lý do từ chối/hủy</th>';
        echo '<th>Ghi chú</th>';
        echo '</tr></thead>';

        echo '<tbody>';
        foreach ($hoTroList as $index => $ht) {
            echo '<tr>';
            echo '<td>' . ($index + 1) . '</td>';
            echo '<td>' . htmlspecialchars($ht['ma_ho_so_kc'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($ht['ho_ten_nguoi_nhan'] ?? '') . '</td>';
            echo '<td>' . (isset($ht['cccd_nguoi_nhan']) ? "'" . $ht['cccd_nguoi_nhan'] : '') . '</td>';
            echo '<td>' . (isset($ht['sdt_nguoi_nhan']) ? "'" . $ht['sdt_nguoi_nhan'] : '') . '</td>';
            echo '<td>' . htmlspecialchars($ht['dia_chi_nguoi_nhan'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($ht['ten_doi_tuong_lien_quan'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($ht['ma_doi_tuong'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($ht['ten_loai_hinh_ho_tro'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($ht['ly_do_ho_tro'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($ht['hinh_thuc_ho_tro_cu_the'] ?? '') . '</td>';
            echo '<td>' . number_format($ht['gia_tri_ho_tro_tien_mat'] ?? 0) . '</td>';
            echo '<td>' . htmlspecialchars($ht['mo_ta_hien_vat_ho_tro'] ?? '') . '</td>';
            echo '<td>' . (!empty($ht['ngay_de_xuat_ht']) ? date('d/m/Y', strtotime($ht['ngay_de_xuat_ht'])) : '') . '</td>';
            echo '<td>' . (!empty($ht['ngay_xu_ly_ht']) ? date('d/m/Y', strtotime($ht['ngay_xu_ly_ht'])) : '') . '</td>';
            echo '<td>' . htmlspecialchars($ht['ten_nguoi_lap'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($ht['ten_nguoi_xu_ly'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($trangThaiOptions[$ht['trang_thai_hs_kc']] ?? $ht['trang_thai_hs_kc']) . '</td>';
            echo '<td>' . htmlspecialchars($ht['ly_do_tu_choi_huy_bo_kc'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($ht['ghi_chu_hs_kc'] ?? '') . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';

        exit();
    }


}
?>