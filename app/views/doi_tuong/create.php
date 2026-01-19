<?php
// $title, $loaiDoiTuongList, $thonList, $oldData (nếu có lỗi), $errors (nếu có lỗi)
if (!isset($thonList)) { $thonList = []; } // Để tránh lỗi nếu chưa truyền
?>

<h1 class="text-2xl font-semibold text-gray-700 mb-6"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>

 <?php if (!empty($errors)): ?>
    <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-400 rounded">
         <strong class="font-bold">Có lỗi xảy ra:</strong>
         <ul class="list-disc list-inside ml-4">
             <?php foreach ($errors as $field => $message): // Hiển thị lỗi theo trường ?>
                 <li><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></li>
             <?php endforeach; ?>
         </ul>
    </div>
<?php endif; ?>


<form action="<?php echo url('doi-tuong/store'); ?>" method="POST" class="bg-white p-6 rounded-lg shadow-md">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Cột 1 -->
        <div>
            <div class="mb-4">
                <label for="ma_doi_tuong" class="form-label">Mã đối tượng <span class="text-red-500">*</span></label>
                <input type="text" id="ma_doi_tuong" name="ma_doi_tuong" required maxlength="20"
                       value="<?php echo htmlspecialchars($oldData['ma_doi_tuong'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['ma_doi_tuong']) ? 'border-red-500' : ''; ?>">
                <?php if (isset($errors['ma_doi_tuong'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ma_doi_tuong']; ?></p><?php endif; ?>
            </div>
            <div class="mb-4">
                <label for="ho_ten" class="form-label">Họ và tên <span class="text-red-500">*</span></label>
                <input type="text" id="ho_ten" name="ho_ten" required maxlength="100"
                        value="<?php echo htmlspecialchars($oldData['ho_ten'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['ho_ten']) ? 'border-red-500' : ''; ?>">
                <?php if (isset($errors['ho_ten'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ho_ten']; ?></p><?php endif; ?>
            </div>
            <div class="mb-4">
                <label for="cccd" class="form-label">Số định danh(CC,CCCD)<span class="text-red-500">*</span></label>
                <!-- <input type="text" id="cccd" name="cccd" maxlength="12"
                    value="<?php echo htmlspecialchars($oldData['cccd'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    class="form-input <?php echo isset($errors['cccd']) ? 'border-red-500' : ''; ?>"> -->
                <input type="text" id="cccd" name="cccd" maxlength="12" minlength="12" pattern="\d{12}" required
                    value="<?php echo htmlspecialchars($oldData['cccd'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    class="form-input <?php echo isset($errors['cccd']) ? 'border-red-500' : ''; ?>">
                    <p class="text-xs text-gray-500 mt-1">Nhập đúng 12 chữ số, ví dụ: 012345678901</p>
                 <?php if (isset($errors['cccd'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['cccd']; ?></p><?php endif; ?>
            </div>
             <div class="mb-4">
                <label for="ngay_sinh" class="form-label">Ngày sinh</label>
                <input type="date" id="ngay_sinh" name="ngay_sinh"
                       value="<?php echo htmlspecialchars($oldData['ngay_sinh'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['ngay_sinh']) ? 'border-red-500' : ''; ?>">
                <?php if (isset($errors['ngay_sinh'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ngay_sinh']; ?></p><?php endif; ?>
            </div>
             <div class="mb-4">
                <label for="gioi_tinh" class="form-label">Giới tính</label>
                <select id="gioi_tinh" name="gioi_tinh" class="form-input" require>
                    <option value="">-- Chọn giới tính --</option>
                    <option value="Nam" <?php echo (isset($oldData['gioi_tinh']) && $oldData['gioi_tinh'] == 'Nam') ? 'selected' : ''; ?>>Nam</option>
                    <option value="Nữ" <?php echo (isset($oldData['gioi_tinh']) && $oldData['gioi_tinh'] == 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
                    <option value="Khác" <?php echo (isset($oldData['gioi_tinh']) && $oldData['gioi_tinh'] == 'Khác') ? 'selected' : ''; ?>>Khác</option>
                </select>
            </div>
             
             <div class="mb-4">
                <label for="so_dien_thoai" class="form-label">Số điện thoại</label>
                <input type="tel" id="so_dien_thoai" name="so_dien_thoai" maxlength="15"
                       value="<?php echo htmlspecialchars($oldData['so_dien_thoai'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input">
            </div>
            <!-- <div class="mb-4">
                <label for="anh_dai_dien_path" class="form-label">Ảnh đại diện (URL hoặc để trống)</label>
                <input type="text" id="anh_dai_dien_path" name="anh_dai_dien_path" maxlength="255"
                       value="<?php echo htmlspecialchars($oldData['anh_dai_dien_path'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input">
            </div> -->
            <!-- <div class="mb-4">
                <label for="minh_chung_path" class="form-label">Minh chứng đính kèm (ảnh/tệp minh chứng)</label>
                <input type="file" id="minh_chung_path" name="minh_chung_path[]"
                    multiple accept="image/*,.pdf,.doc,.docx"
                    class="form-input <?php echo isset($errors['minh_chung_path']) ? 'border-red-500' : ''; ?>">

                <?php if (isset($errors['minh_chung_path'])): ?>
                    <p class="text-red-500 text-xs mt-1"><?php echo $errors['minh_chung_path']; ?></p>
                <?php endif; ?>

                <?php if (!empty($oldData['minh_chung_path']) && is_array($oldData['minh_chung_path'])): ?>
                    <p class="text-xs text-gray-500 mt-1">Tệp đã đính kèm:</p>
                    <ul class="list-disc ml-6 mt-1">
                        <?php foreach ($oldData['minh_chung_path'] as $file): ?>
                            <li>
                                <a href="<?php echo htmlspecialchars($file, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="text-blue-600 hover:underline">
                                    <?php echo basename($file); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div> -->
            <div class="mb-4">
                <label for="minh_chung_path" class="form-label">Minh chứng kèm theo</label>
                <input type="file"
                    id="minh_chung_path"
                    name="minh_chung_path[]"
                    class="form-input"
                    multiple>
                <p class="text-sm text-gray-500 mt-1">Có thể chọn nhiều tệp cùng lúc (ảnh, PDF...)</p>
            </div>
        </div>

        <!-- Cột 2 -->
         <div>
             <div class="mb-4">
                <label for="dia_chi_thuong_tru" class="form-label">Quê quán</label>
                <textarea id="dia_chi_thuong_tru" name="dia_chi_thuong_tru" rows="2" class="form-input"><?php echo htmlspecialchars($oldData['dia_chi_thuong_tru'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
             
            
            <!-- THÊM DROPDOWN THÔN XÓM -->
            <div class="mb-4">
                <label for="thon_id" class="form-label">Thuộc thôn quản lý:<span class="text-red-500">*</span></label>
                <select id="thon_id" name="thon_id" class="form-input <?php echo isset($errors['thon_id']) ? 'border-red-500' : ''; ?>">
                    <option value="">-- Chọn thôn quản lý --</option>
                    <?php foreach ($thonList as $thon): ?>
                        <option value="<?php echo $thon['id']; ?>" <?php echo (isset($oldData['thon_id']) && $oldData['thon_id'] == $thon['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($thon['ten_thon'], ENT_QUOTES, 'UTF-8'); ?>
                            <?php if (!empty($thon['ma_thon'])): ?>
                                (<?php echo htmlspecialchars($thon['ma_thon'], ENT_QUOTES, 'UTF-8'); ?>)
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['thon_id'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['thon_id']; ?></p><?php endif; ?>
            </div>
            <!-- KẾT THÚC DROPDOWN THÔN XÓM -->
            <div class="mb-4">
                <label for="dia_chi_tam_tru" class="form-label">Số nhà, xóm</label>
                <textarea id="dia_chi_tam_tru" name="dia_chi_tam_tru" rows="2" class="form-input"><?php echo htmlspecialchars($oldData['dia_chi_tam_tru'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
            <div class="mb-4">
                <label for="loai_doi_tuong_id" class="form-label">Tình trạng đối tượng</label>
                <select id="loai_doi_tuong_id" name="loai_doi_tuong_id" class="form-input">
                    <option value="">-- Chọn tình trạng đối tượng --</option>
                    <?php foreach ($loaiDoiTuongList as $loai): ?>
                        <option value="<?php echo $loai['id']; ?>" <?php echo (isset($oldData['loai_doi_tuong_id']) && $oldData['loai_doi_tuong_id'] == $loai['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($loai['ten_loai'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select> 
            </div>
            <div class="mb-4">
                <label for="trang_thai_ho_so_dt" class="form-label">Trạng thái hồ sơ</label>
                <select id="trang_thai_ho_so_dt" name="trang_thai_ho_so_dt" class="form-input">
                    <option value="moi_tao" <?php echo (!isset($oldData['trang_thai_ho_so_dt']) || (isset($oldData['trang_thai_ho_so_dt']) && $oldData['trang_thai_ho_so_dt'] == 'moi_tao')) ? 'selected' : ''; ?>>Mới tạo</option>
                    <option value="dang_xu_ly_thong_tin" <?php echo (isset($oldData['trang_thai_ho_so_dt']) && $oldData['trang_thai_ho_so_dt'] == 'dang_xu_ly_thong_tin') ? 'selected' : ''; ?>>Đang xử lý thông tin</option>
                    <option value="da_xac_minh" <?php echo (isset($oldData['trang_thai_ho_so_dt']) && $oldData['trang_thai_ho_so_dt'] == 'da_xac_minh') ? 'selected' : ''; ?>>Đã xác minh</option>
                    <option value="cho_duyet_ho_so" <?php echo (isset($oldData['trang_thai_ho_so_dt']) && $oldData['trang_thai_ho_so_dt'] == 'cho_duyet_ho_so') ? 'selected' : ''; ?>>Chờ duyệt hồ sơ</option>
                    <option value="da_duyet_thong_tin" <?php echo (isset($oldData['trang_thai_ho_so_dt']) && $oldData['trang_thai_ho_so_dt'] == 'da_duyet_thong_tin') ? 'selected' : ''; ?>>Đã duyệt thông tin</option>
                    <option value="bi_tu_choi_thong_tin" <?php echo (isset($oldData['trang_thai_ho_so_dt']) && $oldData['trang_thai_ho_so_dt'] == 'bi_tu_choi_thong_tin') ? 'selected' : ''; ?>>Bị từ chối thông tin</option>
                </select>
            </div>
             <div class="mb-4">
                <label for="ghi_chu" class="form-label">Mô tả hoàn cảnh</label>
                <textarea id="ghi_chu" name="ghi_chu" rows="2" class="form-input"><?php echo htmlspecialchars($oldData['ghi_chu'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
             <div class="mb-4">
                <label for="ngay_tiep_nhan_dt" class="form-label">Ngày tiếp nhận ĐT</label>
                <input type="date" id="ngay_tiep_nhan_dt" name="ngay_tiep_nhan_dt"
                       value="<?php echo htmlspecialchars($oldData['ngay_tiep_nhan_dt'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input">
            </div>
         </div>
    </div>

    <!-- Nút bấm -->
    <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end space-x-3">
         <a href="<?php echo url('doi-tuong/index'); ?>" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">Hủy bỏ</a>
        <button type="submit" class="btn btn-primary">Lưu lại Đối tượng</button>
    </div>
</form>