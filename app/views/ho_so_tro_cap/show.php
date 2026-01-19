<?php
// View: app/views/ho_so_tro_cap/show.php
// Vars: $title, $hoSo, $trangThaiOptions
?>
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-700"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
    <div>
        <a href="<?php echo url('ho-so-tro-cap/edit/' . $hoSo['id']); ?>" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Chỉnh sửa
        </a>
        <a href="<?php echo url('ho-so-tro-cap/index?doi_tuong_id=' . $hoSo['doi_tuong_id']); ?>" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
            Quay lại DS Hồ sơ
        </a>
    </div>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6 pb-6 border-b">
        <div>
            <h3 class="text-md font-semibold text-gray-600 mb-1">Đối tượng hưởng</h3>
            <p class="text-lg font-medium text-gray-800">
                <a href="<?php echo url('doi-tuong/edit/' . $hoSo['doi_tuong_id']); ?>" target="_blank" class="text-blue-600 hover:underline">
                    <?php echo htmlspecialchars($hoSo['ten_doi_tuong'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>
                </a>
            </p>
            <p class="text-sm text-gray-500">Mã ĐT: <?php echo htmlspecialchars($hoSo['ma_doi_tuong'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <!-- <div>
            <h3 class="text-md font-semibold text-gray-600 mb-1">Mã định danh</h3>
            <p class="text-lg font-medium text-gray-800"><?php echo htmlspecialchars($doituong['cccd'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></p>
        </div> -->
        <div>
            <h3 class="text-md font-semibold text-gray-600 mb-1">Trạng thái</h3>
            <p class="text-lg font-medium">
                 <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                    <?php
                        $trangThaiClass = 'bg-gray-100 text-gray-800'; 
                        if ($hoSo['trang_thai'] === 'da_phe_duyet_dang_huong') $trangThaiClass = 'bg-green-100 text-green-800';
                        elseif (in_array($hoSo['trang_thai'], ['cho_xem_xet', 'cho_duyet'])) $trangThaiClass = 'bg-yellow-100 text-yellow-800';
                        elseif (in_array($hoSo['trang_thai'], ['khong_du_dieu_kien', 'da_dung_huong', 'da_chuyen_co_so_khac'])) $trangThaiClass = 'bg-red-100 text-red-800';
                        elseif ($hoSo['trang_thai'] === 'tam_dung_huong') $trangThaiClass = 'bg-orange-100 text-orange-800';
                        echo $trangThaiClass;
                    ?>
                ">
                    <?php echo htmlspecialchars($trangThaiOptions[$hoSo['trang_thai']] ?? ucfirst(str_replace('_', ' ', $hoSo['trang_thai'])), ENT_QUOTES, 'UTF-8'); ?>
                </span>
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
        <div>
            <p class="text-sm text-gray-500">Mức Trợ Cấp Hàng Tháng (Theo QĐ)</p>
            <p class="text-gray-800">
                <?php echo htmlspecialchars($hoSo['ten_muc_tro_cap'] ?? 'Chưa rõ', ENT_QUOTES, 'UTF-8'); ?>
                <!-- <?php if (!empty($hoSo['ma_muc_tro_cap'])): ?>
                    <span class="text-xs text-gray-500">(Mã: <?php echo htmlspecialchars($hoSo['ma_muc_tro_cap'], ENT_QUOTES, 'UTF-8'); ?>)</span>
                <?php endif; ?> -->
                <?php if (isset($hoSo['so_tien_muc_chuan']) && is_numeric($hoSo['so_tien_muc_chuan'])): ?>
                     - <span class="font-normal"><?php echo number_format($hoSo['so_tien_muc_chuan'], 0, ',', '.'); ?> đ</span>
                <?php endif; ?>
            </p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Số tiền hưởng thực tế</p>
            <p class="text-gray-800 font-semibold"><?php echo !empty($hoSo['muc_tro_cap_hang_thang']) ? number_format($hoSo['muc_tro_cap_hang_thang'], 0, ',', '.') . ' VNĐ' : 'Chưa có'; ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Ngày đề nghị hưởng</p>
            <p class="text-gray-800"><?php echo !empty($hoSo['ngay_de_nghi_huong']) ? date('d/m/Y', strtotime($hoSo['ngay_de_nghi_huong'])) : 'Không có'; ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Ngày bắt đầu hưởng</p>
            <p class="text-gray-800"><?php echo !empty($hoSo['ngay_bat_dau_huong']) ? date('d/m/Y', strtotime($hoSo['ngay_bat_dau_huong'])) : 'Chưa có'; ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Ngày kết thúc hưởng</p>
            <p class="text-gray-800"><?php echo !empty($hoSo['ngay_ket_thuc_huong']) ? date('d/m/Y', strtotime($hoSo['ngay_ket_thuc_huong'])) : 'Không xác định'; ?></p>
        </div>
         <!-- <div>
            <p class="text-sm text-gray-500">Loại đối tượng (khi lập HS)</p>
            <p class="text-gray-800"><?php echo htmlspecialchars($hoSo['ten_loai_doi_tuong'] ?? 'Không rõ', ENT_QUOTES, 'UTF-8'); ?></p>
        </div> -->
        <div class="md:col-span-2">
            <p class="text-sm text-gray-500">Lý do/Căn cứ trợ cấp</p>
            <p class="text-gray-800 whitespace-pre-wrap"><?php echo !empty($hoSo['ly_do_tro_cap']) ? nl2br(htmlspecialchars($hoSo['ly_do_tro_cap'], ENT_QUOTES, 'UTF-8')) : 'Không có'; ?></p>
        </div>
        
        <?php if(in_array($hoSo['trang_thai'], ['da_phe_duyet_dang_huong', 'tam_dung_huong', 'da_dung_huong', 'khong_du_dieu_kien'])): ?>
        <hr class="md:col-span-2 my-2">
        <div>
            <p class="text-sm text-gray-500">Người duyệt/xử lý</p>
            <p class="text-gray-800"><?php echo htmlspecialchars($hoSo['ten_nguoi_duyet'] ?? 'Chưa có', ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Ngày duyệt/xử lý</p>
            <p class="text-gray-800"><?php echo !empty($hoSo['ngay_duyet']) ? date('d/m/Y', strtotime($hoSo['ngay_duyet'])) : 'Chưa có'; ?></p>
        </div>
            <?php if(!empty($hoSo['ly_do_thay_doi_trang_thai'])): ?>
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500">Lý do (cho trạng thái hiện tại)</p>
                <p class="text-gray-800 whitespace-pre-wrap"><?php echo nl2br(htmlspecialchars($hoSo['ly_do_thay_doi_trang_thai'], ENT_QUOTES, 'UTF-8')); ?></p>
            </div>
            <?php endif; ?>
        <?php endif; ?>

        <hr class="md:col-span-2 my-2">
         <div>
            <p class="text-sm text-gray-500">Người lập hồ sơ</p>
            <p class="text-gray-800"><?php echo htmlspecialchars($hoSo['ten_nguoi_lap'] ?? 'Không rõ', ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Ngày tạo hồ sơ</p>
            <p class="text-gray-800"><?php echo !empty($hoSo['ngay_tao']) ? date('d/m/Y H:i:s', strtotime($hoSo['ngay_tao'])) : 'N/A'; ?></p>
        </div>
        <div class="md:col-span-2">
            <p class="text-sm text-gray-500">Ghi chú hồ sơ</p>
            <p class="text-gray-800 whitespace-pre-wrap"><?php echo !empty($hoSo['ghi_chu_hs']) ? nl2br(htmlspecialchars($hoSo['ghi_chu_hs'], ENT_QUOTES, 'UTF-8')) : 'Không có'; ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Ngày cập nhật lần cuối</p>
            <p class="text-gray-800"><?php echo !empty($hoSo['ngay_cap_nhat']) ? date('d/m/Y H:i:s', strtotime($hoSo['ngay_cap_nhat'])) : 'N/A'; ?></p>
        </div>
    </div>
</div>

<!-- Phần lịch sử chi trả có thể thêm ở đây nếu cần -->