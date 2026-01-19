<?php
// View: app/views/ho_so_cham_soc_cong_dong/edit.php
// Vars: $title, $hoSo, $trangThaiOptions, $loaiHinhCSOptions, $nguoiChamSocOptions, $oldData, $errors
$displayData = $oldData ?? $hoSo;
$currentUser = getCurrentUser(); 
?>
<h1 class="text-2xl font-semibold text-gray-700 mb-6"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>

<?php include __DIR__ . '/../layouts/_flash_messages.php'; ?>

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

<?php
    $ma_dt = htmlspecialchars($hoSo['ma_doi_tuong'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
    $ten_dt = htmlspecialchars($hoSo['ten_doi_tuong'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
    $ngay_sinh_dt = !empty($hoSo['ngay_sinh']) ? date('d/m/Y', strtotime($hoSo['ngay_sinh'])) : 'N/A';
    $cccd_dt = htmlspecialchars($hoSo['cccd'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
    $dia_chi_dt = htmlspecialchars($hoSo['dia_chi_thuong_tru'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
?>
<div class="mb-6 bg-blue-50 p-4 rounded-lg border border-blue-200">
    <h3 class="text-lg font-semibold text-blue-700 mb-2">Thông tin Đối tượng liên quan</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 text-sm">
        <p><strong>Họ tên:</strong> <?php echo $ten_dt; ?></p>
        <p><strong>Mã đối tượng:</strong> <?php echo $ma_dt; ?></p>
        <p><strong>Ngày sinh:</strong> <?php echo $ngay_sinh_dt; ?></p>
        <p><strong>Số CCCD/CMND:</strong> <?php echo $cccd_dt; ?></p>
        <p><strong>Địa chỉ thường trú:</strong> <?php echo $dia_chi_dt; ?></p>
    </div>
</div>

<form action="<?php echo url('ho-so-cham-soc-cong-dong/update/' . $hoSo['id']); ?>" method="POST" class="bg-white p-6 rounded-lg shadow-md">
     <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Cột 1 -->
        <div class="md:col-span-1">
            <div class="mb-4">
                <label for="ma_ho_so_cs_display" class="form-label">Mã hồ sơ CS</label>
                <input type="text" id="ma_ho_so_cs_display" name="ma_ho_so_cs_display" readonly
                       value="<?php echo htmlspecialchars($displayData['ma_ho_so_cs'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input bg-gray-100">
            </div>

            <div class="mb-4">
                <label for="loai_hinh_cham_soc_cd_id_edit" class="form-label">Loại hình chăm sóc <span class="text-red-500">*</span></label>
                <select id="loai_hinh_cham_soc_cd_id_edit" name="loai_hinh_cham_soc_cd_id" required
                        class="form-input <?php echo isset($errors['loai_hinh_cham_soc_cd_id']) ? 'border-red-500' : ''; ?>">
                    <option value="">-- Chọn loại hình --</option>
                    <?php if (isset($loaiHinhCSOptions) && !empty($loaiHinhCSOptions)): ?>
                        <?php foreach ($loaiHinhCSOptions as $lh): ?>
                            <option value="<?php echo $lh['id']; ?>" 
                                    data-kinh-phi="<?php echo htmlspecialchars($lh['kinh_phi_dinh_muc_du_kien'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo (isset($displayData['loai_hinh_cham_soc_cd_id']) && $displayData['loai_hinh_cham_soc_cd_id'] == $lh['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($lh['ten_loai_hinh'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <?php if(isset($errors['loai_hinh_cham_soc_cd_id'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['loai_hinh_cham_soc_cd_id']; ?></p><?php endif; ?>
            </div>
            
            <div class="mb-4">
                <label for="ngay_de_nghi_cs_edit" class="form-label">Ngày đề nghị <span class="text-red-500">*</span></label>
                <input type="date" id="ngay_de_nghi_cs_edit" name="ngay_de_nghi_cs" required
                       value="<?php echo htmlspecialchars($displayData['ngay_de_nghi_cs'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['ngay_de_nghi_cs']) ? 'border-red-500' : ''; ?>">
                 <?php if(isset($errors['ngay_de_nghi_cs'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ngay_de_nghi_cs']; ?></p><?php endif; ?>
            </div>
            
            <div class="mb-4">
                <label for="noi_dung_de_nghi_edit" class="form-label">Nội dung đề nghị <span class="text-red-500">*</span></label>
                <textarea id="noi_dung_de_nghi_edit" name="noi_dung_de_nghi" rows="3" required class="form-textarea <?php echo isset($errors['noi_dung_de_nghi']) ? 'border-red-500' : ''; ?>"><?php echo htmlspecialchars($displayData['noi_dung_de_nghi'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                 <?php if(isset($errors['noi_dung_de_nghi'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['noi_dung_de_nghi']; ?></p><?php endif; ?>
            </div>
        </div>
        
        <!-- Cột 2 -->
        <div class="md:col-span-1">
             <div class="mb-4">
                <label for="hinh_thuc_cham_soc_cu_the_edit" class="form-label">Hình thức chăm sóc cụ thể</label>
                <input type="text" id="hinh_thuc_cham_soc_cu_the_edit" name="hinh_thuc_cham_soc_cu_the" maxlength="255"
                       value="<?php echo htmlspecialchars($displayData['hinh_thuc_cham_soc_cu_the'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input">
            </div>

            <div class="mb-4">
                <label for="nguoi_cham_soc_id" class="form-label">Người chăm sóc chính</label>
                <select id="nguoi_cham_soc_id" name="nguoi_cham_soc_id"
                        class="form-input <?php echo isset($errors['nguoi_cham_soc_id']) ? 'border-red-500' : ''; ?>">
                    <option value="">-- Chọn người chăm sóc (nếu có) --</option>
                    <?php if (isset($nguoiChamSocOptions) && !empty($nguoiChamSocOptions)): ?>
                        <?php $selectedNCSId = $displayData['nguoi_cham_soc_id'] ?? ''; ?>
                        <?php foreach ($nguoiChamSocOptions as $ncs): ?>
                            <option value="<?php echo $ncs['id']; ?>" <?php echo ($selectedNCSId == $ncs['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ncs['ho_ten'] . (!empty($ncs['ma_nguoi_cham_soc']) ? ' (' . $ncs['ma_nguoi_cham_soc'] . ')' : '')); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <?php if(isset($errors['nguoi_cham_soc_id'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['nguoi_cham_soc_id']; ?></p><?php endif; ?>
                <p class="text-xs text-gray-500 mt-1">Chưa có trong danh sách? <a href="<?php echo url('nguoi-cham-soc/create'); ?>" target="_blank" class="text-blue-600 hover:underline">Thêm người chăm sóc mới</a>.</p>
            </div>
            
            <div class="mb-4">
                <label for="kinh_phi_du_kien_edit" class="form-label">Kinh phí dự kiến (VNĐ)</label>
                <input type="number" id="kinh_phi_du_kien_edit" name="kinh_phi_du_kien" min="0" step="1000"
                       value="<?php echo htmlspecialchars($displayData['kinh_phi_du_kien'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['kinh_phi_du_kien']) ? 'border-red-500' : ''; ?>"
                       placeholder="Để trống nếu theo định mức">
                <?php if(isset($errors['kinh_phi_du_kien'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['kinh_phi_du_kien']; ?></p><?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="ten_don_vi_thuc_hien_ngoai_edit" class="form-label">Tên đơn vị thực hiện (ngoài hệ thống)</label>
                <input type="text" id="ten_don_vi_thuc_hien_ngoai_edit" name="ten_don_vi_thuc_hien_ngoai" maxlength="255"
                       value="<?php echo htmlspecialchars($displayData['ten_don_vi_thuc_hien_ngoai'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input">
            </div>

             <div class="mb-4">
                <label for="nguon_kinh_phi_edit" class="form-label">Nguồn kinh phí</label>
                <input type="text" id="nguon_kinh_phi_edit" name="nguon_kinh_phi" maxlength="255"
                       value="<?php echo htmlspecialchars($displayData['nguon_kinh_phi'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input">
            </div>
        </div>

        <!-- Cột 3 -->
        <div class="md:col-span-1">
            <div class="mb-4">
                <label for="ngay_bat_dau_cham_soc_edit" class="form-label">Ngày bắt đầu chăm sóc</label>
                <input type="date" id="ngay_bat_dau_cham_soc_edit" name="ngay_bat_dau_cham_soc"
                       value="<?php echo htmlspecialchars($displayData['ngay_bat_dau_cham_soc'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input">
            </div>

            <div class="mb-4">
                <label for="ngay_ket_thuc_du_kien_cs_edit" class="form-label">Ngày kết thúc dự kiến</label>
                <input type="date" id="ngay_ket_thuc_du_kien_cs_edit" name="ngay_ket_thuc_du_kien_cs"
                       value="<?php echo htmlspecialchars($displayData['ngay_ket_thuc_du_kien_cs'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['ngay_ket_thuc_du_kien_cs']) ? 'border-red-500' : ''; ?>">
                <?php if(isset($errors['ngay_ket_thuc_du_kien_cs'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ngay_ket_thuc_du_kien_cs']; ?></p><?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="trang_thai_hs_cs_edit" class="form-label">Trạng thái hồ sơ <span class="text-red-500">*</span></label>
                <select id="trang_thai_hs_cs_edit" name="trang_thai_hs_cs" class="form-input <?php echo (isset($errors['trang_thai_hs_cs']) || isset($errors['nguoi_xet_duyet_hs_cs_id']) || isset($errors['ngay_xet_duyet_hs_cs']) || isset($errors['ly_do_thay_doi_trang_thai_cs'])) ? 'border-red-500' : ''; ?>">
                    <?php $selectedTrangThai = $displayData['trang_thai_hs_cs'] ?? ''; ?>
                    <?php foreach ($trangThaiOptions as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php echo ($selectedTrangThai == $value) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                 <?php if(isset($errors['trang_thai_hs_cs'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['trang_thai_hs_cs']; ?></p><?php endif; ?>
            </div>

            <?php 
                $showDuyetFieldsCSEdit = ($currentUser && $currentUser['role'] == 'admin') || ($selectedTrangThai == 'da_phe_duyet'); 
                $tenNguoiDuyetDisplayCS = $displayData['ten_nguoi_xet_duyet'] ?? '';
            ?>
            <div id="approval-fields-cs-edit" class="<?php echo $showDuyetFieldsCSEdit ? '' : 'hidden'; ?>">
                <div class="mb-4">
                    <label for="nguoi_xet_duyet_hs_cs_id_display_edit" class="form-label">Người xét duyệt</label>
                    <input type="text" id="nguoi_xet_duyet_hs_cs_id_display_edit" value="<?php echo htmlspecialchars($tenNguoiDuyetDisplayCS, ENT_QUOTES, 'UTF-8'); ?>" 
                           class="form-input bg-gray-100 <?php echo isset($errors['nguoi_xet_duyet_hs_cs_id']) ? 'border-red-500' : ''; ?>" readonly 
                           placeholder="<?php echo ($selectedTrangThai == 'da_phe_duyet') ? 'Mặc định là bạn' : 'Chỉ hiển thị khi duyệt';?>">
                    <input type="hidden" name="nguoi_xet_duyet_hs_cs_id" id="nguoi_xet_duyet_hs_cs_id_edit" value="<?php echo htmlspecialchars($displayData['nguoi_xet_duyet_hs_cs_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <?php if(isset($errors['nguoi_xet_duyet_hs_cs_id'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['nguoi_xet_duyet_hs_cs_id']; ?></p><?php endif; ?>
                </div>
                <div class="mb-4">
                    <label for="ngay_xet_duyet_hs_cs_edit" class="form-label">Ngày xét duyệt</label>
                    <input type="date" id="ngay_xet_duyet_hs_cs_edit" name="ngay_xet_duyet_hs_cs"
                           value="<?php echo htmlspecialchars($displayData['ngay_xet_duyet_hs_cs'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                           class="form-input <?php echo isset($errors['ngay_xet_duyet_hs_cs']) ? 'border-red-500' : ''; ?>">
                    <?php if(isset($errors['ngay_xet_duyet_hs_cs'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ngay_xet_duyet_hs_cs']; ?></p><?php endif; ?>
                </div>
            </div>
            <div class="mb-4 <?php echo in_array($selectedTrangThai, ['khong_du_dieu_kien', 'tam_dung', 'da_ket_thuc', 'huy_bo']) ? '' : 'hidden'; ?>" id="ly_do_thay_doi_container_cs_edit">
                <label for="ly_do_thay_doi_trang_thai_cs_edit" class="form-label">Lý do thay đổi trạng thái <span class="text-red-500">*</span></label>
                <textarea id="ly_do_thay_doi_trang_thai_cs_edit" name="ly_do_thay_doi_trang_thai_cs" rows="2" class="form-input <?php echo isset($errors['ly_do_thay_doi_trang_thai_cs']) ? 'border-red-500' : ''; ?>"><?php echo htmlspecialchars($displayData['ly_do_thay_doi_trang_thai_cs'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                <?php if(isset($errors['ly_do_thay_doi_trang_thai_cs'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ly_do_thay_doi_trang_thai_cs']; ?></p><?php endif; ?>
            </div>
             <div class="mb-4">
                <label for="ghi_chu_hs_cs_edit" class="form-label">Ghi chú hồ sơ</label>
                <textarea id="ghi_chu_hs_cs_edit" name="ghi_chu_hs_cs" rows="2" class="form-input"><?php echo htmlspecialchars($displayData['ghi_chu_hs_cs'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
        </div>
    </div>

    <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end space-x-3">
        <a href="<?php echo url('ho-so-cham-soc-cong-dong/index?doi_tuong_id=' . $hoSo['doi_tuong_id']); ?>" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">Quay lại DS Hồ sơ</a>
        <a href="<?php echo url('ho-so-cham-soc-cong-dong/show/' . $hoSo['id']); ?>" class="btn btn-secondary">Xem chi tiết</a>
        <button type="submit" class="btn btn-primary">Cập nhật Hồ sơ</button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const loaiHinhSelectEdit = document.getElementById('loai_hinh_cham_soc_cd_id_edit');
    const kinhPhiInputEdit = document.getElementById('kinh_phi_du_kien_edit');

    if (loaiHinhSelectEdit && kinhPhiInputEdit) {
        loaiHinhSelectEdit.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const kinhPhi = selectedOption.getAttribute('data-kinh-phi');
             if (kinhPhiInputEdit.value === '' || kinhPhiInputEdit.value === '0') {
                kinhPhiInputEdit.value = kinhPhi ? parseFloat(kinhPhi).toFixed(0) : '';
            }
        });
    }
    
    const trangThaiSelectCSEdit = document.getElementById('trang_thai_hs_cs_edit');
    const approvalFieldsDivCSEdit = document.getElementById('approval-fields-cs-edit');
    const nguoiXetDuyetIdInputCSEdit = document.getElementById('nguoi_xet_duyet_hs_cs_id_edit');
    const nguoiXetDuyetDisplayInputCSEdit = document.getElementById('nguoi_xet_duyet_hs_cs_id_display_edit');
    const ngayXetDuyetInputCSEdit = document.getElementById('ngay_xet_duyet_hs_cs_edit');
    const lyDoThayDoiContainerCSEdit = document.getElementById('ly_do_thay_doi_container_cs_edit');
    const lyDoThayDoiInputCSEdit = document.getElementById('ly_do_thay_doi_trang_thai_cs_edit');

    const currentUserIdCSEdit = '<?php echo $currentUser['id'] ?? ""; ?>';
    const currentFullnameCSEdit = '<?php echo htmlspecialchars($currentUser['fullname'] ?? "", ENT_QUOTES, 'UTF-8'); ?>';
    const isAdminCSEdit = <?php echo ($currentUser && $currentUser['role'] == 'admin') ? 'true' : 'false'; ?>;

    function toggleApprovalFieldsCSEdit() {
        const selectedValue = trangThaiSelectCSEdit.value;
        if (selectedValue === 'da_phe_duyet') {
            approvalFieldsDivCSEdit.classList.remove('hidden');
            if (!nguoiXetDuyetIdInputCSEdit.value && currentUserIdCSEdit) {
                nguoiXetDuyetIdInputCSEdit.value = currentUserIdCSEdit;
                nguoiXetDuyetDisplayInputCSEdit.value = currentFullnameCSEdit;
            }
            if (!ngayXetDuyetInputCSEdit.value) {
                ngayXetDuyetInputCSEdit.value = new Date().toISOString().slice(0,10);
            }
        } else {
            approvalFieldsDivCSEdit.classList.add('hidden');
        }
        
        if (['khong_du_dieu_kien', 'tam_dung', 'da_ket_thuc', 'huy_bo'].includes(selectedValue)) {
            lyDoThayDoiContainerCSEdit.classList.remove('hidden');
            lyDoThayDoiInputCSEdit.setAttribute('required', 'required');
        } else {
            lyDoThayDoiContainerCSEdit.classList.add('hidden');
            lyDoThayDoiInputCSEdit.removeAttribute('required');
        }
    }

    if(trangThaiSelectCSEdit) {
        trangThaiSelectCSEdit.addEventListener('change', toggleApprovalFieldsCSEdit);
        toggleApprovalFieldsCSEdit(); 
    }
});
</script>