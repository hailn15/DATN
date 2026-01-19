<?php
// app/views/nguoi_cham_soc/_form.php
// Vars: $ncs, $errors
?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Cột 1 -->
    <div>
        <div class="mb-4">
            <label for="ho_ten" class="form-label">Họ và tên <span class="text-red-500">*</span></label>
            <input type="text" id="ho_ten" name="ho_ten" required maxlength="100"
                   value="<?php echo htmlspecialchars($ncs['ho_ten'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input <?php echo isset($errors['ho_ten']) ? 'border-red-500' : ''; ?>">
            <?php if(isset($errors['ho_ten'])): ?><p class="form-error-text"><?php echo $errors['ho_ten']; ?></p><?php endif; ?>
        </div>
        <!-- <div class="mb-4">
            <label for="ma_nguoi_cham_soc" class="form-label">Mã người chăm sóc</label>
            <input type="text" id="ma_nguoi_cham_soc" name="ma_nguoi_cham_soc" maxlength="20"
                   value="<?php echo htmlspecialchars($ncs['ma_nguoi_cham_soc'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input <?php echo isset($errors['ma_nguoi_cham_soc']) ? 'border-red-500' : ''; ?>">
            <?php if(isset($errors['ma_nguoi_cham_soc'])): ?><p class="form-error-text"><?php echo $errors['ma_nguoi_cham_soc']; ?></p><?php endif; ?>
        </div> -->
        <div class="mb-4">
            <label for="ngay_sinh" class="form-label">Ngày sinh</label>
            <input type="date" id="ngay_sinh" name="ngay_sinh"
                   value="<?php echo htmlspecialchars($ncs['ngay_sinh'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>
         <div class="mb-4">
            <label class="form-label">Giới tính</label>
            <div class="flex items-center space-x-4 mt-2">
                <label class="inline-flex items-center">
                    <input type="radio" name="gioi_tinh" value="Nam" class="form-radio" <?php echo (isset($ncs['gioi_tinh']) && $ncs['gioi_tinh'] == 'Nam') ? 'checked' : ''; ?>>
                    <span class="ml-2">Nam</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="gioi_tinh" value="Nữ" class="form-radio" <?php echo (isset($ncs['gioi_tinh']) && $ncs['gioi_tinh'] == 'Nữ') ? 'checked' : ''; ?>>
                    <span class="ml-2">Nữ</span>
                </label>
                 <label class="inline-flex items-center">
                    <input type="radio" name="gioi_tinh" value="Khác" class="form-radio" <?php echo (isset($ncs['gioi_tinh']) && $ncs['gioi_tinh'] == 'Khác' || !isset($ncs['gioi_tinh'])) ? 'checked' : ''; ?>>
                    <span class="ml-2">Khác</span>
                </label>
            </div>
        </div>
    </div>
    <!-- Cột 2 -->
    <div>
        <div class="mb-4">
            <label for="cccd" class="form-label">Mã định danh(Số CC/CCCD) <span class="text-red-500">*</span></label>
            <input type="text" id="cccd" name="cccd" maxlength="15"
                   value="<?php echo htmlspecialchars($ncs['cccd'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input <?php echo isset($errors['cccd']) ? 'border-red-500' : ''; ?>">
            <?php if(isset($errors['cccd'])): ?><p class="form-error-text"><?php echo $errors['cccd']; ?></p><?php endif; ?>
        </div>
        <div class="mb-4">
            <label for="so_dien_thoai" class="form-label">Số điện thoại</label>
            <input type="tel" id="so_dien_thoai" name="so_dien_thoai" maxlength="15"
                   value="<?php echo htmlspecialchars($ncs['so_dien_thoai'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input">
        </div>
        <div class="mb-4">
            <label for="dia_chi" class="form-label">Địa chỉ</label>
            <textarea id="dia_chi" name="dia_chi" rows="2" class="form-textarea"><?php echo htmlspecialchars($ncs['dia_chi'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
        <div class="mb-4">
            <label for="quan_he_voi_doi_tuong" class="form-label">Mối quan hệ (với đối tượng)</label>
            <input type="text" id="quan_he_voi_doi_tuong" name="quan_he_voi_doi_tuong" maxlength="100"
                   value="<?php echo htmlspecialchars($ncs['quan_he_voi_doi_tuong'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input" placeholder="Con, cháu, hàng xóm...">
        </div>
    </div>
</div>
<div class="mb-4">
    <label for="ghi_chu" class="form-label">Mô tả điều kiện chăm sóc</label>
    <textarea id="ghi_chu" name="ghi_chu" rows="3" class="form-textarea"><?php echo htmlspecialchars($ncs['ghi_chu'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
</div>