<?php
// Truy cập biến $pagination từ view cha (index.php)
if (isset($pagination) && $pagination['totalPages'] > 1):
    $currentPage = $pagination['currentPage'];
    $totalPages = $pagination['totalPages'];
    $baseUrl = url('doi-tuong/index'); // Lấy URL cơ sở
    $queryParams = [];
    if (!empty($pagination['searchTerm'])) {
        $queryParams['search'] = $pagination['searchTerm'];
    }

    // Hàm tạo link phân trang với query params
    function createPageLink($baseUrl, $page, $queryParams) {
        $queryParams['page'] = $page;
        return $baseUrl . '&' . http_build_query($queryParams);
    }
?>
<div class="mt-6 flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 rounded-b-lg shadow">
    <div class="flex flex-1 justify-between sm:hidden">
        <?php if ($currentPage > 1): ?>
            <a href="<?php echo createPageLink($baseUrl, $currentPage - 1, $queryParams); ?>" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Trước</a>
        <?php else: ?>
             <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-400 cursor-not-allowed">Trước</span>
        <?php endif; ?>
        <?php if ($currentPage < $totalPages): ?>
            <a href="<?php echo createPageLink($baseUrl, $currentPage + 1, $queryParams); ?>" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Sau</a>
         <?php else: ?>
             <span class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-400 cursor-not-allowed">Sau</span>
        <?php endif; ?>
    </div>
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-gray-700">
                Hiển thị từ
                <span class="font-medium"><?php echo min(($currentPage - 1) * $pagination['limit'] + 1, $pagination['totalRecords']); ?></span>
                đến
                <span class="font-medium"><?php echo min($currentPage * $pagination['limit'], $pagination['totalRecords']); ?></span>
                trong tổng số
                <span class="font-medium"><?php echo number_format($pagination['totalRecords']); ?></span>
                kết quả
            </p>
        </div>
        <div>
            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                <!-- Previous Button -->
                <?php if ($currentPage > 1): ?>
                     <a href="<?php echo createPageLink($baseUrl, $currentPage - 1, $queryParams); ?>" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                        <span class="sr-only">Trước</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" /></svg>
                    </a>
                <?php else: ?>
                     <span class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-300 ring-1 ring-inset ring-gray-300 cursor-not-allowed">
                        <span class="sr-only">Trước</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" /></svg>
                    </span>
                <?php endif; ?>

                <!-- Page Numbers -->
                <?php
                    $range = 2; // Số trang hiển thị ở mỗi bên của trang hiện tại
                    for ($i = 1; $i <= $totalPages; $i++):
                        if ($i == 1 || $i == $totalPages || ($i >= $currentPage - $range && $i <= $currentPage + $range)):
                ?>
                            <a href="<?php echo createPageLink($baseUrl, $i, $queryParams); ?>"
                               class="relative inline-flex items-center px-4 py-2 text-sm font-semibold <?php echo ($i == $currentPage) ? 'z-10 bg-blue-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600' : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php elseif ($i == $currentPage - $range - 1 || $i == $currentPage + $range + 1): ?>
                            <span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0">...</span>
                        <?php endif; ?>
                    <?php endfor; ?>

                <!-- Next Button -->
                 <?php if ($currentPage < $totalPages): ?>
                    <a href="<?php echo createPageLink($baseUrl, $currentPage + 1, $queryParams); ?>" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                        <span class="sr-only">Sau</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" /></svg>
                    </a>
                 <?php else: ?>
                      <span class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-300 ring-1 ring-inset ring-gray-300 cursor-not-allowed">
                         <span class="sr-only">Sau</span>
                         <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" /></svg>
                     </span>
                 <?php endif; ?>
            </nav>
        </div>
    </div>
</div>
<?php endif; ?>