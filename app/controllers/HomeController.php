<?php
// app/controllers/HomeController.php

class HomeController extends BaseController {

    public function index() {
        $this->checkAuth();

        // Khởi tạo các models
        $doiTuongModel = new DoiTuong($this->db);
        $hoSoTroCapModel = new HoSoTroCap($this->db);
        $hoTroKhanCapModel = new HoTroKhanCap($this->db);
        $hoSoChamSocModel = new HoSoChamSocCongDong($this->db);

        // --- Thống kê Đối tượng (Phần này đã đúng) ---
        $trangThaiHoSoDtOptions = $doiTuongModel->getTrangThaiHoSoDtOptions();
        $dtCountsByTrangThai = [];
        foreach (array_keys($trangThaiHoSoDtOptions) as $statusKey) {
            $dtCountsByTrangThai[$statusKey] = $doiTuongModel->countByTrangThaiHoSoDt($statusKey);
        }

        $statsDoiTuong = [
            'total' => $doiTuongModel->countAll(),
            'trang_thai_ho_so_dt_counts' => $dtCountsByTrangThai,
            'trang_thai_ho_so_dt_labels' => $trangThaiHoSoDtOptions,
            'count_by_loai' => $doiTuongModel->countByLoaiDoiTuong(),
        ];

        // --- Thống kê Hồ sơ Trợ cấp Hàng tháng (Cập nhật) ---
        $hstcTrangThaiOptions = $hoSoTroCapModel->getTrangThaiOptions();
        $hstcCountsByStatus = [];
        if (is_array($hstcTrangThaiOptions)) {
            // <<< SỬA LỖI Ở ĐÂY: Dùng đúng biến $statusKey
            foreach ($hstcTrangThaiOptions as $statusKey => $statusLabel) {
                // Sửa từ chuỗi cứng thành biến động
                $hstcCountsByStatus[$statusKey] = $hoSoTroCapModel->countByTrangThai($statusKey);
            }
        }
        $statsHoSoTroCap = [
            'total' => $hoSoTroCapModel->countAll(),
            'doi_tuong_dang_huong_count' => $hoSoTroCapModel->countDistinctDoiTuongByTrangThai('da_phe_duyet_dang_huong'),
            'total_muc_tro_cap_dang_huong' => $hoSoTroCapModel->sumMucTroCapByTrangThai('da_phe_duyet_dang_huong'),
            'counts_by_status' => $hstcCountsByStatus,
            'trang_thai_options' => $hstcTrangThaiOptions
        ];
        
        // --- Thống kê Hỗ trợ Khẩn cấp (Cập nhật) ---
        $htkcTrangThaiOptions = $hoTroKhanCapModel->getTrangThaiOptions();
        $htkcCountsByStatus = [];
        if (is_array($htkcTrangThaiOptions)) {
            // <<< SỬA LỖI Ở ĐÂY: Dùng đúng biến $statusKey
            foreach ($htkcTrangThaiOptions as $statusKey => $statusLabel) {
                 // Sửa từ chuỗi cứng thành biến động
                $htkcCountsByStatus[$statusKey] = $hoTroKhanCapModel->countByTrangThai($statusKey);
            }
        }
        $statsHoTroKhanCap = [
            'total' => $hoTroKhanCapModel->countAll(),
            'total_gia_tri_da_ho_tro_tien_mat' => $hoTroKhanCapModel->sumGiaTriTienMatByTrangThai('da_ho_tro'),
            'counts_by_status' => $htkcCountsByStatus,
            'trang_thai_options' => $htkcTrangThaiOptions
        ];

        // --- Thống kê Hồ sơ Chăm sóc Cộng đồng (Cập nhật) ---
        $hsccTrangThaiOptions = $hoSoChamSocModel->getTrangThaiOptions();
        $hsccCountsByStatus = [];
        if (is_array($hsccTrangThaiOptions)) {
            // <<< SỬA LỖI Ở ĐÂY: Dùng đúng biến $statusKey
            foreach ($hsccTrangThaiOptions as $statusKey => $statusLabel) {
                 // Sửa từ chuỗi cứng thành biến động
                $hsccCountsByStatus[$statusKey] = $hoSoChamSocModel->countByTrangThai($statusKey);
            }
        }
        $statsHoSoChamSoc = [
            'total' => $hoSoChamSocModel->countAll(),
            'doi_tuong_dang_cham_soc_count' => $hoSoChamSocModel->countDistinctDoiTuongByTrangThai('da_phe_duyet'), 
            'total_kinh_phi_du_kien_da_phe_duyet' => $hoSoChamSocModel->sumKinhPhiDuKienByTrangThai('da_phe_duyet'),
            'counts_by_status' => $hsccCountsByStatus,
            'trang_thai_options' => $hsccTrangThaiOptions
        ];

        $data = [
            'title' => 'TRANG CHỦ - THỐNG KÊ TỔNG QUAN',
            'currentUser' => $this->getCurrentUser(),
            'statsDoiTuong' => $statsDoiTuong,
            'statsHoSoTroCap' => $statsHoSoTroCap,
            'statsHoTroKhanCap' => $statsHoTroKhanCap,
            'statsHoSoChamSoc' => $statsHoSoChamSoc,
        ];

        $this->view('home/index', $data);
    }
}