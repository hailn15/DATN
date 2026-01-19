<?php
// View: app/views/ho_so_tro_cap/index.php
// Vars: $title, $hoSoList, $pagination, $trangThaiOptions, $currentFilters, $doiTuongContext
?>
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-700"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
    <?php if (isset($doiTuongContext) && !empty($doiTuongContext['id'])): ?>
        <a href="<?php echo url('ho-so-tro-cap/create/' . $doiTuongContext['id']); ?>" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Thêm HS Trợ cấp cho "<?php echo htmlspecialchars($doiTuongContext['ho_ten'], ENT_QUOTES, 'UTF-8'); ?>"
        </a>
    <?php else: ?>
         <button type="button" id="openThemMoiHoSoModal" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Thêm HS Trợ cấp
        </button>
    <?php endif; ?>
</div>

<!-- Form tìm kiếm và lọc -->
<div class="mb-4 bg-white p-4 rounded-lg shadow-sm">
    <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        
        <input type="hidden" name="url" value="ho-so-tro-cap/index">

        <div>
            <label for="search" class="form-label">Tìm kiếm:</label>
            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($currentFilters['searchTerm'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   placeholder="Mã HS, Tên/Mã ĐT, Tên Mức..."
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
                <a href="<?php echo url('ho-so-tro-cap/index' . (!empty($doiTuongContext['id']) ? '?doi_tuong_id='.$doiTuongContext['id'] : '') ); ?>" class="btn bg-gray-300 hover:bg-gray-400 text-gray-700 flex-grow">Xóa lọc</a>
            <?php endif; ?>

            <!-- ====== NÚT XUẤT EXCEL MỚI ====== -->
            <a href="<?php echo url('ho-so-tro-cap/exportCsv?') . http_build_query(array_filter($currentFilters)); ?>" 
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
        <thead class="bg-gray-50">
            <tr>
                <th class="w-1/12">STT</th>
                <th>Mã Hồ sơ</th>
                <th>Đối tượng (Mã)</th>
                <th>Mức Trợ Cấp (Mã)</th>
                <th>Ngày BĐ Hưởng</th>
                <th class="text-right">Số tiền hưởng</th>
                <th>Trạng thái</th>
                <th>Ngày duyệt</th>
                <th>Người duyệt</th>
                <!-- ====== CỘT HÀNH ĐỘNG ĐƯỢC GHIM ====== -->
                <th class="w-1/12 text-center sticky right-0 bg-gray-50 z-10 border-l border-gray-200">Hành động</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php if (empty($hoSoList)): ?>
                <tr>
                    <td colspan="10" class="text-center py-4 text-gray-500">Không tìm thấy hồ sơ trợ cấp nào.</td>
                </tr>
            <?php else: ?>
                <?php $stt = ($pagination['currentPage'] - 1) * $pagination['limit'] + 1; ?>
                <?php foreach ($hoSoList as $hs): ?>
                    <tr>
                        <td class="text-center"><?php echo $stt++; ?></td>
                        <td>
                            <a href="<?php echo url('ho-so-tro-cap/show/' . $hs['id']); ?>" class="font-medium text-blue-600 hover:underline">
                                <?php echo htmlspecialchars($hs['ma_ho_so'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </td>
                        <td>
                            <a href="<?php echo url('doi-tuong/edit/' . $hs['doi_tuong_id']); ?>" target="_blank" class="text-blue-600 hover:underline" title="Xem chi tiết đối tượng">
                                <?php echo htmlspecialchars($hs['ten_doi_tuong'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>
                                <br><span class="text-xs text-gray-500">(<?php echo htmlspecialchars($hs['ma_doi_tuong'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>)</span>
                            </a>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($hs['ten_muc_tro_cap'] ?? 'Chưa rõ', ENT_QUOTES, 'UTF-8'); ?>
                            <!-- <?php if (!empty($hs['ma_muc_tro_cap'])): ?>
                                <br><span class="text-xs text-gray-500">(<?php echo htmlspecialchars($hs['ma_muc_tro_cap'], ENT_QUOTES, 'UTF-8'); ?>)</span>
                            <?php endif; ?> -->
                        </td>
                        <td><?php echo !empty($hs['ngay_bat_dau_huong']) ? date('d/m/Y', strtotime($hs['ngay_bat_dau_huong'])) : ''; ?></td>
                        <td class="text-right"><?php echo !empty($hs['muc_tro_cap_hang_thang']) ? number_format($hs['muc_tro_cap_hang_thang'], 0, ',', '.') . ' đ' : ''; ?></td>
                        <td>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                <?php
                                    $trangThaiClass = 'bg-gray-100 text-gray-800'; 
                                    if ($hs['trang_thai'] === 'da_phe_duyet_dang_huong') $trangThaiClass = 'bg-green-100 text-green-800';
                                    elseif (in_array($hs['trang_thai'], ['cho_xem_xet', 'cho_duyet'])) $trangThaiClass = 'bg-yellow-100 text-yellow-800';
                                    elseif (in_array($hs['trang_thai'], ['khong_du_dieu_kien', 'da_dung_huong', 'da_chuyen_co_so_khac'])) $trangThaiClass = 'bg-red-100 text-red-800';
                                    elseif ($hs['trang_thai'] === 'tam_dung_huong') $trangThaiClass = 'bg-orange-100 text-orange-800';
                                    echo $trangThaiClass;
                                ?>
                            ">
                                <?php echo htmlspecialchars($trangThaiOptions[$hs['trang_thai']] ?? ucfirst(str_replace('_', ' ', $hs['trang_thai'])), ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </td>
                        <td><?php echo !empty($hs['ngay_duyet']) ? date('d/m/Y', strtotime($hs['ngay_duyet'])) : ''; ?></td>
                        <td><?php echo htmlspecialchars($hs['ten_nguoi_duyet'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <!-- ====== Ô HÀNH ĐỘNG ĐƯỢC GHIM ====== -->
                        <td class="text-center sticky right-0 bg-white z-10 border-l border-gray-200">
                            <div class="flex justify-center items-center space-x-1">
                                <a href="<?php echo url('ho-so-tro-cap/show/' . $hs['id']); ?>" title="Xem" class="text-gray-500 hover:text-gray-700 p-1">
                                     <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </a>
                                <a href="<?php echo url('ho-so-tro-cap/edit/' . $hs['id']); ?>" title="Sửa" class="text-blue-600 hover:text-blue-800 p-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </a>
                                <?php if (!in_array($hs['trang_thai'], ['da_phe_duyet_dang_huong'])): ?>
                                <form action="<?php echo url('ho-so-tro-cap/destroy/' . $hs['id']); ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa hồ sơ này? Hành động này không thể hoàn tác.');" class="inline-block">
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

<!-- Phân trang -->
<?php include __DIR__ . '/../layouts/_pagination.php'; ?>

<!-- Modal Tìm kiếm Đối tượng để tạo Hồ sơ -->
<div id="timDoiTuongModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black bg-opacity-50 transition-opacity" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="bg-white rounded-lg shadow-xl transform transition-all sm:max-w-lg sm:w-full m-4">
        <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Thêm Hồ sơ mới - Chọn Đối tượng
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Chỉ những đối tượng chưa có hồ sơ trợ cấp đang chờ hoặc đang hưởng mới được hiển thị.
                        </p>
                    </div>
                    <div class="mt-4">
                        <form id="searchDoiTuongForm" class="flex items-center space-x-2">
                            <input type="text" id="doiTuongSearchInput" placeholder="Nhập tên, mã, CCCD đối tượng..." class="form-input flex-grow" required minlength="2">
                            <button type="submit" class="btn btn-secondary">Tìm</button>
                        </form>
                    </div>
                    <div id="doiTuongSearchResults" class="mt-4 max-h-60 overflow-y-auto pr-2">
                        <!-- Kết quả tìm kiếm sẽ được chèn vào đây -->
                        <p class="text-gray-500 text-sm">Vui lòng nhập từ khóa và nhấn "Tìm" để tìm kiếm đối tượng.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button type="button" id="closeTimDoiTuongModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                Đóng
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const openModalButton = document.getElementById('openThemMoiHoSoModal');
    if (!openModalButton) return;

    const modal = document.getElementById('timDoiTuongModal');
    const closeModalButton = document.getElementById('closeTimDoiTuongModal');
    const searchForm = document.getElementById('searchDoiTuongForm');
    const searchInput = document.getElementById('doiTuongSearchInput');
    const searchResultsContainer = document.getElementById('doiTuongSearchResults');

    const createUrlBase = '<?php echo url("ho-so-tro-cap/create/"); ?>';

    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        searchInput.focus();
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        searchInput.value = '';
        searchResultsContainer.innerHTML = '<p class="text-gray-500 text-sm">Vui lòng nhập từ khóa và nhấn "Tìm" để tìm kiếm đối tượng.</p>';
    }

    openModalButton.addEventListener('click', openModal);
    closeModalButton.addEventListener('click', closeModal);

    modal.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    searchForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const searchTerm = searchInput.value.trim();
        if (searchTerm.length < 2) {
            searchResultsContainer.innerHTML = '<p class="text-red-500 text-sm">Vui lòng nhập ít nhất 2 ký tự để tìm kiếm.</p>';
            return;
        }

        searchResultsContainer.innerHTML = `
            <div class="flex items-center justify-center py-4">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-gray-600">Đang tìm kiếm...</span>
            </div>`;
        
        // <<< THAY ĐỔI QUAN TRỌNG: Gọi đến URL mới
        const searchUrl = `<?php echo url('doi-tuong/searchJsonEligibleHstc'); ?>&q=${encodeURIComponent(searchTerm)}`;

        fetch(searchUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Lỗi mạng hoặc server.');
                }
                return response.json();
            })
            .then(data => {
                if (data && data.length > 0) {
                    let resultsHtml = data.map(dt => {
                        const ngaySinh = dt.ngay_sinh ? new Date(dt.ngay_sinh).toLocaleDateString('vi-VN') : 'N/A';
                        return `
                            <a href="${createUrlBase}${dt.id}" class="block p-3 hover:bg-gray-100 rounded-md transition duration-150 ease-in-out border-b last:border-b-0">
                                <p class="font-semibold text-blue-600">${dt.ho_ten}</p>
                                <p class="text-sm text-gray-600">Mã ĐT: ${dt.ma_doi_tuong} - Ngày sinh: ${ngaySinh}</p>
                            </a>
                        `;
                    }).join('');
                    searchResultsContainer.innerHTML = resultsHtml;
                } else {
                    searchResultsContainer.innerHTML = '<p class="text-gray-600 text-center py-4">Không tìm thấy đối tượng nào phù hợp.</p>';
                }
            })
            .catch(error => {
                console.error('Lỗi khi tìm kiếm đối tượng:', error);
                searchResultsContainer.innerHTML = '<p class="text-red-600 text-center py-4">Có lỗi xảy ra trong quá trình tìm kiếm. Vui lòng thử lại.</p>';
            });
    });
});
</script>