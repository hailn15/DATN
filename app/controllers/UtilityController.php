<?php
// app/controllers/UtilityController.php

class UtilityController extends BaseController {

    public function __construct() {
        parent::__construct();
        // Có thể checkAuth nếu các trang tiện ích cần đăng nhập
        // $this->checkAuth();
    }

    public function bieuMau() { 
        $this->view('tien_ich/bieu_mau', [
            'title' => 'Biểu mẫu Đăng ký Hồ sơ',
            'currentController' => get_class($this),
            'action' => 'bieuMau' // Truyền action
        ]);
    }

    public function huongDan() { 
        $this->view('tien_ich/huong_dan', [
            'title' => 'Hướng dẫn Sử dụng Phần mềm',
            'currentController' => get_class($this),
            'action' => 'huongDan' // Truyền action
        ]);
    }

    /**
     * Action để hiển thị trang Văn bản Chính sách
     */
    public function vanBanChinhSach() {
        // Danh sách văn bản chính sách đang áp dụng (set cứng)
        $vanBanDangApDung = [
            [
                'ten_van_ban' => 'Nghị định số 20/2021/NĐ-CP ngày 15/3/2021 của Chính phủ quy định chính sách trợ giúp xã hội đối với đối tượng bảo trợ xã hội.',
                'ghi_chu' => 'Áp dụng chung cho các đối tượng bảo trợ xã hội.',
                'file_path' => 'uploads/20.signed.pdf'
            ],
            [
                'ten_van_ban' => 'Nghị định số 76/2024/NĐ-CP ngày 1/7/2024 Sửa đổi, bổ sung một số điều của Nghị định sôs 20/2021/NĐ-CP',
                'file_path' => 'uploads/76-cp.signed.pdf'
            ],
            [
                'ten_van_ban' => 'Thông tư số 02/2021/TT-BLĐTBXH ngày 24/6/2021 của Bộ Lao động - Thương binh và Xã hội hướng dẫn thực hiện một số điều của Nghị định số 20/2021/NĐ-CP.',
                'ghi_chu' => 'Hướng dẫn chi tiết, biểu mẫu liên quan đến NĐ 20/2021.',
                'file_path' => 'uploads/VanBanGoc_thong-tu-02-2021-tt-bldtbxh-huong-dan-nghi-dinh-20-2021-nd-cp.pdf'
            ],
            // [
            //     'ten_van_ban' => 'Quyết định số [Số Quyết định]/QĐ-UBND ngày [Ngày/Tháng/Năm] của UBND tỉnh [Tên Tỉnh] về việc quy định mức chuẩn trợ giúp xã hội và các chính sách trợ giúp xã hội trên địa bàn tỉnh.',
            //     'ghi_chu' => 'Quy định cụ thể của địa phương (cần cập nhật theo tỉnh/thành phố).'
            // ],
            // Thêm các văn bản khác nếu cần
        ];
        
        $this->view('tien_ich/van_ban_chinh_sach', [
            'title' => 'Văn bản Chính sách Tham khảo',
            'currentController' => get_class($this),
            'action' => 'vanBanChinhSach', // Truyền action
            'vanBanList' => $vanBanDangApDung 
        ]);
    }

    public function index() {
        $this->redirect('utility/bieuMau'); 
    }
}
?>