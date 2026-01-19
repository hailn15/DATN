<?php
// View: app/views/muc_tro_cap_hang_thang/edit.php
$displayData = $oldData ?? $mucTroCap;
?>
<h1 class="text-2xl font-semibold text-gray-700 mb-6"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>

<?php if (!empty($errors)): ?>
<div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-400 rounded">
    <strong class="font-bold">Có lỗi xảy ra:</strong>
    <ul class="list-disc list-inside ml-4">
        <?php foreach ($errors as $field => $message): ?>
            <li><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form action="<?php echo url('muc-tro-cap-hang-thang/update/' . $mucTroCap['id']); ?>" method="POST" class="bg-white p-6 rounded-lg shadow-md">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Cột 1 -->
        <div>
            <div class="mb-4">
                <label for="ma_muc" class="form-label">Mã Mức <span class="text-red-500">*</span></label>
                <input type="text" id="ma_muc" name="ma_muc" required maxlength="50"
                       value="<?php echo htmlspecialchars($displayData['ma_muc'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['ma_muc']) ? 'border-red-500' : ''; ?>">
                 <?php if(isset($errors['ma_muc'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ma_muc']; ?></p><?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="ten_muc" class="form-label">Tên Mức Trợ Cấp <span class="text-red-500">*</span></label>
                <input type="text" id="ten_muc" name="ten_muc" required maxlength="255"
                       value="<?php echo htmlspecialchars($displayData['ten_muc'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['ten_muc']) ? 'border-red-500' : ''; ?>">
                <?php if(isset($errors['ten_muc'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ten_muc']; ?></p><?php endif; ?>
            </div>

             <div class="mb-4">
                <label for="muc_chuan" class="form-label">Mức Chuẩn (VNĐ)</label>
                <input type="number" id="muc_chuan" name="muc_chuan" min="0" step="1000"
                       value="<?php echo htmlspecialchars($displayData['muc_chuan'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['muc_chuan']) ? 'border-red-500' : ''; ?>">
                <?php if(isset($errors['muc_chuan'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['muc_chuan']; ?></p><?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="he_so" class="form-label">Hệ Số</label>
                <input type="number" id="he_so" name="he_so" min="0" step="0.01"
                       value="<?php echo htmlspecialchars($displayData['he_so'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['he_so']) ? 'border-red-500' : ''; ?>">
                <?php if(isset($errors['he_so'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['he_so']; ?></p><?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="so_tien_ap_dung" class="form-label">Số Tiền Áp Dụng (VNĐ) <span class="text-red-500">*</span></label>
                <input type="number" id="so_tien_ap_dung" name="so_tien_ap_dung" required min="0" step="1000"
                       value="<?php echo htmlspecialchars($displayData['so_tien_ap_dung'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['so_tien_ap_dung']) ? 'border-red-500' : ''; ?>">
                <small class="text-gray-500">Sẽ tự động tính nếu Mức chuẩn và Hệ số hợp lệ. Có thể nhập tay nếu muốn ghi đè.</small>
                 <?php if(isset($errors['so_tien_ap_dung'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['so_tien_ap_dung']; ?></p><?php endif; ?>
            </div>
        </div>

        <!-- Cột 2 -->
        <div>
            <div class="mb-4">
                <label for="don_vi_tinh" class="form-label">Đơn vị tính</label>
                <input type="text" id="don_vi_tinh" name="don_vi_tinh" maxlength="50"
                       value="<?php echo htmlspecialchars($displayData['don_vi_tinh'] ?? 'VNĐ/tháng', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input">
            </div>

            <div class="mb-4">
                <label for="mo_ta" class="form-label">Mô tả chi tiết</label>
                <textarea id="mo_ta" name="mo_ta" rows="3" class="form-textarea"><?php echo htmlspecialchars($displayData['mo_ta'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>

            <div class="mb-4">
                <label for="van_ban_chinh_sach_id" class="form-label">Văn bản chính sách (Nếu có)</label>
                <select id="van_ban_chinh_sach_id" name="van_ban_chinh_sach_id" class="form-select <?php echo isset($errors['van_ban_chinh_sach_id']) ? 'border-red-500' : ''; ?>">
                    <option value="">-- Chọn văn bản --</option>
                     <?php /* Mockup for vanBanOptions
                    <?php if (isset($vanBanOptions) && !empty($vanBanOptions)): ?>
                        <?php foreach ($vanBanOptions as $vb): ?>
                            <option value="<?php echo $vb['id']; ?>" <?php echo (isset($displayData['van_ban_chinh_sach_id']) && $displayData['van_ban_chinh_sach_id'] == $vb['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($vb['so_hieu'] . ' - ' . $vb['ten_van_ban'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    */ ?>
                </select>
                 <?php if(isset($errors['van_ban_chinh_sach_id'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['van_ban_chinh_sach_id']; ?></p><?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="ghi_chu_them_vb" class="form-label">Ghi chú thêm về Văn bản/Nghị định</label>
                <textarea id="ghi_chu_them_vb" name="ghi_chu_them_vb" rows="2" class="form-textarea"><?php echo htmlspecialchars($displayData['ghi_chu_them_vb'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>

            <div class="mb-4">
                <label for="trang_thai_ap_dung" class="form-label">Trạng thái áp dụng <span class="text-red-500">*</span></label>
                <select id="trang_thai_ap_dung" name="trang_thai_ap_dung" class="form-select">
                    <?php $selectedTrangThai = $displayData['trang_thai_ap_dung'] ?? 'dang_ap_dung'; ?>
                    <?php foreach ($trangThaiOptions as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php echo ($selectedTrangThai == $value) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end space-x-3">
        <a href="<?php echo url('muc-tro-cap-hang-thang/index'); ?>" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">Quay lại DS</a>
        <button type="submit" class="btn btn-primary">Cập nhật Mức Trợ Cấp</button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mucChuanInput = document.getElementById('muc_chuan');
    const heSoInput = document.getElementById('he_so');
    const soTienApDungInput = document.getElementById('so_tien_ap_dung');

    function calculateSoTien() {
        const mucChuanVal = mucChuanInput.value.trim();
        const heSoVal = heSoInput.value.trim();

        const mucChuan = parseFloat(mucChuanVal);
        const heSo = parseFloat(heSoVal);

        if (mucChuanVal !== '' && !isNaN(mucChuan) && mucChuan > 0 && 
            heSoVal !== '' && !isNaN(heSo) && heSo > 0) {
            soTienApDungInput.value = Math.round(mucChuan * heSo);
        } else if (mucChuanVal !== '' && !isNaN(mucChuan) && mucChuan === 0 ||
                   heSoVal !== '' && !isNaN(heSo) && heSo === 0 ) {
             soTienApDungInput.value = 0;
        }
    }

    mucChuanInput.addEventListener('input', calculateSoTien);
    heSoInput.addEventListener('input', calculateSoTien);
    
    calculateSoTien(); // Calculate on page load for existing values
});
</script>