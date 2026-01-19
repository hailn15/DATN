<?php
// View: app/views/loai_hinh_cham_soc_cd/index.php
// Vars: $title, $loaiHinhList, $pagination, $trangThaiOptions, $currentFilters
?>
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-700"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
    <a href="<?php echo url('loai-hinh-cham-soc-cd/create'); ?>" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
        Thêm Loại Hình
    </a>
</div>

<div class="mb-4 bg-white p-4 rounded-lg shadow-sm">
    <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        
        <input type="hidden" name="url" value="loai-hinh-cham-soc-cd/index">

        <div>
            <label for="search" class="form-label">Tìm kiếm:</label>
            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($currentFilters['searchTerm'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   placeholder="Mã, Tên loại hình..."
                   class="form-input">
        </div>
        <div>
            <label for="trang_thai_ap_dung_filter" class="form-label">Trạng thái áp dụng:</label>
            <select id="trang_thai_ap_dung_filter" name="trang_thai_ap_dung" class="form-input">
                <option value="">-- Tất cả --</option>
                <?php foreach ($trangThaiOptions as $value => $label): ?>
                    <option value="<?php echo $value; ?>" <?php echo (isset($currentFilters['trang_thai_ap_dung']) && $currentFilters['trang_thai_ap_dung'] == $value) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <input type="hidden" name="page" value="1">
        <div class="flex space-x-2">
            <button type="submit" class="btn btn-secondary flex-grow">Lọc</button>
            <?php if (!empty($currentFilters['searchTerm']) || !empty($currentFilters['trang_thai_ap_dung'])): ?>
                <a href="<?php echo url('loai-hinh-cham-soc-cd/index'); ?>" class="btn bg-gray-300 hover:bg-gray-400 text-gray-700 flex-grow">Xóa lọc</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="overflow-x-auto bg-white rounded-lg shadow">
    <table class="table">
        <thead>
            <tr>
                <th class="w-auto">STT</th>
                <!-- <th>Mã Loại Hình</th> -->
                <th>Tên Loại Hình</th>
                <th class="text-right">Mức Chuẩn (VNĐ)</th>
                <th class="text-right">Hệ Số</th>
                <th class="text-right">Giá Trị(VNĐ)</th>
                <th>Trạng thái</th>
                <th>Văn bản CS</th>
                
                <th class="w-1/12 text-center sticky right-0 bg-gray-50 z-10 border-l border-gray-200">Hành động</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php if (empty($loaiHinhList)): ?>
                <tr>
                    <td colspan="9" class="text-center py-4 text-gray-500">Không tìm thấy loại hình nào.</td>
                </tr>
            <?php else: ?>
                <?php $stt = ($pagination['currentPage'] - 1) * $pagination['limit'] + 1; ?>
                <?php foreach ($loaiHinhList as $lh): ?>
                    <tr>
                        <td class="text-center"><?php echo $stt++; ?></td>
                        <!-- <td><?php echo htmlspecialchars($lh['ma_loai_hinh'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td> -->
                        <td>
                            <a href="<?php echo url('loai-hinh-cham-soc-cd/edit/' . $lh['id']); ?>" class="font-medium text-blue-600 hover:underline">
                                <?php echo htmlspecialchars($lh['ten_loai_hinh'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </td>
                        <td class="text-right">
                            <?php echo isset($lh['muc_chuan']) ? number_format($lh['muc_chuan'], 0, ',', '.') : 'N/A'; ?>
                        </td>
                        <td class="text-right">
                            <?php echo isset($lh['he_so']) ? number_format($lh['he_so'], 2, ',', '.') : 'N/A'; ?>
                        </td>
                        <td class="text-right"><?php echo isset($lh['kinh_phi_dinh_muc_du_kien']) ? number_format($lh['kinh_phi_dinh_muc_du_kien'], 0, ',', '.') : 'N/A'; ?></td>
                        <td>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                <?php echo ($lh['trang_thai_ap_dung'] === 'dang_ap_dung') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo htmlspecialchars($trangThaiOptions[$lh['trang_thai_ap_dung']] ?? ucfirst($lh['trang_thai_ap_dung']), ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($lh['van_ban_chinh_sach_id'])): ?>
                                <span title="<?php echo htmlspecialchars($lh['ten_van_ban'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($lh['so_hieu_van_ban'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            <?php elseif (!empty($lh['ghi_chu_them_vb'])): ?>
                                <?php echo htmlspecialchars(mb_strimwidth($lh['ghi_chu_them_vb'], 0, 30, "..."), ENT_QUOTES, 'UTF-8'); ?>
                            <?php else: ?>
                                Chưa có
                            <?php endif; ?>
                        </td>
                        
                        <td class="text-center sticky right-0 bg-white z-10 border-l border-gray-200">
                            <div class="flex justify-center items-center space-x-1">
                                <a href="<?php echo url('loai-hinh-cham-soc-cd/edit/' . $lh['id']); ?>" title="Sửa" class="text-blue-600 hover:text-blue-800 p-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </a>
                                <form action="<?php echo url('loai-hinh-cham-soc-cd/destroy/' . $lh['id']); ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa loại hình này?');" class="inline-block">
                                    <button type="submit" title="Xóa" class="text-red-600 hover:text-red-800 p-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../layouts/_pagination.php'; ?>