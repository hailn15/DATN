<?php
// View: app/views/ho_tro_khan_cap/index.php
// Vars: $title, $hoTroList, $pagination, $trangThaiOptions, $currentFilters, $doiTuongContext
?>
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-700"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
    <a href="<?php echo url('ho-tro-khan-cap/create' . (isset($doiTuongContext['id']) ? '/' . $doiTuongContext['id'] : '')); ?>" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
        Tạo Hỗ trợ KC
    </a>
</div>

<div class="mb-4 bg-white p-4 rounded-lg shadow-sm">
    <!-- SỬA ĐỔI Ở ĐÂY: Đặt action="" và thêm input hidden cho 'url' -->
    <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        
        <!-- SỬA ĐỔI QUAN TRỌNG: Thêm input ẩn để giữ lại route khi submit form -->
        <input type="hidden" name="url" value="ho-tro-khan-cap/index">

        <div>
            <label for="search" class="form-label">Tìm kiếm:</label>
            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($currentFilters['searchTerm'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   placeholder="Mã HS, Tên người nhận, Lý do, Loại hình..."
                   class="form-input">
        </div>
        <div>
            <label for="doi_tuong_id_filter" class="form-label">Đối tượng (ID):</label>
            <input type="number" id="doi_tuong_id_filter" name="doi_tuong_id" value="<?php echo htmlspecialchars($currentFilters['doi_tuong_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   placeholder="Nhập ID đối tượng..."
                   class="form-input">
        </div>
        <div>
            <label for="trang_thai_filter" class="form-label">Trạng thái HS:</label>
            <select id="trang_thai_filter" name="trang_thai" class="form-input">
                <option value="">-- Tất cả trạng thái --</option>
                <?php foreach ($trangThaiOptions as $value => $label): ?>
                    <option value="<?php echo $value; ?>" <?php echo (isset($currentFilters['trang_thai']) && $currentFilters['trang_thai'] == $value) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <input type="hidden" name="page" value="1">
        <div class="flex space-x-2">
            <button type="submit" class="btn btn-secondary flex-grow">Lọc</button>
            <?php if (!empty($currentFilters['searchTerm']) || !empty($currentFilters['doi_tuong_id']) || !empty($currentFilters['trang_thai'])): ?>
                <a href="<?php echo url('ho-tro-khan-cap/index' . (!empty($doiTuongContext['id']) ? '?doi_tuong_id='.$doiTuongContext['id'] : '') ); ?>" class="btn bg-gray-300 hover:bg-gray-400 text-gray-700 flex-grow">Xóa lọc tìm kiếm</a>
            <?php endif; ?>

             <!-- ====== THÊM NÚT XUẤT CSV VÀO ĐÂY ====== -->
             <a href="<?php echo url('ho-tro-khan-cap/exportCsv?') . http_build_query(array_filter($currentFilters)); ?>" 
               target="_blank"
               class="btn bg-green-600 text-white hover:bg-green-700 flex-grow flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Xuất CSV
            </a>
        </div>
    </form>
</div>

<div class="overflow-x-auto bg-white rounded-lg shadow">
    <table class="table">
        <thead>
            <tr>
                <th class="w-1/12">STT</th>
                <th>Mã HS KC</th>
                <th>Người nhận</th>
                <th>Loại Hình HT</th>
                <th>Lý do</th>
                <th class="text-right">Giá trị (VNĐ)</th>
                <th>Trạng thái</th>
                <th class="w-1/12 text-center sticky right-0 bg-gray-50 z-10 border-l border-gray-200">Hành động</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php if (empty($hoTroList)): ?>
                <tr>
                    <td colspan="8" class="text-center py-4 text-gray-500">Không tìm thấy hồ sơ hỗ trợ nào.</td>
                </tr>
            <?php else: ?>
                <?php $stt = ($pagination['currentPage'] - 1) * $pagination['limit'] + 1; ?>
                <?php foreach ($hoTroList as $ht): ?>
                    <tr>
                        <td class="text-center"><?php echo $stt++; ?></td>
                         <td>
                            <a href="<?php echo url('ho-tro-khan-cap/show/' . $ht['id']); ?>" class="font-medium text-blue-600 hover:underline">
                                <?php echo htmlspecialchars($ht['ma_ho_so_kc'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($ht['ho_ten_nguoi_nhan'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                            <?php if (!empty($ht['ten_doi_tuong_lien_quan'])): ?>
                                <br><span class="text-xs text-gray-500">(ĐT liên quan: <a href="<?php echo url('doi-tuong/edit/' . $ht['doi_tuong_id']); ?>" target="_blank" class="text-blue-500 hover:underline"><?php echo htmlspecialchars($ht['ten_doi_tuong_lien_quan']); ?></a>)</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($ht['ten_loai_hinh_ho_tro'] ?? $ht['hinh_thuc_ho_tro_cu_the'] ?? 'Chưa rõ', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars(mb_strimwidth($ht['ly_do_ho_tro'], 0, 60, "..."), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="text-right"><?php echo isset($ht['gia_tri_ho_tro_tien_mat']) ? number_format($ht['gia_tri_ho_tro_tien_mat'], 0, ',', '.') . ' đ' : 'N/A'; ?></td>
                        <td>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                <?php
                                    $trangThaiClass = 'bg-gray-100 text-gray-800';
                                    if ($ht['trang_thai_hs_kc'] === 'da_ho_tro') $trangThaiClass = 'bg-green-100 text-green-800';
                                    elseif (in_array($ht['trang_thai_hs_kc'], ['cho_xem_xet', 'cho_duyet'])) $trangThaiClass = 'bg-yellow-100 text-yellow-800';
                                    elseif (in_array($ht['trang_thai_hs_kc'], ['khong_du_dieu_kien', 'huy_bo'])) $trangThaiClass = 'bg-red-100 text-red-800';
                                    echo $trangThaiClass;
                                ?>
                            ">
                                <?php echo htmlspecialchars($trangThaiOptions[$ht['trang_thai_hs_kc']] ?? ucfirst(str_replace('_', ' ', $ht['trang_thai_hs_kc'])), ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </td>
                        <td class="text-center sticky right-0 bg-white z-10 border-l border-gray-200">
                            <div class="flex justify-center items-center space-x-1">
                                <a href="<?php echo url('ho-tro-khan-cap/show/' . $ht['id']); ?>" title="Xem" class="text-gray-500 hover:text-gray-700 p-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </a>
                                <a href="<?php echo url('ho-tro-khan-cap/edit/' . $ht['id']); ?>" title="Sửa" class="text-blue-600 hover:text-blue-800 p-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </a>
                                <?php if (!in_array($ht['trang_thai_hs_kc'], ['da_ho_tro'])): ?>
                                <form action="<?php echo url('ho-tro-khan-cap/destroy/' . $ht['id']); ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa mục hỗ trợ này?');" class="inline-block">
                                    <button type="submit" title="Xóa" class="text-red-600 hover:text-red-800 p-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../layouts/_pagination.php'; ?>