<?php
// app/views/thon_xom/index.php
// Vars: $title, $thonXomList, $pagination, $currentController, $currentFilters

// Lấy flash message một lần
$flash = $this->getFlashMessage(); 
$successMessage = null;
$errorMessage = null; 
if ($flash) {
    if ($flash['type'] === 'success') {
        $successMessage = $flash['message'];
    } elseif ($flash['type'] === 'error') {
        $errorMessage = $flash['message'];
    }
}
?>
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-700"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
    <a href="<?php echo url('thon-xom/create'); ?>" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
        </svg>
        THÊM MỚI THÔN/TỔ DÂN PHỐ
    </a>
</div>

<!-- Form tìm kiếm -->
<div class="mb-4 bg-white p-4 rounded-lg shadow-sm">
    <!-- SỬA ĐỔI Ở ĐÂY: Đơn giản hóa form, loại bỏ logic if phức tạp -->
    <form action="" method="GET" class="flex items-end space-x-3">
        
        <!-- SỬA ĐỔI QUAN TRỌNG: Thêm input ẩn một cách tường minh -->
        <input type="hidden" name="url" value="thon-xom/index">

        <div class="flex-grow">
            <label for="search" class="form-label">Tìm kiếm:</label>
            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($currentFilters['searchTerm'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   placeholder="Nhập tên hoặc mã thôn..."
                   class="form-input w-full">
        </div>
        <input type="hidden" name="page" value="1"> 
        <div class="flex space-x-2">
             <button type="submit" class="btn btn-secondary">Lọc</button>
            <?php if (!empty($currentFilters['searchTerm'])): ?>
                <a href="<?php echo url('thon-xom/index'); ?>" class="btn bg-gray-300 hover:bg-gray-400 text-gray-700">Xóa lọc</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php if ($successMessage): ?>
    <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-400 rounded">
        <?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php endif; ?>
<?php if ($errorMessage): ?>
    <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-400 rounded">
         <?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php endif; ?>


<div class="overflow-x-auto bg-white rounded-lg shadow">
    <table class="table">
        <thead>
            <tr>
                <th class="px-4 py-3 w-10">STT</th>
                <!-- <th class="px-4 py-3">Mã Thôn</th> -->
                <th class="px-4 py-3">Tên Thôn/Xóm</th>
                <th class="px-4 py-3">Mô tả</th>
                <th class="px-4 py-3">Ngày tạo</th>
                <th class="px-4 py-3 text-center whitespace-nowrap">Hành động</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php if (empty($thonXomList)): ?>
                <tr>
                    <td colspan="6" class="text-center py-4 text-gray-500">Không tìm thấy thôn/xóm nào.</td>
                </tr>
            <?php else: ?>
                <?php $stt = ($pagination['currentPage'] - 1) * $pagination['limit'] + 1; ?>
                <?php foreach ($thonXomList as $tx): ?>
                    <tr>
                        <td class="px-4 py-3 text-center"><?php echo $stt++; ?></td>
                        <!-- <td class="px-4 py-3"><?php echo htmlspecialchars($tx['ma_thon'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td> -->
                        <td class="px-4 py-3">
                            <a href="<?php echo url('thon-xom/edit/' . $tx['id']); ?>" class="font-medium text-blue-600 hover:underline">
                                <?php echo htmlspecialchars($tx['ten_thon'], ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </td>
                        <td class="px-4 py-3">
                            <?php echo !empty($tx['mo_ta']) ? nl2br(htmlspecialchars(mb_strimwidth($tx['mo_ta'], 0, 70, "..."), ENT_QUOTES, 'UTF-8')) : ''; ?>
                        </td>
                        <td class="px-4 py-3"><?php echo !empty($tx['ngay_tao']) ? date('d/m/Y H:i', strtotime($tx['ngay_tao'])) : 'N/A'; ?></td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center items-center space-x-1">
                                <a href="<?php echo url('thon-xom/edit/' . $tx['id']); ?>" title="Sửa thôn/xóm" class="text-blue-600 hover:text-blue-800 p-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form action="<?php echo url('thon-xom/destroy/' . $tx['id']); ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thôn/xóm này? Các đối tượng thuộc thôn này sẽ được cập nhật về trạng thái không có thôn (nếu có).');" class="inline-block">
                                    <button type="submit" title="Xóa thôn/xóm" class="text-red-600 hover:text-red-800 p-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
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
 <?php
    $paginationBasePath = 'thon-xom/index';
    if (file_exists(__DIR__ . '/../layouts/_pagination.php')) {
        include __DIR__ . '/../layouts/_pagination.php';
    } else {
        echo '<p class="mt-4 text-red-500">Lỗi: Không tìm thấy file phân trang _pagination.php.</p>';
    }
 ?>