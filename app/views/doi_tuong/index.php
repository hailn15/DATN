<?php
// app/views/doi_tuong/index.php
// Truy cập các biến đã được truyền từ Controller:
// $title, $doiTuongList, $pagination

// Hàm trợ giúp để chuyển đổi giá trị ENUM sang text dễ đọc
function formatTrangThaiHoSoDt($statusKey) {
    $statusMap = [
        'moi_tao' => 'Mới tạo', 'dang_xu_ly_thong_tin' => 'Đang xử lý', 'da_xac_minh' => 'Đã xác minh',
        'cho_duyet_ho_so' => 'Chờ duyệt', 'da_duyet_thong_tin' => 'Đã duyệt', 'bi_tu_choi_thong_tin' => 'Bị từ chối',
    ];
    return $statusMap[$statusKey] ?? ucfirst(str_replace('_', ' ', $statusKey));
}
function getTrangThaiHoSoDtClass($statusKey) {
    switch ($statusKey) {
        case 'da_duyet_thong_tin': return 'bg-green-100 text-green-800';
        case 'bi_tu_choi_thong_tin': return 'bg-red-100 text-red-800';
        case 'dang_xu_ly_thong_tin': case 'cho_duyet_ho_so': return 'bg-yellow-100 text-yellow-800';
        default: return 'bg-blue-100 text-blue-800';
    }
}
?>
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-700"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
    <a href="<?php echo url('doi-tuong/create'); ?>" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
        Thêm mới
    </a>
</div>

<?php include __DIR__ . '/../layouts/_flash_messages.php'; ?>

<div class="mb-4 bg-white p-4 rounded-lg shadow-sm">
    <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        
        <input type="hidden" name="url" value="doi-tuong/index">

        <div>
            <label for="search" class="form-label">Tìm kiếm chung:</label>
            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($currentFilters['searchTerm'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   placeholder="Nhập mã, tên hoặc CCCD..." class="form-input">
        </div>
        
        <!-- <div>
            <label for="loai_doi_tuong_filter" class="form-label">Loại đối tượng:</label>
            <select id="loai_doi_tuong_filter" name="loai_doi_tuong_id" class="form-input">
                <option value="">-- Tất cả loại --</option>
                <?php foreach ($loaiDoiTuongOptions as $ldt): ?>
                    <option value="<?php echo $ldt['id']; ?>" <?php echo (isset($currentFilters['loai_doi_tuong_id']) && $currentFilters['loai_doi_tuong_id'] == $ldt['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($ldt['ten_loai'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div> -->

        <div>
            <label for="trang_thai_filter" class="form-label">Trạng thái hồ sơ:</label>
            <select id="trang_thai_filter" name="trang_thai_ho_so_dt" class="form-input">
                <option value="">-- Tất cả trạng thái --</option>
                <?php foreach ($trangThaiOptions as $value => $label): ?>
                    <option value="<?php echo $value; ?>" <?php echo (isset($currentFilters['trang_thai_ho_so_dt']) && $currentFilters['trang_thai_ho_so_dt'] == $value) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        
        <input type="hidden" name="page" value="1"> 
        
        <div class="flex space-x-2">
            <button type="submit" class="btn btn-secondary flex-grow">Lọc</button>
            <?php if (!empty($currentFilters['searchTerm']) || !empty($currentFilters['loai_doi_tuong_id']) || !empty($currentFilters['trang_thai_ho_so_dt'])): ?>
                <a href="<?php echo url('doi-tuong/index'); ?>" class="btn bg-gray-300 hover:bg-gray-400 text-gray-700 flex-grow">Xóa lọc</a>
            <?php endif; ?>
            <!-- <a href="<?php echo url('doi-tuong/exportCsv?') . http_build_query(array_filter($currentFilters)); ?>" 
                target="_blank"
                class="btn bg-green-600 text-white hover:bg-green-700 flex-grow flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Xuất CSV
                </a> -->
        </div>       
    </form>
</div>

<div class="overflow-x-auto bg-white rounded-lg shadow">
    <table class="table">
        <thead>
            <tr>
                <th class="w-10">STT</th>
                <th>Mã ĐT</th>
                <th>Họ và Tên</th>
                <th>Ngày Sinh</th>
                <th class="whitespace-nowrap">Hồ sơ đã có</th>
                <th>Trạng thái</th>
                <th class="text-center">Hành động</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php if (empty($doiTuongList)): ?>
                <tr><td colspan="7" class="text-center py-4 text-gray-500">Không tìm thấy đối tượng nào.</td></tr>
            <?php else: ?>
                <?php $stt = ($pagination['currentPage'] - 1) * $pagination['limit'] + 1; ?>
                <?php foreach ($doiTuongList as $dt): ?>
                    <tr>
                        <td class="text-center"><?php echo $stt++; ?></td>
                        <td><?php echo htmlspecialchars($dt['ma_doi_tuong'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <a href="<?php echo url('doi-tuong/edit/' . $dt['id']); ?>" class="font-medium text-blue-600 hover:underline">
                                <?php echo htmlspecialchars($dt['ho_ten'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </td>
                        <td><?php echo !empty($dt['ngay_sinh']) ? date('d/m/Y', strtotime($dt['ngay_sinh'])) : ''; ?></td>
                        <td class="whitespace-nowrap">
                            <div class="flex flex-wrap gap-1">
                                <?php if (!empty($dt['ho_so_tro_cap_count']) && $dt['ho_so_tro_cap_count'] > 0): ?>
                                    <a href="<?php echo url('ho-so-tro-cap/index?doi_tuong_id=' . $dt['id']); ?>" 
                                       class="px-2 py-0.5 text-xs font-medium text-sky-800 bg-sky-100 rounded-full hover:bg-sky-200"
                                       title="Xem <?php echo $dt['ho_so_tro_cap_count']; ?> hồ sơ trợ cấp">
                                        Trợ cấp (<?php echo $dt['ho_so_tro_cap_count']; ?>)
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($dt['ho_so_cham_soc_count']) && $dt['ho_so_cham_soc_count'] > 0): ?>
                                    <a href="<?php echo url('ho-so-cham-soc-cong-dong/index?doi_tuong_id=' . $dt['id']); ?>" 
                                       class="px-2 py-0.5 text-xs font-medium text-green-800 bg-green-100 rounded-full hover:bg-green-200"
                                       title="Xem <?php echo $dt['ho_so_cham_soc_count']; ?> hồ sơ chăm sóc">
                                        Chăm sóc (<?php echo $dt['ho_so_cham_soc_count']; ?>)
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($dt['ho_so_khan_cap_count']) && $dt['ho_so_khan_cap_count'] > 0): ?>
                                    <a href="<?php echo url('ho-tro-khan-cap/index?doi_tuong_id=' . $dt['id']); ?>" 
                                       class="px-2 py-0.5 text-xs font-medium text-orange-800 bg-orange-100 rounded-full hover:bg-orange-200"
                                       title="Xem <?php echo $dt['ho_so_khan_cap_count']; ?> hồ sơ khẩn cấp">
                                        Khẩn cấp (<?php echo $dt['ho_so_khan_cap_count']; ?>)
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo getTrangThaiHoSoDtClass($dt['trang_thai_ho_so_dt'] ?? ''); ?>">
                                <?php echo htmlspecialchars(formatTrangThaiHoSoDt($dt['trang_thai_ho_so_dt'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="flex justify-center items-center space-x-1">
                                <button type="button" class="table-action-button text-indigo-600 open-ho-so-modal"
                                        data-doituong-id="<?php echo $dt['id']; ?>"
                                        data-doituong-ten="<?php echo htmlspecialchars($dt['ho_ten'] ?? 'Đối tượng', ENT_QUOTES, 'UTF-8'); ?>"
                                        title="Thêm/Xem Hồ sơ Hỗ trợ">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                </button>
                                <a href="<?php echo url('doi-tuong/edit/' . $dt['id']); ?>" title="Sửa" class="table-action-button text-blue-600">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </a>
                                <form action="<?php echo url('doi-tuong/destroy/' . $dt['id']); ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đối tượng này?');" class="inline-block">
                                    <button type="submit" title="Xóa" class="table-action-button text-red-600">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
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

 <!-- Phân trang -->
 <?php include __DIR__ . '/../layouts/_pagination.php'; ?>

<!-- Modal HTML -->
<div id="hoSoActionsModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black bg-opacity-50 transition-opacity" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- ... (Nội dung Modal giữ nguyên) ... -->
    <div class="bg-white rounded-lg shadow-xl transform transition-all sm:max-w-md sm:w-full m-4">
        <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                     <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-text">
                        Chọn loại Hồ sơ Hỗ trợ
                    </h3>
                    <div class="mt-4 space-y-2" id="modal-actions-container">
                         <!-- Content will be generated by JS -->
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button type="button" id="closeHoSoModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                Đóng
            </button>
        </div>
    </div>
</div>

 <style>
    .table-action-button { @apply p-1 rounded-md hover:bg-gray-100 transition-colors; }
 </style>

<?php
// === BƯỚC 1: CHUẨN BỊ DỮ LIỆU URL TRONG PHP ===
// Tạo một mảng PHP với các URL được tạo bởi hàm url()
$hoSoTypesData = [
    [ 
        'key' => 'tc', 
        'name' => 'Trợ cấp Hàng tháng', 
        'indexUrl' => url('ho-so-tro-cap/index'), // Sử dụng hàm url()
        'createUrl' => url('ho-so-tro-cap/create/'), // Sử dụng hàm url()
        'color' => 'sky' 
    ],
    [ 
        'key' => 'cs', 
        'name' => 'Chăm sóc Cộng đồng', 
        'indexUrl' => url('ho-so-cham-soc-cong-dong/index'), 
        'createUrl' => url('ho-so-cham-soc-cong-dong/create/'), 
        'color' => 'green' 
    ],
    [ 
        'key' => 'kc', 
        'name' => 'Hỗ trợ Khẩn cấp', 
        'indexUrl' => url('ho-tro-khan-cap/index'), 
        'createUrl' => url('ho-tro-khan-cap/create/'), 
        'color' => 'orange' 
    ]
];
?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('hoSoActionsModal');
    const modalTitleElement = document.getElementById('modal-title-text');
    const modalActionsContainer = document.getElementById('modal-actions-container');
    const closeModalButton = document.getElementById('closeHoSoModal');

    const hoSoTypes = <?php echo json_encode($hoSoTypesData); ?>;

    document.querySelectorAll('.open-ho-so-modal').forEach(button => {
        button.addEventListener('click', function () {
            const doiTuongId = this.dataset.doituongId;
            const doiTuongTen = this.dataset.doituongTen;

            modalTitleElement.textContent = `Hồ sơ Hỗ trợ cho: ${doiTuongTen}`;
            
            let actionsHtml = '';
            hoSoTypes.forEach((type, index) => {
                if (index > 0) actionsHtml += '<hr class="my-3">';
                
                // === SỬA LỖI Ở ĐÂY: THAY '?' BẰNG '&' CHO LINK XEM DANH SÁCH ===
                // Vì type.indexUrl đã chứa "?url=..."
                actionsHtml += `
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-1">${type.name}</p>
                        <a href="${type.indexUrl}&doi_tuong_id=${doiTuongId}" class="modal-action-link block w-full text-center py-2 px-3 text-sm font-medium text-white bg-${type.color}-500 hover:bg-${type.color}-600 rounded-md shadow-sm mb-1.5">
                            Xem DS ${type.name}
                        </a>
                        <a href="${type.createUrl}${doiTuongId}" class="modal-action-link block w-full text-center py-2 px-3 text-sm font-medium text-white bg-${type.color}-600 hover:bg-${type.color}-700 rounded-md shadow-sm">
                            Thêm mới ${type.name}
                        </a>
                    </div>
                `;
            });

            modalActionsContainer.innerHTML = actionsHtml;
            modal.classList.remove('hidden');
            modal.classList.add('flex'); 
        });
    });

    function closeModalFn() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    if (closeModalButton) closeModalButton.addEventListener('click', closeModalFn);
    modal.addEventListener('click', (event) => { if (event.target === modal) closeModalFn(); });
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape' && !modal.classList.contains('hidden')) closeModalFn(); });
});
</script>