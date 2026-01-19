<?php
// $title, $doiTuong, $loaiDoiTuongList, $thonList, $oldData (nếu có lỗi), $errors (nếu có lỗi)
$displayData = $oldData ?? $doiTuong;
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

<form action="<?php echo url('doi-tuong/update/' . $doiTuong['id']); ?>" method="POST" class="bg-white p-6 rounded-lg shadow-md">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Cột 1 -->
        <div>
            <div class="mb-4">
                <label for="ma_doi_tuong" class="form-label">Mã đối tượng <span class="text-red-500">*</span></label>
                <input type="text" id="ma_doi_tuong" name="ma_doi_tuong" required maxlength="20"
                       value="<?php echo htmlspecialchars($displayData['ma_doi_tuong'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['ma_doi_tuong']) ? 'border-red-500' : ''; ?>">
                 <?php if (isset($errors['ma_doi_tuong'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ma_doi_tuong']; ?></p><?php endif; ?>
            </div>
            <div class="mb-4">
                <label for="ho_ten" class="form-label">Họ và tên <span class="text-red-500">*</span></label>
                <input type="text" id="ho_ten" name="ho_ten" required maxlength="100"
                        value="<?php echo htmlspecialchars($displayData['ho_ten'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['ho_ten']) ? 'border-red-500' : ''; ?>">
                <?php if (isset($errors['ho_ten'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ho_ten']; ?></p><?php endif; ?>
            </div>
             <div class="mb-4">
                <label for="ngay_sinh" class="form-label">Ngày sinh</label>
                <input type="date" id="ngay_sinh" name="ngay_sinh"
                       value="<?php echo htmlspecialchars($displayData['ngay_sinh'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['ngay_sinh']) ? 'border-red-500' : ''; ?>">
                <?php if (isset($errors['ngay_sinh'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ngay_sinh']; ?></p><?php endif; ?>
            </div>
             <div class="mb-4">
                <label for="gioi_tinh" class="form-label">Giới tính</label>
                <select id="gioi_tinh" name="gioi_tinh" class="form-input">
                    <option value="">-- Chọn giới tính --</option>
                    <option value="Nam" <?php echo (isset($displayData['gioi_tinh']) && $displayData['gioi_tinh'] == 'Nam') ? 'selected' : ''; ?>>Nam</option>
                    <option value="Nữ" <?php echo (isset($displayData['gioi_tinh']) && $displayData['gioi_tinh'] == 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
                    <option value="Khác" <?php echo (isset($displayData['gioi_tinh']) && $displayData['gioi_tinh'] == 'Khác') ? 'selected' : ''; ?>>Khác</option>
                </select>
            </div>
             <div class="mb-4">
                <label for="cccd" class="form-label">Mã định danh (Số CC/CCCD)</label>
                <input type="text" id="cccd" name="cccd" maxlength="15"
                       value="<?php echo htmlspecialchars($displayData['cccd'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input <?php echo isset($errors['cccd']) ? 'border-red-500' : ''; ?>">
                <?php if (isset($errors['cccd'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['cccd']; ?></p><?php endif; ?>
            </div>
             <div class="mb-4">
                <label for="so_dien_thoai" class="form-label">Số điện thoại</label>
                <input type="tel" id="so_dien_thoai" name="so_dien_thoai" maxlength="15"
                       value="<?php echo htmlspecialchars($displayData['so_dien_thoai'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input">
            </div>
            <!-- <div class="mb-4">
                <label for="anh_dai_dien_path" class="form-label">Minh chứng kèm theo</label>
                <input type="text" id="anh_dai_dien_path" name="anh_dai_dien_path" maxlength="255"
                       value="<?php echo htmlspecialchars($displayData['anh_dai_dien_path'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input">
                <?php if (!empty($displayData['anh_dai_dien_path'])): ?>
                    <img src="<?php echo htmlspecialchars($displayData['anh_dai_dien_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="Ảnh đại diện" class="mt-2 h-24 w-24 object-cover rounded">
                <?php endif; ?>
                
            </div> -->
            <?php
$minhChungDaCo = !empty($displayData['minh_chung_path']) ? json_decode($displayData['minh_chung_path'], true) : [];
?>

            <div class="mb-4">
                <label for="minh_chung_path" class="form-label">Minh chứng kèm theo</label>

                <!-- Input chọn nhiều file mới -->
                <input type="file"
                    id="minh_chung_path"
                    name="minh_chung_path[]"
                    class="form-input"
                    multiple>

                <p class="text-xs text-gray-500 mt-1">Bạn có thể chọn thêm nhiều tệp (PDF, ảnh, Word...). Các tệp đã đính kèm sẽ không bị mất.</p>
            </div>

            <!-- Hiển thị file cũ nếu có -->
            <div class="mb-4">
                <label class="form-label">Danh sách minh chứng đã đính kèm</label>
                <?php if (!empty($minhChungDaCo)): ?>
                    <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                        <?php foreach ($minhChungDaCo as $fileUrl): ?>
                            <li>
                                <a href="<?php echo htmlspecialchars($fileUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                target="_blank"
                                class="text-blue-600 hover:underline">
                                    <?php echo basename($fileUrl); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-sm text-gray-500 italic">Chưa có tệp minh chứng nào được đính kèm.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Cột 2 -->
         <div>
             <div class="mb-4">
                <label for="dia_chi_thuong_tru" class="form-label">Quê Quán</label>
                <textarea id="dia_chi_thuong_tru" name="dia_chi_thuong_tru" rows="2" class="form-input"><?php echo htmlspecialchars($displayData['dia_chi_thuong_tru'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
             

            <!-- THÊM DROPDOWN THÔN XÓM -->
            <div class="mb-4">
                <label for="thon_id" class="form-label">Thuộc thôn quản lý:</label>
                <select id="thon_id" name="thon_id" class="form-input <?php echo isset($errors['thon_id']) ? 'border-red-500' : ''; ?>">
                    <option value="">-- Chọn thôn quản lý --</option>
                    <?php foreach ($thonList as $thon): ?>
                        <option value="<?php echo $thon['id']; ?>" <?php echo (isset($displayData['thon_id']) && $displayData['thon_id'] == $thon['id']) ? 'selected' : ''; ?>>
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
                <textarea id="dia_chi_tam_tru" name="dia_chi_tam_tru" rows="2" class="form-input"><?php echo htmlspecialchars($displayData['dia_chi_tam_tru'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
             <div class="mb-4">
                <label for="loai_doi_tuong_id" class="form-label">Tình trạng đối tượng</label>
                <select id="loai_doi_tuong_id" name="loai_doi_tuong_id" class="form-input">
                    <option value="">-- Chọn tình trạng đối tượng --</option>
                    <?php foreach ($loaiDoiTuongList as $loai): ?>
                        <option value="<?php echo $loai['id']; ?>" <?php echo (isset($displayData['loai_doi_tuong_id']) && $displayData['loai_doi_tuong_id'] == $loai['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($loai['ten_loai'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- <div class="mb-4">
                <label for="trang_thai_doi_tuong" class="form-label">Trạng thái đối tượng</label>
                <select id="trang_thai_doi_tuong" name="trang_thai_doi_tuong" class="form-input">
                    <option value="con_song" <?php echo (isset($displayData['trang_thai_doi_tuong']) && $displayData['trang_thai_doi_tuong'] == 'con_song') ? 'selected' : ''; ?>>Còn sống</option>
                    <option value="mat_tich" <?php echo (isset($displayData['trang_thai_doi_tuong']) && $displayData['trang_thai_doi_tuong'] == 'mat_tich') ? 'selected' : ''; ?>>Mất tích</option>
                    <option value="da_chet" <?php echo (isset($displayData['trang_thai_doi_tuong']) && $displayData['trang_thai_doi_tuong'] == 'da_chet') ? 'selected' : ''; ?>>Đã chết</option>
                    </select>
            </div> -->
             <div class="mb-4">
                <label for="trang_thai_ho_so_dt" class="form-label">Trạng thái hồ sơ</label>
                <select id="trang_thai_ho_so_dt" name="trang_thai_ho_so_dt" class="form-input">
                    <option value="moi_tao" <?php echo (isset($displayData['trang_thai_ho_so_dt']) && $displayData['trang_thai_ho_so_dt'] == 'moi_tao') ? 'selected' : ''; ?>>Mới tạo</option>
                    <option value="dang_xu_ly_thong_tin" <?php echo (isset($displayData['trang_thai_ho_so_dt']) && $displayData['trang_thai_ho_so_dt'] == 'dang_xu_ly_thong_tin') ? 'selected' : ''; ?>>Đang xử lý thông tin</option>
                    <option value="da_xac_minh" <?php echo (isset($displayData['trang_thai_ho_so_dt']) && $displayData['trang_thai_ho_so_dt'] == 'da_xac_minh') ? 'selected' : ''; ?>>Đã xác minh</option>
                    <option value="cho_duyet_ho_so" <?php echo (isset($displayData['trang_thai_ho_so_dt']) && $displayData['trang_thai_ho_so_dt'] == 'cho_duyet_ho_so') ? 'selected' : ''; ?>>Chờ duyệt hồ sơ</option>
                    <option value="da_duyet_thong_tin" <?php echo (isset($displayData['trang_thai_ho_so_dt']) && $displayData['trang_thai_ho_so_dt'] == 'da_duyet_thong_tin') ? 'selected' : ''; ?>>Đã duyệt thông tin</option>
                    <option value="bi_tu_choi_thong_tin" <?php echo (isset($displayData['trang_thai_ho_so_dt']) && $displayData['trang_thai_ho_so_dt'] == 'bi_tu_choi_thong_tin') ? 'selected' : ''; ?>>Bị từ chối thông tin</option>
                </select>
            </div>
             <div class="mb-4">
                <label for="ghi_chu" class="form-label">Mô tả hoàn cảnh</label>
                <textarea id="ghi_chu" name="ghi_chu" rows="2" class="form-input"><?php echo htmlspecialchars($displayData['ghi_chu'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
             <!-- Trường ngày tiếp nhận ĐT có thể không cho sửa ở form edit, mà hiển thị từ DB -->
             <!-- <div class="mb-4">
                <label for="ngay_tiep_nhan_dt" class="form-label">Ngày tiếp nhận ĐT</label>
                <input type="date" id="ngay_tiep_nhan_dt" name="ngay_tiep_nhan_dt"
                       value="<?php echo htmlspecialchars($displayData['ngay_tiep_nhan_dt'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="form-input">
            </div> -->
         </div>
    </div>

    <!-- Thông tin không cho sửa trực tiếp -->
    <div class="mt-6 pt-4 border-t border-gray-200">
        <h3 class="text-lg font-medium text-gray-600 mb-3">Thông tin Tiếp nhận & Cập nhật</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500">Ngày tiếp nhận ĐT:</p>
                <p class="text-gray-800"><?php echo !empty($doiTuong['ngay_tiep_nhan_dt']) ? date('d/m/Y', strtotime($doiTuong['ngay_tiep_nhan_dt'])) : 'N/A'; ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Người tiếp nhận ĐT:</p>
                <p class="text-gray-800">
                    <?php echo htmlspecialchars($doiTuong['ten_nguoi_tiep_nhan_dt'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>
                    <?php if (!empty($doiTuong['nguoi_tiep_nhan_dt_id'])): ?>
                        (ID: <?php echo htmlspecialchars($doiTuong['nguoi_tiep_nhan_dt_id'], ENT_QUOTES, 'UTF-8'); ?>)
                    <?php endif; ?>
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Ngày tạo bản ghi:</p>
                <p class="text-gray-800"><?php echo !empty($doiTuong['ngay_tao']) ? date('d/m/Y H:i:s', strtotime($doiTuong['ngay_tao'])) : 'N/A'; ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Cập nhật lần cuối:</p>
                <p class="text-gray-800"><?php echo !empty($doiTuong['ngay_cap_nhat']) ? date('d/m/Y H:i:s', strtotime($doiTuong['ngay_cap_nhat'])) : 'Chưa cập nhật'; ?></p>
            </div>
        </div>
    </div>

    <!-- Nút bấm -->
    <div class="mt-8 pt-4 border-t border-gray-200 flex justify-end space-x-3">
         <a href="<?php echo url('doi-tuong/index'); ?>" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">Hủy bỏ</a>
        <button type="submit" class="btn btn-primary">Cập nhật Đối tượng</button>
    </div>
</form>