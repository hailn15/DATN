<?php
// View: app/views/ho_so_tro_cap/create.php
// Vars: $title, $doiTuong, $trangThaiOptions, $mucTroCapOptions, $oldData, $errors, $defaultTrangThai
$currentUser = getCurrentUser(); 
?>
<h1 class="text-2xl font-semibold text-gray-700 mb-6"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>

<?php if (!empty($errors)): ?>
<div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-400 rounded">
    <strong class="font-bold">Có lỗi xảy ra:</strong>
    <ul class="list-disc list-inside ml-4">
        <?php foreach ($errors as $field => $error): // Thay đổi để hiển thị lỗi theo field nếu có ?>
            <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<?php
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


<form action="<?php echo url('ho-so-tro-cap/store'); ?>" method="POST" class="bg-white p-6 rounded-lg shadow-md">
    <input type="hidden" name="doi_tuong_id" value="<?php echo htmlspecialchars($doiTuong['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Cột 1 -->
        <div>
            <div class="mb-4">
                <label for="ma_ho_so" class="form-label">Mã hồ sơ <span class="text-red-500">*</span></label>
                <input type="text" id="ma_ho_so" name="ma_ho_so" required maxlength="30"
                       value="<?php echo htmlspecialchars($oldData['ma_ho_so'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['ma_ho_so']) ? 'border-red-500' : ''; ?>">
                <?php if(isset($errors['ma_ho_so'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ma_ho_so']; ?></p><?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="muc_tro_cap_id" class="form-label">Mức Trợ Cấp Hàng Tháng <span class="text-red-500">*</span></label>
                <select id="muc_tro_cap_id" name="muc_tro_cap_id" required
                        class="form-input <?php echo isset($errors['muc_tro_cap_id']) ? 'border-red-500' : ''; ?>">
                    <option value="">-- Chọn Mức Trợ Cấp --</option>
                    <?php if (isset($mucTroCapOptions) && !empty($mucTroCapOptions)): ?>
                        <?php foreach ($mucTroCapOptions as $muc): ?>
                            <option value="<?php echo $muc['id']; ?>" 
                                    data-so-tien="<?php echo htmlspecialchars($muc['so_tien_ap_dung'], ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo (isset($oldData['muc_tro_cap_id']) && $oldData['muc_tro_cap_id'] == $muc['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($muc['ten_muc'] . ' (' . number_format($muc['so_tien_ap_dung'], 0, ',', '.') . ' đ)', ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>Chưa có mức trợ cấp nào</option>
                    <?php endif; ?>
                </select>
                <?php if(isset($errors['muc_tro_cap_id'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['muc_tro_cap_id']; ?></p><?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="ngay_de_nghi_huong" class="form-label">Ngày tiếp nhận <span class="text-red-500">*</span></label>
                <input type="date" id="ngay_de_nghi_huong" name="ngay_de_nghi_huong" required
                       value="<?php echo htmlspecialchars($oldData['ngay_de_nghi_huong'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['ngay_de_nghi_huong']) ? 'border-red-500' : ''; ?>">
                <?php if(isset($errors['ngay_de_nghi_huong'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ngay_de_nghi_huong']; ?></p><?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="ngay_bat_dau_huong" class="form-label">Ngày bắt đầu hưởng </label>
                <input type="date" id="ngay_bat_dau_huong" name="ngay_bat_dau_huong" 
                       value="<?php echo htmlspecialchars($oldData['ngay_bat_dau_huong'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['ngay_bat_dau_huong']) ? 'border-red-500' : ''; ?>">
                <?php if(isset($errors['ngay_bat_dau_huong'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ngay_bat_dau_huong']; ?></p><?php endif; ?>
            </div>
            
            <div class="mb-4">
                <label for="ngay_ket_thuc_huong" class="form-label">Ngày kết thúc hưởng (nếu đã tạm dừng/thay đổi)</label>
                <input type="date" id="ngay_ket_thuc_huong" name="ngay_ket_thuc_huong"
                       value="<?php echo htmlspecialchars($oldData['ngay_ket_thuc_huong'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['ngay_ket_thuc_huong']) ? 'border-red-500' : ''; ?>">
                 <?php if(isset($errors['ngay_ket_thuc_huong'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ngay_ket_thuc_huong']; ?></p><?php endif; ?>
            </div>

            <!-- Số tiền hưởng này sẽ được controller gán, không cho người dùng nhập trực tiếp -->
            <input type="hidden" id="muc_tro_cap_hang_thang" name="muc_tro_cap_hang_thang" 
                   value="<?php echo htmlspecialchars($oldData['muc_tro_cap_hang_thang'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <div class="mb-4">
                <label class="form-label">Số tiền hưởng dự kiến (VNĐ)</label>
                <input type="text" id="muc_tro_cap_hang_thang_display" readonly
                       value="<?php echo !empty($oldData['muc_tro_cap_hang_thang']) ? number_format($oldData['muc_tro_cap_hang_thang'],0,',','.') : ''; ?>"
                       class="form-input bg-gray-100"
                       placeholder="Sẽ tự động điền khi chọn Mức trợ cấp">
            </div>

        </div>

        <!-- Cột 2 -->
        <div>
            <div class="mb-4">
                <label for="ly_do_tro_cap" class="form-label">Lý do/Căn cứ trợ cấp</label>
                <textarea id="ly_do_tro_cap" name="ly_do_tro_cap" rows="3" class="form-input"><?php echo htmlspecialchars($oldData['ly_do_tro_cap'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
            
            <div class="mb-4">
                <label for="trang_thai" class="form-label">Trạng thái hồ sơ <span class="text-red-500">*</span></label>
                <select id="trang_thai" name="trang_thai" class="form-input <?php echo (isset($errors['trang_thai']) || isset($errors['nguoi_duyet_id']) || isset($errors['ngay_duyet']) || isset($errors['ly_do_thay_doi_trang_thai'])) ? 'border-red-500' : ''; ?>">
                    <?php 
                        $selectedTrangThai = $oldData['trang_thai'] ?? $defaultTrangThai;
                    ?>
                    <?php foreach ($trangThaiOptions as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php echo ($selectedTrangThai == $value) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if(isset($errors['trang_thai'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['trang_thai']; ?></p><?php endif; ?>
            </div>

            <?php
                $showDuyetFields = ($currentUser && $currentUser['role'] == 'admin') || ($selectedTrangThai == 'da_phe_duyet_dang_huong');
            ?>
            <div id="approval-fields" class="<?php echo $showDuyetFields ? '' : 'hidden'; ?>">
                <div class="mb-4">
                    <label for="nguoi_duyet_id_display" class="form-label">Người duyệt</label>
                    <input type="text" id="nguoi_duyet_display" value="<?php echo htmlspecialchars($oldData['ten_nguoi_duyet'] ?? ($selectedTrangThai == 'da_phe_duyet_dang_huong' ? ($currentUser['fullname'] ?? '') : ''), ENT_QUOTES, 'UTF-8'); ?>" 
                           class="form-input bg-gray-100 <?php echo isset($errors['nguoi_duyet_id']) ? 'border-red-500' : ''; ?>" readonly 
                           placeholder="<?php echo ($selectedTrangThai == 'da_phe_duyet_dang_huong') ? 'Mặc định là bạn' : 'Chỉ hiển thị khi duyệt';?>">
                    <input type="hidden" name="nguoi_duyet_id" id="nguoi_duyet_id" value="<?php echo htmlspecialchars($oldData['nguoi_duyet_id'] ?? ($selectedTrangThai == 'da_phe_duyet_dang_huong' ? ($currentUser['id'] ?? '') : ''), ENT_QUOTES, 'UTF-8'); ?>">
                     <?php if(isset($errors['nguoi_duyet_id'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['nguoi_duyet_id']; ?></p><?php endif; ?>
                </div>
                <div class="mb-4">
                    <label for="ngay_duyet" class="form-label">Ngày duyệt</label>
                    <input type="date" id="ngay_duyet" name="ngay_duyet"
                           value="<?php echo htmlspecialchars($oldData['ngay_duyet'] ?? ($selectedTrangThai == 'da_phe_duyet_dang_huong' ? date('Y-m-d') : ''), ENT_QUOTES, 'UTF-8'); ?>"
                           class="form-input <?php echo isset($errors['ngay_duyet']) ? 'border-red-500' : ''; ?>">
                    <?php if(isset($errors['ngay_duyet'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ngay_duyet']; ?></p><?php endif; ?>
                </div>
            </div>
            <div class="mb-4 <?php echo in_array($selectedTrangThai, ['khong_du_dieu_kien', 'tam_dung_huong', 'da_dung_huong']) ? '' : 'hidden'; ?>" id="ly_do_thay_doi_container" >
                <label for="ly_do_thay_doi_trang_thai" class="form-label">Lý do thay đổi trạng thái <span class="text-red-500">*</span></label>
                <textarea id="ly_do_thay_doi_trang_thai" name="ly_do_thay_doi_trang_thai" rows="2" class="form-input <?php echo isset($errors['ly_do_thay_doi_trang_thai']) ? 'border-red-500' : ''; ?>"><?php echo htmlspecialchars($oldData['ly_do_thay_doi_trang_thai'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                <?php if(isset($errors['ly_do_thay_doi_trang_thai'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ly_do_thay_doi_trang_thai']; ?></p><?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="ghi_chu_hs" class="form-label">Ghi chú hồ sơ</label>
                <textarea id="ghi_chu_hs" name="ghi_chu_hs" rows="2" class="form-input"><?php echo htmlspecialchars($oldData['ghi_chu_hs'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>

        </div>
    </div>

    <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end space-x-3">
        <a href="<?php echo url('ho-so-tro-cap/index?doi_tuong_id=' . $doiTuong['id']); ?>" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">Quay lại DS Hồ sơ</a>
        <button type="submit" class="btn btn-primary">Lưu Hồ sơ</button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const mucTroCapSelect = document.getElementById('muc_tro_cap_id');
    const soTienInputHidden = document.getElementById('muc_tro_cap_hang_thang'); // Input ẩn
    const soTienDisplay = document.getElementById('muc_tro_cap_hang_thang_display'); // Input hiển thị

    if (mucTroCapSelect && soTienInputHidden && soTienDisplay) {
        mucTroCapSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const soTien = selectedOption.getAttribute('data-so-tien');
            const soTienValue = soTien ? parseFloat(soTien).toFixed(0) : '';
            
            soTienInputHidden.value = soTienValue;
            soTienDisplay.value = soTienValue ? new Intl.NumberFormat('vi-VN').format(soTienValue) : '';
        });

        if (mucTroCapSelect.value) {
            mucTroCapSelect.dispatchEvent(new Event('change'));
        }
    }
    
    const trangThaiSelect = document.getElementById('trang_thai');
    const approvalFieldsDiv = document.getElementById('approval-fields');
    const nguoiDuyetIdInput = document.getElementById('nguoi_duyet_id');
    const nguoiDuyetDisplayInput = document.getElementById('nguoi_duyet_display');
    const ngayDuyetInput = document.getElementById('ngay_duyet');
    const lyDoThayDoiContainer = document.getElementById('ly_do_thay_doi_container');
    const lyDoThayDoiInput = document.getElementById('ly_do_thay_doi_trang_thai');

    const currentUserId = '<?php echo $currentUser['id'] ?? ""; ?>';
    const currentFullname = '<?php echo htmlspecialchars($currentUser['fullname'] ?? "", ENT_QUOTES, 'UTF-8'); ?>';
    const isAdmin = <?php echo ($currentUser && $currentUser['role'] == 'admin') ? 'true' : 'false'; ?>;

    function toggleApprovalFields() {
        const selectedValue = trangThaiSelect.value;
        if (selectedValue === 'da_phe_duyet_dang_huong' || (isAdmin && ['da_phe_duyet_dang_huong', 'cho_duyet'].includes(selectedValue))) {
            approvalFieldsDiv.classList.remove('hidden');
            if (selectedValue === 'da_phe_duyet_dang_huong') {
                 if (!nguoiDuyetIdInput.value && currentUserId) nguoiDuyetIdInput.value = currentUserId; // Chỉ gán nếu rỗng
                 if (!nguoiDuyetDisplayInput.value && currentFullname) nguoiDuyetDisplayInput.value = currentFullname; // Chỉ gán nếu rỗng
                 if (!ngayDuyetInput.value) ngayDuyetInput.value = new Date().toISOString().slice(0,10); // Chỉ gán nếu rỗng
            }
        } else {
            approvalFieldsDiv.classList.add('hidden');
        }
        
        if (['khong_du_dieu_kien', 'tam_dung_huong', 'da_dung_huong'].includes(selectedValue)) {
            lyDoThayDoiContainer.classList.remove('hidden');
            lyDoThayDoiInput.setAttribute('required', 'required');
        } else {
            lyDoThayDoiContainer.classList.add('hidden');
            lyDoThayDoiInput.removeAttribute('required');
        }
    }

    if(trangThaiSelect) {
        trangThaiSelect.addEventListener('change', toggleApprovalFields);
        toggleApprovalFields();
    }
});
</script>