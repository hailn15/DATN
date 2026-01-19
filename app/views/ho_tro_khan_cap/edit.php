<?php
// View: app/views/ho_tro_khan_cap/edit.php
// Vars: $title, $hoTro, $doiTuong, $trangThaiOptions, $loaiHinhKCOptions, $nguoiXuLyOptions, $oldData, $errors
$displayData = $oldData ?? $hoTro;
$currentUser = getCurrentUser(); 
?>
<h1 class="text-2xl font-semibold text-gray-700 mb-6"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>

<?php if (!empty($errors)): ?>
<div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-400 rounded">
    <strong class="font-bold">Có lỗi xảy ra:</strong>
    <ul class="list-disc list-inside ml-4">
        <?php foreach ($errors as $field => $error): ?>
            <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<?php if ($doiTuong): 
    $ma_dt = htmlspecialchars($doiTuong['ma_doi_tuong'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
    $ten_dt = htmlspecialchars($doiTuong['ho_ten'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
?>
<div class="mb-6 bg-blue-50 p-4 rounded-lg border border-blue-200">
    <h3 class="text-lg font-semibold text-blue-700 mb-2">Thông tin Đối tượng liên quan</h3>
    <p><strong>Họ tên:</strong> <?php echo $ten_dt; ?> (Mã: <?php echo $ma_dt; ?>)</p>
</div>
<?php endif; ?>

<form action="<?php echo url('ho-tro-khan-cap/update/' . $hoTro['id']); ?>" method="POST" class="bg-white p-6 rounded-lg shadow-md">
    <input type="hidden" name="doi_tuong_id" value="<?php echo htmlspecialchars($displayData['doi_tuong_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Cột 1 -->
        <div class="md:col-span-1">
            <div class="mb-4">
                <label for="ma_ho_so_kc_edit" class="form-label">Mã hồ sơ KC</label>
                <input type="text" id="ma_ho_so_kc_edit" name="ma_ho_so_kc" maxlength="30"
                       value="<?php echo htmlspecialchars($displayData['ma_ho_so_kc'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['ma_ho_so_kc']) ? 'border-red-500' : ''; ?>">
                 <?php if(isset($errors['ma_ho_so_kc'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ma_ho_so_kc']; ?></p><?php endif; ?>
            </div>
            <div class="mb-4">
                <label for="ho_ten_nguoi_nhan_edit" class="form-label">Họ tên người nhận <span class="text-red-500">*</span></label>
                <input type="text" id="ho_ten_nguoi_nhan_edit" name="ho_ten_nguoi_nhan" required maxlength="100"
                       value="<?php echo htmlspecialchars($displayData['ho_ten_nguoi_nhan'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['ho_ten_nguoi_nhan']) ? 'border-red-500' : ''; ?>">
                <?php if(isset($errors['ho_ten_nguoi_nhan'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ho_ten_nguoi_nhan']; ?></p><?php endif; ?>
            </div>
            <div class="mb-4">
                <label for="cccd_nguoi_nhan_edit" class="form-label">CCCD người nhận</label>
                <input type="text" id="cccd_nguoi_nhan_edit" name="cccd_nguoi_nhan" maxlength="15"
                       value="<?php echo htmlspecialchars($displayData['cccd_nguoi_nhan'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input">
            </div>
             <div class="mb-4">
                <label for="sdt_nguoi_nhan_edit" class="form-label">SĐT người nhận</label>
                <input type="text" id="sdt_nguoi_nhan_edit" name="sdt_nguoi_nhan" maxlength="15"
                       value="<?php echo htmlspecialchars($displayData['sdt_nguoi_nhan'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input">
            </div>
        </div>
        
        <!-- Cột 2 -->
        <div class="md:col-span-1">
            <div class="mb-4">
                <label for="dia_chi_nguoi_nhan_edit" class="form-label">Địa chỉ người nhận</label>
                <textarea id="dia_chi_nguoi_nhan_edit" name="dia_chi_nguoi_nhan" rows="2" class="form-textarea"><?php echo htmlspecialchars($displayData['dia_chi_nguoi_nhan'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
            <div class="mb-4">
                <label for="loai_hinh_ho_tro_kc_id_edit" class="form-label">Loại hình hỗ trợ <span class="text-red-500">*</span></label>
                <select id="loai_hinh_ho_tro_kc_id_edit" name="loai_hinh_ho_tro_kc_id" required
                        class="form-input <?php echo isset($errors['loai_hinh_ho_tro_kc_id']) ? 'border-red-500' : ''; ?>">
                    <option value="">-- Chọn loại hình --</option>
                    <?php if (isset($loaiHinhKCOptions) && !empty($loaiHinhKCOptions)): ?>
                        <?php foreach ($loaiHinhKCOptions as $lh): ?>
                            <option value="<?php echo $lh['id']; ?>" 
                                    data-gia-tri="<?php echo htmlspecialchars($lh['gia_tri_ho_tro_dinh_muc'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    data-hien-vat="<?php echo htmlspecialchars($lh['mo_ta_hien_vat_dinh_muc'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo (isset($displayData['loai_hinh_ho_tro_kc_id']) && $displayData['loai_hinh_ho_tro_kc_id'] == $lh['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($lh['ten_loai_hinh'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                         <option value="" disabled>Chưa có loại hình HTKC</option>
                    <?php endif; ?>
                </select>
                <?php if(isset($errors['loai_hinh_ho_tro_kc_id'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['loai_hinh_ho_tro_kc_id']; ?></p><?php endif; ?>
            </div>
             <div class="mb-4">
                <label for="ly_do_ho_tro_edit" class="form-label">Lý do hỗ trợ <span class="text-red-500">*</span></label>
                <textarea id="ly_do_ho_tro_edit" name="ly_do_ho_tro" rows="3" required class="form-textarea <?php echo isset($errors['ly_do_ho_tro']) ? 'border-red-500' : ''; ?>"><?php echo htmlspecialchars($displayData['ly_do_ho_tro'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                 <?php if(isset($errors['ly_do_ho_tro'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ly_do_ho_tro']; ?></p><?php endif; ?>
            </div>
            <div class="mb-4">
                <label for="hinh_thuc_ho_tro_cu_the_edit" class="form-label">Hình thức hỗ trợ cụ thể</label>
                <input type="text" id="hinh_thuc_ho_tro_cu_the_edit" name="hinh_thuc_ho_tro_cu_the" maxlength="255"
                       value="<?php echo htmlspecialchars($displayData['hinh_thuc_ho_tro_cu_the'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input">
            </div>
        </div>

        <!-- Cột 3 -->
        <div class="md:col-span-1">
            <div class="mb-4">
                <label for="gia_tri_ho_tro_tien_mat_edit" class="form-label">Giá trị hỗ trợ (Tiền mặt - VNĐ)</label>
                <input type="number" id="gia_tri_ho_tro_tien_mat_edit" name="gia_tri_ho_tro_tien_mat" min="0" step="1000"
                       value="<?php echo htmlspecialchars($displayData['gia_tri_ho_tro_tien_mat'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['gia_tri_ho_tro_tien_mat']) ? 'border-red-500' : ''; ?>"
                       placeholder="Để trống nếu theo định mức">
                 <?php if(isset($errors['gia_tri_ho_tro_tien_mat'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['gia_tri_ho_tro_tien_mat']; ?></p><?php endif; ?>
            </div>
            <div class="mb-4">
                <label for="mo_ta_hien_vat_ho_tro_edit" class="form-label">Mô tả hiện vật hỗ trợ</label>
                <textarea id="mo_ta_hien_vat_ho_tro_edit" name="mo_ta_hien_vat_ho_tro" rows="2" class="form-textarea"
                 placeholder="Để trống nếu theo định mức"><?php echo htmlspecialchars($displayData['mo_ta_hien_vat_ho_tro'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
             <div class="mb-4">
                <label for="ngay_de_xuat_ht_edit" class="form-label">Ngày đề xuất</label>
                <input type="date" id="ngay_de_xuat_ht_edit" name="ngay_de_xuat_ht"
                       value="<?php echo htmlspecialchars($displayData['ngay_de_xuat_ht'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input">
            </div>
            <div class="mb-4">
                <label for="trang_thai_hs_kc_edit" class="form-label">Trạng thái hồ sơ <span class="text-red-500">*</span></label>
                <select id="trang_thai_hs_kc_edit" name="trang_thai_hs_kc" class="form-input <?php echo (isset($errors['trang_thai_hs_kc']) || isset($errors['nguoi_xu_ly_hs_kc_id']) || isset($errors['ngay_xu_ly_ht']) || isset($errors['ly_do_tu_choi_huy_bo_kc'])) ? 'border-red-500' : ''; ?>">
                    <?php $selectedTrangThai = $displayData['trang_thai_hs_kc'] ?? ''; ?>
                    <?php foreach ($trangThaiOptions as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php echo ($selectedTrangThai == $value) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if(isset($errors['trang_thai_hs_kc'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['trang_thai_hs_kc']; ?></p><?php endif; ?>
            </div>
            <?php 
                $showXuLyFieldsKCEdit = ($currentUser && $currentUser['role'] == 'admin') || ($selectedTrangThai == 'da_ho_tro'); 
                $tenNguoiXuLyDisplayKC = $displayData['ten_nguoi_xu_ly'] ?? '';
                if ($selectedTrangThai == 'da_ho_tro' && empty($tenNguoiXuLyDisplayKC) && isset($displayData['nguoi_xu_ly_hs_kc_id']) && $displayData['nguoi_xu_ly_hs_kc_id'] == $currentUser['id']) {
                    $tenNguoiXuLyDisplayKC = $currentUser['fullname'];
                } elseif (empty($tenNguoiXuLyDisplayKC) && $selectedTrangThai == 'da_ho_tro') {
                    $tenNguoiXuLyDisplayKC = $currentUser['fullname'] ?? '';
                }
            ?>
            <div id="processing-fields-kc-edit" class="<?php echo $showXuLyFieldsKCEdit ? '' : 'hidden'; ?>">
                <div class="mb-4">
                    <label for="nguoi_xu_ly_hs_kc_id_display_edit" class="form-label">Người xử lý</label>
                    <input type="text" id="nguoi_xu_ly_hs_kc_id_display_edit" value="<?php echo htmlspecialchars($tenNguoiXuLyDisplayKC, ENT_QUOTES, 'UTF-8'); ?>" 
                           class="form-input bg-gray-100 <?php echo isset($errors['nguoi_xu_ly_hs_kc_id']) ? 'border-red-500' : ''; ?>" readonly 
                           placeholder="<?php echo ($selectedTrangThai == 'da_ho_tro') ? 'Mặc định là bạn' : 'Chỉ hiển thị khi xử lý';?>">
                    <input type="hidden" name="nguoi_xu_ly_hs_kc_id" id="nguoi_xu_ly_hs_kc_id_edit" value="<?php echo htmlspecialchars($displayData['nguoi_xu_ly_hs_kc_id'] ?? ($selectedTrangThai == 'da_ho_tro' ? ($currentUser['id'] ?? '') : ''), ENT_QUOTES, 'UTF-8'); ?>">
                     <?php if(isset($errors['nguoi_xu_ly_hs_kc_id'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['nguoi_xu_ly_hs_kc_id']; ?></p><?php endif; ?>
                </div>
                <div class="mb-4">
                    <label for="ngay_xu_ly_ht_edit" class="form-label">Ngày xử lý</label>
                    <input type="date" id="ngay_xu_ly_ht_edit" name="ngay_xu_ly_ht"
                           value="<?php echo htmlspecialchars($displayData['ngay_xu_ly_ht'] ?? ($selectedTrangThai == 'da_ho_tro' ? date('Y-m-d') : ''), ENT_QUOTES, 'UTF-8'); ?>"
                           class="form-input <?php echo isset($errors['ngay_xu_ly_ht']) ? 'border-red-500' : ''; ?>">
                    <?php if(isset($errors['ngay_xu_ly_ht'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ngay_xu_ly_ht']; ?></p><?php endif; ?>
                </div>
            </div>
            <div class="mb-4 <?php echo in_array($selectedTrangThai, ['khong_du_dieu_kien', 'huy_bo']) ? '' : 'hidden'; ?>" id="ly_do_reject_container_kc_edit">
                <label for="ly_do_tu_choi_huy_bo_kc_edit" class="form-label">Lý do từ chối/hủy bỏ <span class="text-red-500">*</span></label>
                <textarea id="ly_do_tu_choi_huy_bo_kc_edit" name="ly_do_tu_choi_huy_bo_kc" rows="2" class="form-input <?php echo isset($errors['ly_do_tu_choi_huy_bo_kc']) ? 'border-red-500' : ''; ?>"><?php echo htmlspecialchars($displayData['ly_do_tu_choi_huy_bo_kc'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                <?php if(isset($errors['ly_do_tu_choi_huy_bo_kc'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ly_do_tu_choi_huy_bo_kc']; ?></p><?php endif; ?>
            </div>
            <div class="mb-4">
                <label for="ghi_chu_hs_kc_edit" class="form-label">Ghi chú hồ sơ</label>
                <textarea id="ghi_chu_hs_kc_edit" name="ghi_chu_hs_kc" rows="2" class="form-input"><?php echo htmlspecialchars($displayData['ghi_chu_hs_kc'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
        </div>
    </div>

    <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end space-x-3">
         <a href="<?php echo url('ho-tro-khan-cap/index' . (!empty($hoTro['doi_tuong_id']) ? '?doi_tuong_id=' . $hoTro['doi_tuong_id'] : '')); ?>" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">Quay lại DS</a>
        <a href="<?php echo url('ho-tro-khan-cap/show/' . $hoTro['id']); ?>" class="btn btn-secondary">Xem chi tiết</a>
        <button type="submit" class="btn btn-primary">Cập nhật Hỗ trợ</button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const loaiHinhKCSelectEdit = document.getElementById('loai_hinh_ho_tro_kc_id_edit');
    const giaTriInputEdit = document.getElementById('gia_tri_ho_tro_tien_mat_edit');
    const hienVatInputEdit = document.getElementById('mo_ta_hien_vat_ho_tro_edit');

    if (loaiHinhKCSelectEdit) {
        loaiHinhKCSelectEdit.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const giaTri = selectedOption.getAttribute('data-gia-tri');
            const hienVat = selectedOption.getAttribute('data-hien-vat');
            
            if (giaTriInputEdit.value === '' || giaTriInputEdit.value === '0' || !isFinite(parseFloat(giaTriInputEdit.value))) {
                giaTriInputEdit.value = giaTri ? parseFloat(giaTri).toFixed(0) : '';
            }
            if (hienVatInputEdit.value === '') {
                hienVatInputEdit.value = hienVat || '';
            }
        });
    }
    
    const trangThaiSelectKCEdit = document.getElementById('trang_thai_hs_kc_edit');
    const processingFieldsDivKCEdit = document.getElementById('processing-fields-kc-edit');
    const nguoiXuLyIdInputKCEdit = document.getElementById('nguoi_xu_ly_hs_kc_id_edit');
    const nguoiXuLyDisplayInputKCEdit = document.getElementById('nguoi_xu_ly_hs_kc_id_display_edit');
    const ngayXuLyInputKCEdit = document.getElementById('ngay_xu_ly_ht_edit');
    const lyDoRejectContainerKCEdit = document.getElementById('ly_do_reject_container_kc_edit');
    const lyDoRejectInputKCEdit = document.getElementById('ly_do_tu_choi_huy_bo_kc_edit');

    const currentUserIdKCEdit = '<?php echo $currentUser['id'] ?? ""; ?>';
    const currentFullnameKCEdit = '<?php echo htmlspecialchars($currentUser['fullname'] ?? "", ENT_QUOTES, 'UTF-8'); ?>';
    const isAdminKCEdit = <?php echo ($currentUser && $currentUser['role'] == 'admin') ? 'true' : 'false'; ?>;
    const initialNguoiXuLyIdKC = '<?php echo htmlspecialchars($hoTro['nguoi_xu_ly_hs_kc_id'] ?? ($displayData['nguoi_xu_ly_hs_kc_id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>';
    const initialTenNguoiXuLyKC = '<?php echo htmlspecialchars($hoTro['ten_nguoi_xu_ly'] ?? ($displayData['ten_nguoi_xu_ly'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>';
    const initialNgayXuLyKC = '<?php echo htmlspecialchars($hoTro['ngay_xu_ly_ht'] ?? ($displayData['ngay_xu_ly_ht'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>';


    function toggleProcessingFieldsKCEdit() {
        const selectedValue = trangThaiSelectKCEdit.value;
        if (selectedValue === 'da_ho_tro' || (isAdminKCEdit && ['da_ho_tro', 'cho_duyet'].includes(selectedValue))) {
            processingFieldsDivKCEdit.classList.remove('hidden');
             if (selectedValue === 'da_ho_tro') {
                if (!nguoiXuLyIdInputKCEdit.value && initialNguoiXuLyIdKC) {
                    nguoiXuLyIdInputKCEdit.value = initialNguoiXuLyIdKC;
                } else if (!nguoiXuLyIdInputKCEdit.value && currentUserIdKCEdit) {
                    nguoiXuLyIdInputKCEdit.value = currentUserIdKCEdit;
                }

                if (!nguoiXuLyDisplayInputKCEdit.value && initialTenNguoiXuLyKC) {
                    nguoiXuLyDisplayInputKCEdit.value = initialTenNguoiXuLyKC;
                } else if (!nguoiXuLyDisplayInputKCEdit.value && currentFullnameKCEdit){
                     nguoiXuLyDisplayInputKCEdit.value = currentFullnameKCEdit;
                }
                
                if (!ngayXuLyInputKCEdit.value && initialNgayXuLyKC) {
                    ngayXuLyInputKCEdit.value = initialNgayXuLyKC;
                } else if(!ngayXuLyInputKCEdit.value) {
                    ngayXuLyInputKCEdit.value = new Date().toISOString().slice(0,10);
                }
            }
        } else {
            processingFieldsDivKCEdit.classList.add('hidden');
        }
        
        if (['khong_du_dieu_kien', 'huy_bo'].includes(selectedValue)) {
            lyDoRejectContainerKCEdit.classList.remove('hidden');
            lyDoRejectInputKCEdit.setAttribute('required', 'required');
        } else {
            lyDoRejectContainerKCEdit.classList.add('hidden');
            lyDoRejectInputKCEdit.removeAttribute('required');
        }
    }

    if(trangThaiSelectKCEdit) {
        trangThaiSelectKCEdit.addEventListener('change', toggleProcessingFieldsKCEdit);
        toggleProcessingFieldsKCEdit(); 
    }
});
</script>