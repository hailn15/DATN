<?php
// View: app/views/ho_so_cham_soc_cong_dong/create.php
// Vars: $title, $doiTuong, $trangThaiOptions, $loaiHinhCSOptions, $nguoiChamSocOptions, $oldData, $errors, $defaultTrangThai
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
    // Thông tin đối tượng
    $ma_dt = htmlspecialchars($doiTuong['ma_doi_tuong'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
    $ngay_sinh_dt = !empty($doiTuong['ngay_sinh']) ? date('d/m/Y', strtotime($doiTuong['ngay_sinh'])) : 'N/A';
    $cccd_dt = htmlspecialchars($doiTuong['cccd'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
    $dia_chi_dt = htmlspecialchars($doiTuong['dia_chi_thuong_tru'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
?>
<div class="mb-6 bg-blue-50 p-4 rounded-lg border border-blue-200">
    <h3 class="text-lg font-semibold text-blue-700 mb-2">Thông tin Đối tượng</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 text-sm">
        <p><strong>Mã đối tượng:</strong> <?php echo $ma_dt; ?></p>
        <p><strong>Ngày sinh:</strong> <?php echo $ngay_sinh_dt; ?></p>
        <p><strong>Số CCCD/CMND:</strong> <?php echo $cccd_dt; ?></p>
        <p><strong>Địa chỉ thường trú:</strong> <?php echo $dia_chi_dt; ?></p>
    </div>
</div>

<form action="<?php echo url('ho-so-cham-soc-cong-dong/store'); ?>" method="POST" class="bg-white p-6 rounded-lg shadow-md">
    <input type="hidden" name="doi_tuong_id" value="<?php echo htmlspecialchars($doiTuong['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Cột 1 -->
        <div class="md:col-span-1">
            <div class="mb-4">
                <label for="ma_ho_so_cs" class="form-label">Mã hồ sơ CS <span class="text-red-500">*</span></label>
                <input type="text" id="ma_ho_so_cs" name="ma_ho_so_cs" required maxlength="30"
                       value="<?php echo htmlspecialchars($oldData['ma_ho_so_cs'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['ma_ho_so_cs']) ? 'border-red-500' : ''; ?>">
                <?php if(isset($errors['ma_ho_so_cs'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ma_ho_so_cs']; ?></p><?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="loai_hinh_cham_soc_cd_id" class="form-label">Loại hình chăm sóc <span class="text-red-500">*</span></label>
                <select id="loai_hinh_cham_soc_cd_id" name="loai_hinh_cham_soc_cd_id" required
                        class="form-input <?php echo isset($errors['loai_hinh_cham_soc_cd_id']) ? 'border-red-500' : ''; ?>">
                    <option value="">-- Chọn loại hình --</option>
                    <?php if (isset($loaiHinhCSOptions) && !empty($loaiHinhCSOptions)): ?>
                        <?php foreach ($loaiHinhCSOptions as $lh): ?>
                            <option value="<?php echo $lh['id']; ?>" 
                                    data-kinh-phi="<?php echo htmlspecialchars($lh['kinh_phi_dinh_muc_du_kien'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo (isset($oldData['loai_hinh_cham_soc_cd_id']) && $oldData['loai_hinh_cham_soc_cd_id'] == $lh['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($lh['ten_loai_hinh'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                         <option value="" disabled>Chưa có loại hình nào</option>
                    <?php endif; ?>
                </select>
                <?php if(isset($errors['loai_hinh_cham_soc_cd_id'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['loai_hinh_cham_soc_cd_id']; ?></p><?php endif; ?>
            </div>
            
            <div class="mb-4">
                <label for="ngay_de_nghi_cs" class="form-label">Ngày đề nghị <span class="text-red-500">*</span></label>
                <input type="date" id="ngay_de_nghi_cs" name="ngay_de_nghi_cs" required
                       value="<?php echo htmlspecialchars($oldData['ngay_de_nghi_cs'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['ngay_de_nghi_cs']) ? 'border-red-500' : ''; ?>">
                 <?php if(isset($errors['ngay_de_nghi_cs'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ngay_de_nghi_cs']; ?></p><?php endif; ?>
            </div>
            
            <div class="mb-4">
                <label for="noi_dung_de_nghi" class="form-label">Nội dung đề nghị <span class="text-red-500">*</span></label>
                <textarea id="noi_dung_de_nghi" name="noi_dung_de_nghi" rows="3" required class="form-textarea <?php echo isset($errors['noi_dung_de_nghi']) ? 'border-red-500' : ''; ?>"><?php echo htmlspecialchars($oldData['noi_dung_de_nghi'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                 <?php if(isset($errors['noi_dung_de_nghi'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['noi_dung_de_nghi']; ?></p><?php endif; ?>
            </div>
        </div>
        
        <!-- Cột 2 -->
        <div class="md:col-span-1">
            <div class="mb-4">
                <label for="hinh_thuc_cham_soc_cu_the" class="form-label">Hình thức chăm sóc cụ thể</label>
                <input type="text" id="hinh_thuc_cham_soc_cu_the" name="hinh_thuc_cham_soc_cu_the" maxlength="255"
                       value="<?php echo htmlspecialchars($oldData['hinh_thuc_cham_soc_cu_the'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input">
            </div>

            <div class="mb-4">
                <label for="nguoi_cham_soc_id" class="form-label">Người chăm sóc chính</label>
                <select id="nguoi_cham_soc_id" name="nguoi_cham_soc_id"
                        class="form-input <?php echo isset($errors['nguoi_cham_soc_id']) ? 'border-red-500' : ''; ?>">
                    <option value="">-- Chọn người chăm sóc (nếu có) --</option>
                    <?php if (isset($nguoiChamSocOptions) && !empty($nguoiChamSocOptions)): ?>
                        <?php foreach ($nguoiChamSocOptions as $ncs): ?>
                            <option value="<?php echo $ncs['id']; ?>" <?php echo (isset($oldData['nguoi_cham_soc_id']) && $oldData['nguoi_cham_soc_id'] == $ncs['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ncs['ho_ten'] . (!empty($ncs['cccd']) ? ' (' . $ncs['cccd'] . ')' : '')); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <?php if(isset($errors['nguoi_cham_soc_id'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['nguoi_cham_soc_id']; ?></p><?php endif; ?>
                <p class="text-xs text-gray-500 mt-1">Chưa có trong danh sách? <a href="<?php echo url('nguoi-cham-soc/create'); ?>" target="_blank" class="text-blue-600 hover:underline">Thêm người chăm sóc mới</a>.</p>
            </div>
            
            <div class="mb-4">
                <label for="kinh_phi_du_kien" class="form-label">Kinh phí dự kiến (VNĐ)</label>
                <input type="number" id="kinh_phi_du_kien" name="kinh_phi_du_kien" min="0" step="1000"
                       value="<?php echo htmlspecialchars($oldData['kinh_phi_du_kien'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['kinh_phi_du_kien']) ? 'border-red-500' : ''; ?>"
                       placeholder="Để trống nếu theo định mức">
                <?php if(isset($errors['kinh_phi_du_kien'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['kinh_phi_du_kien']; ?></p><?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="ten_don_vi_thuc_hien_ngoai" class="form-label">Tên đơn vị thực hiện (ngoài hệ thống)</label>
                <input type="text" id="ten_don_vi_thuc_hien_ngoai" name="ten_don_vi_thuc_hien_ngoai" maxlength="255"
                       value="<?php echo htmlspecialchars($oldData['ten_don_vi_thuc_hien_ngoai'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input">
            </div>

             <div class="mb-4">
                <label for="nguon_kinh_phi" class="form-label">Nguồn kinh phí</label>
                <input type="text" id="nguon_kinh_phi" name="nguon_kinh_phi" maxlength="255"
                       value="<?php echo htmlspecialchars($oldData['nguon_kinh_phi'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input">
            </div>
        </div>

        <!-- Cột 3 -->
        <div class="md:col-span-1">
            <div class="mb-4">
                <label for="ngay_bat_dau_cham_soc" class="form-label">Ngày bắt đầu chăm sóc</label>
                <input type="date" id="ngay_bat_dau_cham_soc" name="ngay_bat_dau_cham_soc"
                       value="<?php echo htmlspecialchars($oldData['ngay_bat_dau_cham_soc'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input">
            </div>

            <div class="mb-4">
                <label for="ngay_ket_thuc_du_kien_cs" class="form-label">Ngày kết thúc dự kiến</label>
                <input type="date" id="ngay_ket_thuc_du_kien_cs" name="ngay_ket_thuc_du_kien_cs"
                       value="<?php echo htmlspecialchars($oldData['ngay_ket_thuc_du_kien_cs'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['ngay_ket_thuc_du_kien_cs']) ? 'border-red-500' : ''; ?>">
                <?php if(isset($errors['ngay_ket_thuc_du_kien_cs'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ngay_ket_thuc_du_kien_cs']; ?></p><?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="trang_thai_hs_cs" class="form-label">Trạng thái hồ sơ <span class="text-red-500">*</span></label>
                <select id="trang_thai_hs_cs" name="trang_thai_hs_cs" class="form-input <?php echo (isset($errors['trang_thai_hs_cs']) || isset($errors['nguoi_xet_duyet_hs_cs_id']) || isset($errors['ngay_xet_duyet_hs_cs']) || isset($errors['ly_do_thay_doi_trang_thai_cs'])) ? 'border-red-500' : ''; ?>">
                    <?php $selectedTrangThai = $oldData['trang_thai_hs_cs'] ?? $defaultTrangThai; ?>
                    <?php foreach ($trangThaiOptions as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php echo ($selectedTrangThai == $value) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                 <?php if(isset($errors['trang_thai_hs_cs'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['trang_thai_hs_cs']; ?></p><?php endif; ?>
            </div>

            <?php $showDuyetFieldsCS = ($currentUser && $currentUser['role'] == 'admin') || ($selectedTrangThai == 'da_phe_duyet'); ?>
            <div id="approval-fields-cs" class="<?php echo $showDuyetFieldsCS ? '' : 'hidden'; ?>">
                <div class="mb-4">
                    <label for="nguoi_xet_duyet_hs_cs_id_display" class="form-label">Người xét duyệt</label>
                    <input type="text" id="nguoi_xet_duyet_hs_cs_id_display" value="<?php echo htmlspecialchars($oldData['ten_nguoi_xet_duyet'] ?? ($selectedTrangThai == 'da_phe_duyet' ? ($currentUser['fullname'] ?? '') : ''), ENT_QUOTES, 'UTF-8'); ?>" 
                           class="form-input bg-gray-100 <?php echo isset($errors['nguoi_xet_duyet_hs_cs_id']) ? 'border-red-500' : ''; ?>" readonly 
                           placeholder="<?php echo ($selectedTrangThai == 'da_phe_duyet') ? 'Mặc định là bạn' : 'Chỉ hiển thị khi duyệt';?>">
                    <input type="hidden" name="nguoi_xet_duyet_hs_cs_id" id="nguoi_xet_duyet_hs_cs_id" value="<?php echo htmlspecialchars($oldData['nguoi_xet_duyet_hs_cs_id'] ?? ($selectedTrangThai == 'da_phe_duyet' ? ($currentUser['id'] ?? '') : ''), ENT_QUOTES, 'UTF-8'); ?>">
                    <?php if(isset($errors['nguoi_xet_duyet_hs_cs_id'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['nguoi_xet_duyet_hs_cs_id']; ?></p><?php endif; ?>
                </div>
                <div class="mb-4">
                    <label for="ngay_xet_duyet_hs_cs" class="form-label">Ngày xét duyệt</label>
                    <input type="date" id="ngay_xet_duyet_hs_cs" name="ngay_xet_duyet_hs_cs"
                           value="<?php echo htmlspecialchars($oldData['ngay_xet_duyet_hs_cs'] ?? ($selectedTrangThai == 'da_phe_duyet' ? date('Y-m-d') : ''), ENT_QUOTES, 'UTF-8'); ?>"
                           class="form-input <?php echo isset($errors['ngay_xet_duyet_hs_cs']) ? 'border-red-500' : ''; ?>">
                    <?php if(isset($errors['ngay_xet_duyet_hs_cs'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ngay_xet_duyet_hs_cs']; ?></p><?php endif; ?>
                </div>
            </div>
            <div class="mb-4 <?php echo in_array($selectedTrangThai, ['khong_du_dieu_kien', 'tam_dung', 'da_ket_thuc', 'huy_bo']) ? '' : 'hidden'; ?>" id="ly_do_thay_doi_container_cs">
                <label for="ly_do_thay_doi_trang_thai_cs" class="form-label">Lý do thay đổi trạng thái <span class="text-red-500">*</span></label>
                <textarea id="ly_do_thay_doi_trang_thai_cs" name="ly_do_thay_doi_trang_thai_cs" rows="2" class="form-input <?php echo isset($errors['ly_do_thay_doi_trang_thai_cs']) ? 'border-red-500' : ''; ?>"><?php echo htmlspecialchars($oldData['ly_do_thay_doi_trang_thai_cs'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                <?php if(isset($errors['ly_do_thay_doi_trang_thai_cs'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ly_do_thay_doi_trang_thai_cs']; ?></p><?php endif; ?>
            </div>
             <div class="mb-4">
                <label for="ghi_chu_hs_cs" class="form-label">Ghi chú hồ sơ</label>
                <textarea id="ghi_chu_hs_cs" name="ghi_chu_hs_cs" rows="2" class="form-input"><?php echo htmlspecialchars($oldData['ghi_chu_hs_cs'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
        </div>
    </div>

    <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end space-x-3">
        <a href="ho-so-cham-soc-cong-dong/index" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">Quay lại DS Hồ sơ</a>
        <button type="submit" class="btn btn-primary">Lưu Hồ sơ</button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const loaiHinhSelect = document.getElementById('loai_hinh_cham_soc_cd_id');
    const kinhPhiInput = document.getElementById('kinh_phi_du_kien');

    if (loaiHinhSelect && kinhPhiInput) {
        loaiHinhSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const kinhPhi = selectedOption.getAttribute('data-kinh-phi');
            if (kinhPhiInput.value === '' || kinhPhiInput.value === '0') {
                kinhPhiInput.value = kinhPhi ? parseFloat(kinhPhi).toFixed(0) : '';
            }
        });
        if (loaiHinhSelect.value) {
            loaiHinhSelect.dispatchEvent(new Event('change'));
        }
    }
    
    const trangThaiSelectCS = document.getElementById('trang_thai_hs_cs');
    const approvalFieldsDivCS = document.getElementById('approval-fields-cs');
    const nguoiXetDuyetIdInputCS = document.getElementById('nguoi_xet_duyet_hs_cs_id');
    const nguoiXetDuyetDisplayInputCS = document.getElementById('nguoi_xet_duyet_hs_cs_id_display');
    const ngayXetDuyetInputCS = document.getElementById('ngay_xet_duyet_hs_cs');
    const lyDoThayDoiContainerCS = document.getElementById('ly_do_thay_doi_container_cs');
    const lyDoThayDoiInputCS = document.getElementById('ly_do_thay_doi_trang_thai_cs');

    const currentUserIdCS = '<?php echo $currentUser['id'] ?? ""; ?>';
    const currentFullnameCS = '<?php echo htmlspecialchars($currentUser['fullname'] ?? "", ENT_QUOTES, 'UTF-8'); ?>';
    const isAdminCS = <?php echo ($currentUser && $currentUser['role'] == 'admin') ? 'true' : 'false'; ?>;

    function toggleApprovalFieldsCS() {
        const selectedValue = trangThaiSelectCS.value;
        if (selectedValue === 'da_phe_duyet') {
            approvalFieldsDivCS.classList.remove('hidden');
            if (!nguoiXetDuyetIdInputCS.value && currentUserIdCS) nguoiXetDuyetIdInputCS.value = currentUserIdCS;
            if (!nguoiXetDuyetDisplayInputCS.value && currentFullnameCS) nguoiXetDuyetDisplayInputCS.value = currentFullnameCS;
            if (!ngayXetDuyetInputCS.value) ngayXetDuyetInputCS.value = new Date().toISOString().slice(0,10);
        } else {
            approvalFieldsDivCS.classList.add('hidden');
        }
        
        if (['khong_du_dieu_kien', 'tam_dung', 'da_ket_thuc', 'huy_bo'].includes(selectedValue)) {
            lyDoThayDoiContainerCS.classList.remove('hidden');
            lyDoThayDoiInputCS.setAttribute('required', 'required');
        } else {
            lyDoThayDoiContainerCS.classList.add('hidden');
            lyDoThayDoiInputCS.removeAttribute('required');
        }
    }

    if(trangThaiSelectCS) {
        trangThaiSelectCS.addEventListener('change', toggleApprovalFieldsCS);
        toggleApprovalFieldsCS();
    }
});
</script>