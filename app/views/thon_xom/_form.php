<?php
// app/views/thon_xom/_form.php
// Vars: $isEdit (boolean), $displayData (array for form values), $errors (array for field errors)
?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <div class="mb-4">
            <label for="ten_thon" class="form-label">Tên thôn/xóm <span class="text-red-500">*</span></label>
            <input type="text" id="ten_thon" name="ten_thon" required maxlength="100"
                   value="<?php echo htmlspecialchars($displayData['ten_thon'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input <?php echo isset($errors['ten_thon']) ? 'border-red-500' : ''; ?>">
            <?php if(isset($errors['ten_thon'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ten_thon']; ?></p><?php endif; ?>
        </div>
        <div class="mb-4">
            <label for="ma_thon" class="form-label">Mã thôn/xóm (nếu có)</label>
            <input type="text" id="ma_thon" name="ma_thon" maxlength="20"
                   value="<?php echo htmlspecialchars($displayData['ma_thon'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="form-input <?php echo isset($errors['ma_thon']) ? 'border-red-500' : ''; ?>">
            <?php if(isset($errors['ma_thon'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ma_thon']; ?></p><?php endif; ?>
        </div>
    </div>
    <div>
        <div class="mb-4">
            <label for="mo_ta" class="form-label">Mô tả</label>
            <textarea id="mo_ta" name="mo_ta" rows="5" class="form-input"><?php echo htmlspecialchars($displayData['mo_ta'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
    </div>
</div>

<div class="mt-6 pt-4 border-t border-gray-200 flex justify-end space-x-3">
    <a href="<?php echo url('thon-xom/index'); ?>" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">
        <?php echo $isEdit ? 'Quay lại DS' : 'Hủy'; ?>
    </a>
    <button type="submit" class="btn btn-primary">
        <?php echo $isEdit ? 'Cập nhật Thôn/Xóm' : 'Lưu Thôn/Xóm'; ?>
    </button>
</div>