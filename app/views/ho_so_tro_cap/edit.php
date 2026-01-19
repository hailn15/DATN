<?php
// View: app/views/ho_so_tro_cap/edit.php
// Vars: $title, $hoSo, $trangThaiOptions, $mucTroCapOptions, $oldData, $errors
$displayData = $oldData ?? $hoSo;
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

<form action="<?php echo url('ho-so-tro-cap/update/' . $hoSo['id']); ?>" method="POST" class="bg-white p-6 rounded-lg shadow-md">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Cột 1 -->
        <div>
            <div class="mb-4">
                <label for="ma_ho_so_display" class="form-label">Mã hồ sơ</label>
                <input type="text" id="ma_ho_so_display" name="ma_ho_so_display" readonly
                       value="<?php echo htmlspecialchars($displayData['ma_ho_so'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input bg-gray-100">
            </div>

            <div class="mb-4">
                <label for="muc_tro_cap_id_edit" class="form-label">Mức Trợ Cấp Hàng Tháng <span class="text-red-500">*</span></label>
                <select id="muc_tro_cap_id_edit" name="muc_tro_cap_id" required
                        class="form-input <?php echo isset($errors['muc_tro_cap_id']) ? 'border-red-500' : ''; ?>">
                    <option value="">-- Chọn Mức Trợ Cấp --</option>
                    <?php if (isset($mucTroCapOptions) && !empty($mucTroCapOptions)): ?>
                        <?php foreach ($mucTroCapOptions as $muc): ?>
                            <option value="<?php echo $muc['id']; ?>" 
                                    data-so-tien="<?php echo htmlspecialchars($muc['so_tien_ap_dung'], ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo (isset($displayData['muc_tro_cap_id']) && $displayData['muc_tro_cap_id'] == $muc['id']) ? 'selected' : ''; ?>>
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
                <label for="ngay_de_nghi_huong_edit" class="form-label">Ngày đề nghị hưởng <span class="text-red-500">*</span></label>
                <input type="date" id="ngay_de_nghi_huong_edit" name="ngay_de_nghi_huong" required
                       value="<?php echo htmlspecialchars($displayData['ngay_de_nghi_huong'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['ngay_de_nghi_huong']) ? 'border-red-500' : ''; ?>">
                <?php if(isset($errors['ngay_de_nghi_huong'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ngay_de_nghi_huong']; ?></p><?php endif; ?>
            </div>


            <div class="mb-4">
                <label for="ngay_bat_dau_huong_edit" class="form-label">Ngày bắt đầu hưởng </label>
                <input type="date" id="ngay_bat_dau_huong_edit" name="ngay_bat_dau_huong" 
                       value="<?php echo htmlspecialchars($displayData['ngay_bat_dau_huong'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['ngay_bat_dau_huong']) ? 'border-red-500' : ''; ?>">
                 <?php if(isset($errors['ngay_bat_dau_huong'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ngay_bat_dau_huong']; ?></p><?php endif; ?>
            </div>
            
            <div class="mb-4">
                <label for="ngay_ket_thuc_huong_edit" class="form-label">Ngày kết thúc hưởng (nếu có)</label>
                <input type="date" id="ngay_ket_thuc_huong_edit" name="ngay_ket_thuc_huong"
                       value="<?php echo htmlspecialchars($displayData['ngay_ket_thuc_huong'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['ngay_ket_thuc_huong']) ? 'border-red-500' : ''; ?>">
                <?php if(isset($errors['ngay_ket_thuc_huong'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ngay_ket_thuc_huong']; ?></p><?php endif; ?>
            </div>

            <!-- Số tiền hưởng này sẽ được controller gán, không cho người dùng nhập trực tiếp TRỪ KHI bạn muốn cho phép override -->
            <input type="hidden" id="muc_tro_cap_hang_thang_edit_hidden" name="muc_tro_cap_hang_thang" 
                   value="<?php echo htmlspecialchars($displayData['muc_tro_cap_hang_thang'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <div class="mb-4">
                <label class="form-label">Số tiền hưởng thực tế (VNĐ)</label>
                <input type="text" id="muc_tro_cap_hang_thang_edit_display" readonly
                       value="<?php echo !empty($displayData['muc_tro_cap_hang_thang']) ? number_format($displayData['muc_tro_cap_hang_thang'],0,',','.') : ''; ?>"
                       class="form-input bg-gray-100 <?php echo isset($errors['muc_tro_cap_hang_thang']) ? 'border-red-500' : ''; ?>"
                       placeholder="Sẽ tự động điền khi chọn Mức trợ cấp">
                 <small class="text-xs text-gray-500">Số tiền này sẽ được lấy từ Mức Trợ Cấp đã chọn (trừ khi logic cho phép sửa trực tiếp).</small>
            </div>

        </div>

        <!-- Cột 2 -->
        <div>
             <div class="mb-4">
                <label for="ly_do_tro_cap_edit" class="form-label">Lý do/Căn cứ trợ cấp</label>
                <textarea id="ly_do_tro_cap_edit" name="ly_do_tro_cap" rows="3" class="form-input"><?php echo htmlspecialchars($displayData['ly_do_tro_cap'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
            
            <div class="mb-4">
                <label for="trang_thai_edit" class="form-label">Trạng thái hồ sơ <span class="text-red-500">*</span></label>
                <select id="trang_thai_edit" name="trang_thai" class="form-input <?php echo (isset($errors['trang_thai']) || isset($errors['nguoi_duyet_id']) || isset($errors['ngay_duyet']) || isset($errors['ly_do_thay_doi_trang_thai'])) ? 'border-red-500' : ''; ?>">
                     <?php $selectedTrangThai = $displayData['trang_thai'] ?? ''; ?>
                    <?php foreach ($trangThaiOptions as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php echo ($selectedTrangThai == $value) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                 <?php if(isset($errors['trang_thai'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['trang_thai']; ?></p><?php endif; ?>
            </div>
             <?php
                $showDuyetFieldsEdit = ($currentUser && $currentUser['role'] == 'admin') || ($selectedTrangThai == 'da_phe_duyet_dang_huong');
                $tenNguoiDuyetDisplay = $displayData['ten_nguoi_duyet'] ?? '';
                if ($selectedTrangThai == 'da_phe_duyet_dang_huong' && empty($tenNguoiDuyetDisplay) && isset($displayData['nguoi_duyet_id']) && $displayData['nguoi_duyet_id'] == $currentUser['id']) {
                    $tenNguoiDuyetDisplay = $currentUser['fullname'];
                } elseif (empty($tenNguoiDuyetDisplay) && $selectedTrangThai == 'da_phe_duyet_dang_huong') {
                    $tenNguoiDuyetDisplay = $currentUser['fullname'] ?? ''; // Mặc định là user hiện tại nếu duyệt
                }

            ?>
             <div id="approval-fields-edit" class="<?php echo $showDuyetFieldsEdit ? '' : 'hidden'; ?>">
                <div class="mb-4">
                    <label for="nguoi_duyet_display_edit_label" class="form-label">Người duyệt</label>
                     <input type="text" id="nguoi_duyet_display_edit" value="<?php echo htmlspecialchars($tenNguoiDuyetDisplay, ENT_QUOTES, 'UTF-8'); ?>" 
                           class="form-input bg-gray-100 <?php echo isset($errors['nguoi_duyet_id']) ? 'border-red-500' : ''; ?>" readonly
                           placeholder="<?php echo ($selectedTrangThai == 'da_phe_duyet_dang_huong') ? 'Mặc định là bạn nếu chưa có' : 'Chỉ hiển thị khi duyệt';?>">
                    <input type="hidden" name="nguoi_duyet_id" id="nguoi_duyet_id_edit" value="<?php echo htmlspecialchars($displayData['nguoi_duyet_id'] ?? ($selectedTrangThai == 'da_phe_duyet_dang_huong' ? ($currentUser['id'] ?? '') : ''), ENT_QUOTES, 'UTF-8'); ?>">
                     <?php if(isset($errors['nguoi_duyet_id'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['nguoi_duyet_id']; ?></p><?php endif; ?>
                </div>
                <div class="mb-4">
                    <label for="ngay_duyet_edit_label" class="form-label">Ngày duyệt</label>
                    <input type="date" id="ngay_duyet_edit" name="ngay_duyet"
                           value="<?php echo htmlspecialchars($displayData['ngay_duyet'] ?? ($selectedTrangThai == 'da_phe_duyet_dang_huong' ? date('Y-m-d') : ''), ENT_QUOTES, 'UTF-8'); ?>"
                           class="form-input <?php echo isset($errors['ngay_duyet']) ? 'border-red-500' : ''; ?>">
                    <?php if(isset($errors['ngay_duyet'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ngay_duyet']; ?></p><?php endif; ?>
                </div>
            </div>
            <div class="mb-4 <?php echo in_array($selectedTrangThai, ['khong_du_dieu_kien', 'tam_dung_huong', 'da_dung_huong']) ? '' : 'hidden'; ?>" id="ly_do_thay_doi_container_edit">
                <label for="ly_do_thay_doi_trang_thai_edit" class="form-label">Lý do thay đổi trạng thái <span class="text-red-500">*</span></label>
                <textarea id="ly_do_thay_doi_trang_thai_edit" name="ly_do_thay_doi_trang_thai" rows="2" class="form-input <?php echo isset($errors['ly_do_thay_doi_trang_thai']) ? 'border-red-500' : ''; ?>"><?php echo htmlspecialchars($displayData['ly_do_thay_doi_trang_thai'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                 <?php if(isset($errors['ly_do_thay_doi_trang_thai'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ly_do_thay_doi_trang_thai']; ?></p><?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="ghi_chu_hs_edit" class="form-label">Ghi chú hồ sơ</label>
                <textarea id="ghi_chu_hs_edit" name="ghi_chu_hs" rows="2" class="form-input"><?php echo htmlspecialchars($displayData['ghi_chu_hs'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
        </div>
    </div>

    <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end space-x-3">
        <a href="<?php echo url('ho-so-tro-cap/index?doi_tuong_id=' . $hoSo['doi_tuong_id']); ?>" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">Quay lại DS Hồ sơ</a>
        <a href="<?php echo url('ho-so-tro-cap/show/' . $hoSo['id']); ?>" class="btn btn-secondary">Xem chi tiết</a>
        <button type="submit" class="btn btn-primary">Cập nhật Hồ sơ</button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const mucTroCapSelectEdit = document.getElementById('muc_tro_cap_id_edit');
    const soTienInputHiddenEdit = document.getElementById('muc_tro_cap_hang_thang_edit_hidden');
    const soTienDisplayEdit = document.getElementById('muc_tro_cap_hang_thang_edit_display');

    if (mucTroCapSelectEdit && soTienInputHiddenEdit && soTienDisplayEdit) {
        mucTroCapSelectEdit.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const soTien = selectedOption.getAttribute('data-so-tien');
            const soTienValue = soTien ? parseFloat(soTien).toFixed(0) : '';
            
            soTienInputHiddenEdit.value = soTienValue;
            soTienDisplayEdit.value = soTienValue ? new Intl.NumberFormat('vi-VN').format(soTienValue) : '';
        });
        // Không trigger change on load ở edit để giữ giá trị từ DB nếu không đổi Mức
    }
    
    const trangThaiSelectEditJS = document.getElementById('trang_thai_edit');
    const approvalFieldsDivEditJS = document.getElementById('approval-fields-edit');
    const nguoiDuyetIdInputEditJS = document.getElementById('nguoi_duyet_id_edit');
    const nguoiDuyetDisplayInputEditJS = document.getElementById('nguoi_duyet_display_edit');
    const ngayDuyetInputEditJS = document.getElementById('ngay_duyet_edit');
    const lyDoThayDoiContainerEditJS = document.getElementById('ly_do_thay_doi_container_edit');
    const lyDoThayDoiInputEditJS = document.getElementById('ly_do_thay_doi_trang_thai_edit');


    const currentUserIdEditJS = '<?php echo $currentUser['id'] ?? ""; ?>';
    const currentFullnameEditJS = '<?php echo htmlspecialchars($currentUser['fullname'] ?? "", ENT_QUOTES, 'UTF-8'); ?>';
    const isAdminEditJS = <?php echo ($currentUser && $currentUser['role'] == 'admin') ? 'true' : 'false'; ?>;
    const initialNguoiDuyetIdJS = '<?php echo htmlspecialchars($hoSo['nguoi_duyet_id'] ?? ($displayData['nguoi_duyet_id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>';
    const initialTenNguoiDuyetJS = '<?php echo htmlspecialchars($hoSo['ten_nguoi_duyet'] ?? ($displayData['ten_nguoi_duyet'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>';
    const initialNgayDuyetJS = '<?php echo htmlspecialchars($hoSo['ngay_duyet'] ?? ($displayData['ngay_duyet'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>';


    function toggleApprovalFieldsEdit() {
        const selectedValue = trangThaiSelectEditJS.value;
        if (selectedValue === 'da_phe_duyet_dang_huong' || (isAdminEditJS && ['da_phe_duyet_dang_huong', 'cho_duyet'].includes(selectedValue))) {
            approvalFieldsDivEditJS.classList.remove('hidden');
            if (selectedValue === 'da_phe_duyet_dang_huong') {
                if (!nguoiDuyetIdInputEditJS.value && initialNguoiDuyetIdJS) { // Ưu tiên giá trị đã lưu nếu có
                    nguoiDuyetIdInputEditJS.value = initialNguoiDuyetIdJS;
                } else if (!nguoiDuyetIdInputEditJS.value && currentUserIdEditJS) { // Sau đó mới đến user hiện tại
                    nguoiDuyetIdInputEditJS.value = currentUserIdEditJS;
                }

                if (!nguoiDuyetDisplayInputEditJS.value && initialTenNguoiDuyetJS) {
                    nguoiDuyetDisplayInputEditJS.value = initialTenNguoiDuyetJS;
                } else if (!nguoiDuyetDisplayInputEditJS.value && currentFullnameEditJS){
                     nguoiDuyetDisplayInputEditJS.value = currentFullnameEditJS;
                }
                
                if (!ngayDuyetInputEditJS.value && initialNgayDuyetJS) {
                    ngayDuyetInputEditJS.value = initialNgayDuyetJS;
                } else if(!ngayDuyetInputEditJS.value) {
                    ngayDuyetInputEditJS.value = new Date().toISOString().slice(0,10);
                }
            }
        } else {
            approvalFieldsDivEditJS.classList.add('hidden');
        }
        
        if (['khong_du_dieu_kien', 'tam_dung_huong', 'da_dung_huong'].includes(selectedValue)) {
            lyDoThayDoiContainerEditJS.classList.remove('hidden');
            lyDoThayDoiInputEditJS.setAttribute('required', 'required');
        } else {
            lyDoThayDoiContainerEditJS.classList.add('hidden');
            lyDoThayDoiInputEditJS.removeAttribute('required');
        }
    }

    if(trangThaiSelectEditJS) {
        trangThaiSelectEditJS.addEventListener('change', toggleApprovalFieldsEdit);
        toggleApprovalFieldsEdit(); 
    }
});
</script>