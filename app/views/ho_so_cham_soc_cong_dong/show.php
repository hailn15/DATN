<?php
// View: app/views/ho_so_cham_soc_cong_dong/show.php
// Vars: $title, $hoSo, $trangThaiOptions
?>
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-700"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
    <div>
        <a href="<?php echo url('ho-so-cham-soc-cong-dong/edit/' . $hoSo['id']); ?>" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Chỉnh sửa
        </a>
        <a href="<?php echo url('ho-so-cham-soc-cong-dong/index?doi_tuong_id=' . $hoSo['doi_tuong_id']); ?>" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
            Quay lại DS Hồ sơ
        </a>
    </div>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6 pb-6 border-b">
        <div>
            <h3 class="text-md font-semibold text-gray-600 mb-1">Đối tượng Chăm sóc</h3>
            <p class="text-lg font-medium text-gray-800">
                <a href="<?php echo url('doi-tuong/edit/' . $hoSo['doi_tuong_id']); ?>" target="_blank" class="text-blue-600 hover:underline">
                    <?php echo htmlspecialchars($hoSo['ten_doi_tuong'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>
                </a>
            </p>
            <p class="text-sm text-gray-500">Mã ĐT: <?php echo htmlspecialchars($hoSo['ma_doi_tuong'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div>
            <h3 class="text-md font-semibold text-gray-600 mb-1">Mã hồ sơ CS</h3>
            <p class="text-lg font-medium text-gray-800"><?php echo htmlspecialchars($hoSo['ma_ho_so_cs'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div>
            <h3 class="text-md font-semibold text-gray-600 mb-1">Trạng thái</h3>
            <p class="text-lg font-medium">
                 <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                    <?php
                        $trangThaiClass = 'bg-gray-100 text-gray-800';
                        if ($hoSo['trang_thai_hs_cs'] === 'da_phe_duyet') $trangThaiClass = 'bg-green-100 text-green-800';
                        elseif (in_array($hoSo['trang_thai_hs_cs'], ['cho_xem_xet', 'cho_duyet'])) $trangThaiClass = 'bg-yellow-100 text-yellow-800';
                        elseif (in_array($hoSo['trang_thai_hs_cs'], ['khong_du_dieu_kien', 'da_ket_thuc', 'huy_bo'])) $trangThaiClass = 'bg-red-100 text-red-800';
                        elseif ($hoSo['trang_thai_hs_cs'] === 'tam_dung') $trangThaiClass = 'bg-orange-100 text-orange-800';
                        echo $trangThaiClass;
                    ?>
                ">
                    <?php echo htmlspecialchars($trangThaiOptions[$hoSo['trang_thai_hs_cs']] ?? ucfirst(str_replace('_', ' ', $hoSo['trang_thai_hs_cs'])), ENT_QUOTES, 'UTF-8'); ?>
                </span>
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
        <div>
            <p class="text-sm text-gray-500">Loại hình chăm sóc</p>
            <p class="text-gray-800">
                <?php echo htmlspecialchars($hoSo['ten_loai_hinh_cham_soc'] ?? 'Chưa rõ', ENT_QUOTES, 'UTF-8'); ?>
                <?php if (isset($hoSo['kinh_phi_dinh_muc_loai_hinh']) && is_numeric($hoSo['kinh_phi_dinh_muc_loai_hinh'])): ?>
                     <span class="text-xs text-gray-500">(ĐM: <?php echo number_format($hoSo['kinh_phi_dinh_muc_loai_hinh'], 0, ',', '.'); ?> đ)</span>
                <?php endif; ?>
            </p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Hình thức chăm sóc cụ thể</p>
            <p class="text-gray-800"><?php echo !empty($hoSo['hinh_thuc_cham_soc_cu_the']) ? htmlspecialchars($hoSo['hinh_thuc_cham_soc_cu_the'], ENT_QUOTES, 'UTF-8') : 'Không có'; ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Ngày đề nghị</p>
            <p class="text-gray-800"><?php echo !empty($hoSo['ngay_de_nghi_cs']) ? date('d/m/Y', strtotime($hoSo['ngay_de_nghi_cs'])) : 'Chưa có'; ?></p>
        </div>
         <div class="md:col-span-2">
            <p class="text-sm text-gray-500">Nội dung đề nghị</p>
            <p class="text-gray-800 whitespace-pre-wrap"><?php echo !empty($hoSo['noi_dung_de_nghi']) ? nl2br(htmlspecialchars($hoSo['noi_dung_de_nghi'], ENT_QUOTES, 'UTF-8')) : 'Không có'; ?></p>
        </div>

        <hr class="md:col-span-2 my-2">
        <div>
            <p class="text-sm text-gray-500">Đơn vị thực hiện (ngoài)</p>
            <p class="text-gray-800"><?php echo !empty($hoSo['ten_don_vi_thuc_hien_ngoai']) ? htmlspecialchars($hoSo['ten_don_vi_thuc_hien_ngoai'], ENT_QUOTES, 'UTF-8') : 'Không có'; ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Người chăm sóc chính</p>
            <p class="text-gray-800"><?php echo $nguoiChamSocTen; ?></p>
        </div>
         <!-- <div>
               
            <p class="text-sm text-gray-500">Người chăm sóc chính</p>
            <p class="text-gray-800"><?php echo !empty($hoSo['nguoi_cham_soc_id']) ? htmlspecialchars($hoSo['nguoi_cham_soc_id'], ENT_QUOTES, 'UTF-8') : 'Không có'; ?></p>
        </div> -->
        <div>
            <p class="text-sm text-gray-500">Kinh phí dự kiến</p>
            <p class="text-gray-800 font-semibold"><?php echo isset($hoSo['kinh_phi_du_kien']) ? number_format($hoSo['kinh_phi_du_kien'], 0, ',', '.') . ' VNĐ' : 'Chưa có'; ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Nguồn kinh phí</p>
            <p class="text-gray-800"><?php echo !empty($hoSo['nguon_kinh_phi']) ? htmlspecialchars($hoSo['nguon_kinh_phi'], ENT_QUOTES, 'UTF-8') : 'Không có'; ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Ngày bắt đầu chăm sóc</p>
            <p class="text-gray-800"><?php echo !empty($hoSo['ngay_bat_dau_cham_soc']) ? date('d/m/Y', strtotime($hoSo['ngay_bat_dau_cham_soc'])) : 'Chưa có'; ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Ngày kết thúc dự kiến</p>
            <p class="text-gray-800"><?php echo !empty($hoSo['ngay_ket_thuc_du_kien_cs']) ? date('d/m/Y', strtotime($hoSo['ngay_ket_thuc_du_kien_cs'])) : 'Không có'; ?></p>
        </div>
        
        <?php if(in_array($hoSo['trang_thai_hs_cs'], ['da_phe_duyet', 'khong_du_dieu_kien', 'tam_dung', 'da_ket_thuc', 'huy_bo'])): ?>
        <hr class="md:col-span-2 my-2">
        <div>
            <p class="text-sm text-gray-500">Người xét duyệt/xử lý</p>
            <p class="text-gray-800"><?php echo htmlspecialchars($hoSo['ten_nguoi_xet_duyet'] ?? 'Chưa có', ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Ngày xét duyệt/xử lý</p>
            <p class="text-gray-800"><?php echo !empty($hoSo['ngay_xet_duyet_hs_cs']) ? date('d/m/Y', strtotime($hoSo['ngay_xet_duyet_hs_cs'])) : 'Chưa có'; ?></p>
        </div>
            <?php if(!empty($hoSo['ly_do_thay_doi_trang_thai_cs'])): ?>
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500">Lý do (cho trạng thái hiện tại)</p>
                <p class="text-gray-800 whitespace-pre-wrap"><?php echo nl2br(htmlspecialchars($hoSo['ly_do_thay_doi_trang_thai_cs'], ENT_QUOTES, 'UTF-8')); ?></p>
            </div>
            <?php endif; ?>
        <?php endif; ?>

        <hr class="md:col-span-2 my-2">
        <div>
            <p class="text-sm text-gray-500">Người lập hồ sơ</p>
            <p class="text-gray-800"><?php echo htmlspecialchars($hoSo['ten_nguoi_lap_hs_cs'] ?? 'Không rõ', ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
         <div>
            <p class="text-sm text-gray-500">Ngày tạo hồ sơ</p>
            <p class="text-gray-800"><?php echo !empty($hoSo['ngay_tao']) ? date('d/m/Y H:i:s', strtotime($hoSo['ngay_tao'])) : 'N/A'; ?></p>
        </div>
        <div class="md:col-span-2">
            <p class="text-sm text-gray-500">Ghi chú hồ sơ</p>
            <p class="text-gray-800 whitespace-pre-wrap"><?php echo !empty($hoSo['ghi_chu_hs_cs']) ? nl2br(htmlspecialchars($hoSo['ghi_chu_hs_cs'], ENT_QUOTES, 'UTF-8')) : 'Không có'; ?></p>
        </div>
         <div>
            <p class="text-sm text-gray-500">Ngày cập nhật lần cuối</p>
            <p class="text-gray-800"><?php echo !empty($hoSo['ngay_cap_nhat']) ? date('d/m/Y H:i:s', strtotime($hoSo['ngay_cap_nhat'])) : 'N/A'; ?></p>
        </div>
    </div>
</div>