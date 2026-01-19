<?php
// View: app/views/ho_tro_khan_cap/create.php
// Vars: $title, $doiTuong, $trangThaiOptions, $loaiHinhKCOptions, $nguoiXuLyOptions, $oldData, $errors, $defaultTrangThai
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
    $ngay_sinh_dt = !empty($doiTuong['ngay_sinh']) ? date('d/m/Y', strtotime($doiTuong['ngay_sinh'])) : 'N/A';
    $cccd_dt = htmlspecialchars($doiTuong['cccd'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
?>
<div class="mb-6 bg-blue-50 p-4 rounded-lg border border-blue-200">
    <h3 class="text-lg font-semibold text-blue-700 mb-2">Thông tin Đối tượng liên quan (Nếu có)</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 text-sm">
        <p><strong>Mã đối tượng:</strong> <?php echo $ma_dt; ?></p>
        <p><strong>Ngày sinh:</strong> <?php echo $ngay_sinh_dt; ?></p>
        <p><strong>Số CCCD/CMND:</strong> <?php echo $cccd_dt; ?></p>
    </div>
</div>
<?php endif; ?>

<form action="<?php echo url('ho-tro-khan-cap/store'); ?>" method="POST" class="bg-white p-6 rounded-lg shadow-md">
    <input type="hidden" name="doi_tuong_id" value="<?php echo htmlspecialchars($doiTuong['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Cột 1 -->
        <div class="md:col-span-1">
            <div class="mb-4">
                <label for="ma_ho_so_kc" class="form-label">Mã hồ sơ<span class="text-red-500">*</span></label>
                <input type="text" id="ma_ho_so_kc" name="ma_ho_so_kc" required maxlength="30"
                       value="<?php echo htmlspecialchars($oldData['ma_ho_so_kc'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['ma_ho_so_kc']) ? 'border-red-500' : ''; ?>">
                <?php if(isset($errors['ma_ho_so_kc'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ma_ho_so_kc']; ?></p><?php endif; ?>
            </div>
            <div class="mb-4">
                <label for="ho_ten_nguoi_nhan" class="form-label">Họ tên người nhận <span class="text-red-500">*</span></label>
                <input type="text" id="ho_ten_nguoi_nhan" name="ho_ten_nguoi_nhan" required maxlength="100"
                       value="<?php echo htmlspecialchars($oldData['ho_ten_nguoi_nhan'] ?? ($doiTuong['ho_ten'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['ho_ten_nguoi_nhan']) ? 'border-red-500' : ''; ?>">
                <?php if(isset($errors['ho_ten_nguoi_nhan'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ho_ten_nguoi_nhan']; ?></p><?php endif; ?>
            </div>
            <div class="mb-4">
                <label for="cccd_nguoi_nhan" class="form-label">Mã định danh<span class="text-red-500">*</span></label>
                <input type="text" id="cccd" name="cccd" maxlength="12" minlength="12" pattern="\d{12}" required
                    value="<?php echo htmlspecialchars($oldData['cccd'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    class="form-input <?php echo isset($errors['cccd']) ? 'border-red-500' : ''; ?>">
                    <p class="text-xs text-gray-500 mt-1">Nhập đúng 12 chữ số, ví dụ: 012345678901</p>
            </div>
             <div class="mb-4">
                <label for="sdt_nguoi_nhan" class="form-label">SĐT người nhận</label>
                <input type="text" id="sdt_nguoi_nhan" name="sdt_nguoi_nhan" maxlength="15"
                       value="<?php echo htmlspecialchars($oldData['sdt_nguoi_nhan'] ?? ($doiTuong['so_dien_thoai'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input">
            </div>
        </div>
        
        <!-- Cột 2 -->
        <div class="md:col-span-1">
            <div class="mb-4">
                <label for="dia_chi_nguoi_nhan" class="form-label">Địa chỉ người nhận</label>
                <textarea id="dia_chi_nguoi_nhan" name="dia_chi_nguoi_nhan" rows="2" class="form-textarea"><?php echo htmlspecialchars($oldData['dia_chi_nguoi_nhan'] ?? ($doiTuong['dia_chi_thuong_tru'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
            <div class="mb-4">
                <label for="loai_hinh_ho_tro_kc_id" class="form-label">Loại hình hỗ trợ <span class="text-red-500">*</span></label>
                <select id="loai_hinh_ho_tro_kc_id" name="loai_hinh_ho_tro_kc_id" required
                        class="form-input <?php echo isset($errors['loai_hinh_ho_tro_kc_id']) ? 'border-red-500' : ''; ?>">
                    <option value="">-- Chọn loại hình --</option>
                    <?php if (isset($loaiHinhKCOptions) && !empty($loaiHinhKCOptions)): ?>
                        <?php foreach ($loaiHinhKCOptions as $lh): ?>
                            <option value="<?php echo $lh['id']; ?>" 
                                    data-gia-tri="<?php echo htmlspecialchars($lh['gia_tri_ho_tro_dinh_muc'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    data-hien-vat="<?php echo htmlspecialchars($lh['mo_ta_hien_vat_dinh_muc'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo (isset($oldData['loai_hinh_ho_tro_kc_id']) && $oldData['loai_hinh_ho_tro_kc_id'] == $lh['id']) ? 'selected' : ''; ?>>
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
                <label for="ly_do_ho_tro" class="form-label">Lý do hỗ trợ <span class="text-red-500">*</span></label>
                <textarea id="ly_do_ho_tro" name="ly_do_ho_tro" rows="3" required class="form-textarea <?php echo isset($errors['ly_do_ho_tro']) ? 'border-red-500' : ''; ?>"><?php echo htmlspecialchars($oldData['ly_do_ho_tro'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                <?php if(isset($errors['ly_do_ho_tro'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ly_do_ho_tro']; ?></p><?php endif; ?>
            </div>
            <div class="mb-4">
                <label for="hinh_thuc_ho_tro_cu_the" class="form-label">Hình thức hỗ trợ cụ thể</label>
                <input type="text" id="hinh_thuc_ho_tro_cu_the" name="hinh_thuc_ho_tro_cu_the" maxlength="255"
                       value="<?php echo htmlspecialchars($oldData['hinh_thuc_ho_tro_cu_the'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input">
            </div>
        </div>

        <!-- Cột 3 -->
        <div class="md:col-span-1">
            <div class="mb-4">
                <label for="gia_tri_ho_tro_tien_mat" class="form-label">Giá trị hỗ trợ (Tiền mặt - VNĐ)</label>
                <input type="number" id="gia_tri_ho_tro_tien_mat" name="gia_tri_ho_tro_tien_mat" min="0" step="1000"
                       value="<?php echo htmlspecialchars($oldData['gia_tri_ho_tro_tien_mat'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['gia_tri_ho_tro_tien_mat']) ? 'border-red-500' : ''; ?>"
                       placeholder="Để trống nếu theo định mức">
                <?php if(isset($errors['gia_tri_ho_tro_tien_mat'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['gia_tri_ho_tro_tien_mat']; ?></p><?php endif; ?>
            </div>
            <div class="mb-4">
                <label for="mo_ta_hien_vat_ho_tro" class="form-label">Mô tả hiện vật hỗ trợ</label>
                <textarea id="mo_ta_hien_vat_ho_tro" name="mo_ta_hien_vat_ho_tro" rows="2" class="form-textarea"
                 placeholder="Để trống nếu theo định mức"><?php echo htmlspecialchars($oldData['mo_ta_hien_vat_ho_tro'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
             <div class="mb-4">
                <label for="ngay_de_xuat_ht" class="form-label">Ngày đề xuất</label>
                <input type="date" id="ngay_de_xuat_ht" name="ngay_de_xuat_ht"
                       value="<?php echo htmlspecialchars($oldData['ngay_de_xuat_ht'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input">
            </div>
            <div class="mb-4">
                <label for="trang_thai_hs_kc" class="form-label">Trạng thái hồ sơ <span class="text-red-500">*</span></label>
                <select id="trang_thai_hs_kc" name="trang_thai_hs_kc" class="form-input <?php echo (isset($errors['trang_thai_hs_kc']) || isset($errors['nguoi_xu_ly_hs_kc_id']) || isset($errors['ngay_xu_ly_ht']) || isset($errors['ly_do_tu_choi_huy_bo_kc'])) ? 'border-red-500' : ''; ?>">
                    <?php $selectedTrangThai = $oldData['trang_thai_hs_kc'] ?? $defaultTrangThai; ?>
                    <?php foreach ($trangThaiOptions as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php echo ($selectedTrangThai == $value) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if(isset($errors['trang_thai_hs_kc'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['trang_thai_hs_kc']; ?></p><?php endif; ?>
            </div>

            <?php $showXuLyFieldsKC = ($currentUser && $currentUser['role'] == 'admin') || ($selectedTrangThai == 'da_ho_tro'); ?>
            <div id="processing-fields-kc" class="<?php echo $showXuLyFieldsKC ? '' : 'hidden'; ?>">
                <div class="mb-4">
                    <label for="nguoi_xu_ly_hs_kc_id_display" class="form-label">Người xử lý</label>
                    <input type="text" id="nguoi_xu_ly_hs_kc_id_display" value="<?php echo htmlspecialchars($oldData['ten_nguoi_xu_ly'] ?? ($selectedTrangThai == 'da_ho_tro' ? ($currentUser['fullname'] ?? '') : ''), ENT_QUOTES, 'UTF-8'); ?>" 
                           class="form-input bg-gray-100 <?php echo isset($errors['nguoi_xu_ly_hs_kc_id']) ? 'border-red-500' : ''; ?>" readonly 
                           placeholder="<?php echo ($selectedTrangThai == 'da_ho_tro') ? 'Mặc định là bạn' : 'Chỉ hiển thị khi xử lý';?>">
                    <input type="hidden" name="nguoi_xu_ly_hs_kc_id" id="nguoi_xu_ly_hs_kc_id" value="<?php echo htmlspecialchars($oldData['nguoi_xu_ly_hs_kc_id'] ?? ($selectedTrangThai == 'da_ho_tro' ? ($currentUser['id'] ?? '') : ''), ENT_QUOTES, 'UTF-8'); ?>">
                    <?php if(isset($errors['nguoi_xu_ly_hs_kc_id'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['nguoi_xu_ly_hs_kc_id']; ?></p><?php endif; ?>
                </div>
                <div class="mb-4">
                    <label for="ngay_xu_ly_ht" class="form-label">Ngày xử lý</label>
                    <input type="date" id="ngay_xu_ly_ht" name="ngay_xu_ly_ht"
                           value="<?php echo htmlspecialchars($oldData['ngay_xu_ly_ht'] ?? ($selectedTrangThai == 'da_ho_tro' ? date('Y-m-d') : ''), ENT_QUOTES, 'UTF-8'); ?>"
                           class="form-input <?php echo isset($errors['ngay_xu_ly_ht']) ? 'border-red-500' : ''; ?>">
                     <?php if(isset($errors['ngay_xu_ly_ht'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ngay_xu_ly_ht']; ?></p><?php endif; ?>
                </div>
            </div>
            <div class="mb-4 <?php echo in_array($selectedTrangThai, ['khong_du_dieu_kien', 'huy_bo']) ? '' : 'hidden'; ?>" id="ly_do_reject_container_kc">
                <label for="ly_do_tu_choi_huy_bo_kc" class="form-label">Lý do từ chối/hủy bỏ <span class="text-red-500">*</span></label>
                <textarea id="ly_do_tu_choi_huy_bo_kc" name="ly_do_tu_choi_huy_bo_kc" rows="2" class="form-input <?php echo isset($errors['ly_do_tu_choi_huy_bo_kc']) ? 'border-red-500' : ''; ?>"><?php echo htmlspecialchars($oldData['ly_do_tu_choi_huy_bo_kc'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                <?php if(isset($errors['ly_do_tu_choi_huy_bo_kc'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ly_do_tu_choi_huy_bo_kc']; ?></p><?php endif; ?>
            </div>
             <div class="mb-4">
                <label for="ghi_chu_hs_kc" class="form-label">Ghi chú hồ sơ</label>
                <textarea id="ghi_chu_hs_kc" name="ghi_chu_hs_kc" rows="2" class="form-input"><?php echo htmlspecialchars($oldData['ghi_chu_hs_kc'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
        </div>
    </div>

    <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end space-x-3">
         <a href="<?php echo url('ho-tro-khan-cap/index' . (!empty($doiTuong['id']) ? '?doi_tuong_id=' . $doiTuong['id'] : '')); ?>" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">Quay lại DS</a>
        <button type="submit" class="btn btn-primary">Lưu Hỗ trợ</button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const loaiHinhKCSelect = document.getElementById('loai_hinh_ho_tro_kc_id');
    const giaTriInput = document.getElementById('gia_tri_ho_tro_tien_mat');
    const hienVatInput = document.getElementById('mo_ta_hien_vat_ho_tro');

    if (loaiHinhKCSelect) {
        loaiHinhKCSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const giaTri = selectedOption.getAttribute('data-gia-tri');
            const hienVat = selectedOption.getAttribute('data-hien-vat');
            
            if (giaTriInput.value === '' || giaTriInput.value === '0' || !isFinite(parseFloat(giaTriInput.value))) {
                giaTriInput.value = giaTri ? parseFloat(giaTri).toFixed(0) : '';
            }
            if (hienVatInput.value === '') {
                hienVatInput.value = hienVat || '';
            }
        });
         if (loaiHinhKCSelect.value) { // Trigger on load if pre-selected
            loaiHinhKCSelect.dispatchEvent(new Event('change'));
        }
    }
    
    const trangThaiSelectKC = document.getElementById('trang_thai_hs_kc');
    const processingFieldsDivKC = document.getElementById('processing-fields-kc');
    const nguoiXuLyIdInputKC = document.getElementById('nguoi_xu_ly_hs_kc_id');
    const nguoiXuLyDisplayInputKC = document.getElementById('nguoi_xu_ly_hs_kc_id_display');
    const ngayXuLyInputKC = document.getElementById('ngay_xu_ly_ht');
    const lyDoRejectContainerKC = document.getElementById('ly_do_reject_container_kc');
    const lyDoRejectInputKC = document.getElementById('ly_do_tu_choi_huy_bo_kc');

    const currentUserIdKC = '<?php echo $currentUser['id'] ?? ""; ?>';
    const currentFullnameKC = '<?php echo htmlspecialchars($currentUser['fullname'] ?? "", ENT_QUOTES, 'UTF-8'); ?>';
    const isAdminKC = <?php echo ($currentUser && $currentUser['role'] == 'admin') ? 'true' : 'false'; ?>;

    function toggleProcessingFieldsKC() {
        const selectedValue = trangThaiSelectKC.value;
        if (selectedValue === 'da_ho_tro' || (isAdminKC && ['da_ho_tro', 'cho_duyet'].includes(selectedValue))) {
            processingFieldsDivKC.classList.remove('hidden');
             if (selectedValue === 'da_ho_tro') {
                 if (!nguoiXuLyIdInputKC.value && currentUserIdKC) nguoiXuLyIdInputKC.value = currentUserIdKC;
                 if (!nguoiXuLyDisplayInputKC.value && currentFullnameKC) nguoiXuLyDisplayInputKC.value = currentFullnameKC;
                 if (!ngayXuLyInputKC.value) ngayXuLyInputKC.value = new Date().toISOString().slice(0,10);
            }
        } else {
            processingFieldsDivKC.classList.add('hidden');
        }
        
        if (['khong_du_dieu_kien', 'huy_bo'].includes(selectedValue)) {
            lyDoRejectContainerKC.classList.remove('hidden');
            lyDoRejectInputKC.setAttribute('required', 'required');
        } else {
            lyDoRejectContainerKC.classList.add('hidden');
            lyDoRejectInputKC.removeAttribute('required');
        }
    }

    if(trangThaiSelectKC) {
        trangThaiSelectKC.addEventListener('change', toggleProcessingFieldsKC);
        toggleProcessingFieldsKC();
    }
});
</script>