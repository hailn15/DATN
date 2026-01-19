<?php
// View: app/views/ho_tro_khan_cap/show.php
// Vars: $title, $hoTro, $trangThaiOptions
?>
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-700"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
    <div>
        <a href="<?php echo url('ho-tro-khan-cap/edit/' . $hoTro['id']); ?>" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Chỉnh sửa
        </a>
        <a href="<?php echo url('ho-tro-khan-cap/index' . (!empty($hoTro['doi_tuong_id']) ? '?doi_tuong_id=' . $hoTro['doi_tuong_id'] : '')); ?>" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
            Quay lại DS
        </a>
    </div>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-2 gap-y-6 mb-6 pb-6 border-b">
        <div>
            <h3 class="text-md font-semibold text-gray-600 mb-1">Người nhận hỗ trợ</h3>
            <p class="text-lg font-medium text-gray-800">
                <?php echo htmlspecialchars($hoTro['ho_ten_nguoi_nhan'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <?php if (!empty($hoTro['ten_doi_tuong_lien_quan'])): ?>
                <p class="text-sm text-gray-500">ĐT liên quan: 
                    <a href="<?php echo url('doi-tuong/edit/' . $hoTro['doi_tuong_id']); ?>" target="_blank" class="text-blue-600 hover:underline">
                        <?php echo htmlspecialchars($hoTro['ten_doi_tuong_lien_quan'] . ' (Mã: ' . $hoTro['ma_doi_tuong'] .')', ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </p>
            <?php endif; ?>
        </div>
        <div>
            <h3 class="text-md font-semibold text-gray-600 mb-1">Mã định danh</h3>
            <p class="text-lg font-medium text-gray-800"><?php echo htmlspecialchars($hoTro['cccd_nguoi_nhan'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <!-- <div>
            <h3 class="text-md font-semibold text-gray-600 mb-1">Mã hồ sơ KC</h3>
            <p class="text-lg font-medium text-gray-800"></p>
        </div> -->
        <div>
            <h3 class="text-md font-semibold text-gray-600 mb-1">Trạng thái</h3>
            <p class="text-lg font-medium">
                 <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                    <?php
                        $trangThaiClass = 'bg-gray-100 text-gray-800';
                        if ($hoTro['trang_thai_hs_kc'] === 'da_ho_tro') $trangThaiClass = 'bg-green-100 text-green-800';
                        elseif (in_array($hoTro['trang_thai_hs_kc'], ['cho_xem_xet', 'cho_duyet'])) $trangThaiClass = 'bg-yellow-100 text-yellow-800';
                        elseif (in_array($hoTro['trang_thai_hs_kc'], ['khong_du_dieu_kien', 'huy_bo'])) $trangThaiClass = 'bg-red-100 text-red-800';
                        echo $trangThaiClass;
                    ?>
                ">
                    <?php echo htmlspecialchars($trangThaiOptions[$hoTro['trang_thai_hs_kc']] ?? ucfirst(str_replace('_', ' ', $hoTro['trang_thai_hs_kc'])), ENT_QUOTES, 'UTF-8'); ?>
                </span>
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
        <div>
            <p class="text-sm text-gray-500">Loại hình hỗ trợ</p>
            <p class="text-gray-800">
                <?php echo htmlspecialchars($hoTro['ten_loai_hinh_ho_tro'] ?? 'Chưa rõ', ENT_QUOTES, 'UTF-8'); ?>
                <?php if (isset($hoTro['gia_tri_dinh_muc_loai_hinh']) && is_numeric($hoTro['gia_tri_dinh_muc_loai_hinh'])): ?>
                     <span class="text-xs text-gray-500">(ĐM: <?php echo number_format($hoTro['gia_tri_dinh_muc_loai_hinh'], 0, ',', '.'); ?> đ)</span>
                <?php endif; ?>
            </p>
        </div>
         <div>
            <p class="text-sm text-gray-500">Hình thức hỗ trợ cụ thể</p>
            <p class="text-gray-800"><?php echo !empty($hoTro['hinh_thuc_ho_tro_cu_the']) ? htmlspecialchars($hoTro['hinh_thuc_ho_tro_cu_the'], ENT_QUOTES, 'UTF-8') : 'Không có'; ?></p>
        </div>
        <div class="md:col-span-2">
            <p class="text-sm text-gray-500">Lý do hỗ trợ</p>
            <p class="text-gray-800 whitespace-pre-wrap"><?php echo !empty($hoTro['ly_do_ho_tro']) ? nl2br(htmlspecialchars($hoTro['ly_do_ho_tro'], ENT_QUOTES, 'UTF-8')) : 'Không có'; ?></p>
        </div>
        
        <hr class="md:col-span-2 my-2">
        <div>
            <p class="text-sm text-gray-500">Giá trị hỗ trợ (Tiền mặt)</p>
            <p class="text-gray-800 font-semibold"><?php echo isset($hoTro['gia_tri_ho_tro_tien_mat']) ? number_format($hoTro['gia_tri_ho_tro_tien_mat'], 0, ',', '.') . ' VNĐ' : 'Không có'; ?></p>
        </div>
        <div class="md:col-span-2">
            <p class="text-sm text-gray-500">Mô tả hiện vật hỗ trợ</p>
            <p class="text-gray-800 whitespace-pre-wrap"><?php echo !empty($hoTro['mo_ta_hien_vat_ho_tro']) ? nl2br(htmlspecialchars($hoTro['mo_ta_hien_vat_ho_tro'], ENT_QUOTES, 'UTF-8')) : 'Không có'; ?></p>
        </div>
        
        <hr class="md:col-span-2 my-2">
         <div>
            <p class="text-sm text-gray-500">Ngày đề xuất</p>
            <p class="text-gray-800"><?php echo !empty($hoTro['ngay_de_xuat_ht']) ? date('d/m/Y', strtotime($hoTro['ngay_de_xuat_ht'])) : 'Chưa có'; ?></p>
        </div>

        <?php if(in_array($hoTro['trang_thai_hs_kc'], ['da_ho_tro', 'khong_du_dieu_kien', 'huy_bo'])): ?>
        <div>
            <p class="text-sm text-gray-500">Người xử lý</p>
            <p class="text-gray-800"><?php echo htmlspecialchars($hoTro['ten_nguoi_xu_ly'] ?? 'Chưa có', ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Ngày xử lý</p>
            <p class="text-gray-800"><?php echo !empty($hoTro['ngay_xu_ly_ht']) ? date('d/m/Y', strtotime($hoTro['ngay_xu_ly_ht'])) : 'Chưa có'; ?></p>
        </div>
            <?php if(!empty($hoTro['ly_do_tu_choi_huy_bo_kc'])): ?>
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500">Lý do từ chối/hủy bỏ</p>
                <p class="text-gray-800 whitespace-pre-wrap"><?php echo nl2br(htmlspecialchars($hoTro['ly_do_tu_choi_huy_bo_kc'], ENT_QUOTES, 'UTF-8')); ?></p>
            </div>
            <?php endif; ?>
        <?php endif; ?>

        <hr class="md:col-span-2 my-2">
        <div>
            <p class="text-sm text-gray-500">Người lập hồ sơ</p>
            <p class="text-gray-800"><?php echo htmlspecialchars($hoTro['ten_nguoi_lap_hs_kc'] ?? 'Không rõ', ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
         <div>
            <p class="text-sm text-gray-500">Ngày tạo hồ sơ</p>
            <p class="text-gray-800"><?php echo !empty($hoTro['ngay_tao']) ? date('d/m/Y H:i:s', strtotime($hoTro['ngay_tao'])) : 'N/A'; ?></p>
        </div>
         <div class="md:col-span-2">
            <p class="text-sm text-gray-500">Ghi chú hồ sơ</p>
            <p class="text-gray-800 whitespace-pre-wrap"><?php echo !empty($hoTro['ghi_chu_hs_kc']) ? nl2br(htmlspecialchars($hoTro['ghi_chu_hs_kc'], ENT_QUOTES, 'UTF-8')) : 'Không có'; ?></p>
        </div>
         <div>
            <p class="text-sm text-gray-500">Ngày cập nhật lần cuối</p>
            <p class="text-gray-800"><?php echo !empty($hoTro['ngay_cap_nhat']) ? date('d/m/Y H:i:s', strtotime($hoTro['ngay_cap_nhat'])) : 'N/A'; ?></p>
        </div>
    </div>
</div>