<?php
// app/views/nguoi_cham_soc/index.php
// Vars: $title, $nguoiChamSocList, $pagination, $currentFilters
?>
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-700"><?php echo htmlspecialchars($title); ?></h1>
    <a href="<?php echo url('nguoi-cham-soc/create'); ?>" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        Thêm mới
    </a>
</div>

<div class="mb-4 bg-white p-4 rounded-lg shadow-sm">
    <!-- SỬA ĐỔI Ở ĐÂY: Đặt action="" và thêm input hidden cho 'url' -->
    <form action="" method="GET" class="flex items-end space-x-4">
        
        <!-- SỬA ĐỔI QUAN TRỌNG: Thêm input ẩn để giữ lại route khi submit form -->
        <input type="hidden" name="url" value="nguoi-cham-soc/index">

        <div class="flex-grow">
            <label for="search" class="form-label">Tìm kiếm:</label>
            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($currentFilters['searchTerm'] ?? ''); ?>" placeholder="Tên, mã, CCCD, SĐT..." class="form-input">
        </div>
        <input type="hidden" name="page" value="1">
        <button type="submit" class="btn btn-secondary">Lọc</button>
        <?php if (!empty($currentFilters['searchTerm'])): ?>
            <a href="<?php echo url('nguoi-cham-soc/index'); ?>" class="btn bg-gray-300 text-gray-700">Xóa lọc</a>
        <?php endif; ?>
    </form>
</div>

<div class="overflow-x-auto bg-white rounded-lg shadow">
    <table class="table">
        <thead>
            <tr>
                <th class="w-1/12">STT</th>
                <th>Họ tên (Số định danh)</th>
                <th>Ngày sinh</th>
                <th>Giới tính</th>
                <!-- <th>CCCD</th> -->
                <th>Số điện thoại</th>
                <th>Địa chỉ</th>
                <th class="w-1/12 text-center">Hành động</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            <?php if (empty($nguoiChamSocList)): ?>
                <tr><td colspan="8" class="text-center py-4">Không có dữ liệu.</td></tr>
            <?php else: ?>
                <?php $stt = ($pagination['currentPage'] - 1) * $pagination['limit'] + 1; ?>
                <?php foreach ($nguoiChamSocList as $ncs): ?>
                <tr>
                    <td class="text-center"><?php echo $stt++; ?></td>
                    <td>
                        <p class="font-medium text-gray-800"><?php echo htmlspecialchars($ncs['ho_ten']); ?></p>
                        <?php if(!empty($ncs['cccd'])): ?>
                        <p class="text-xs text-gray-500">(<?php echo htmlspecialchars($ncs['cccd']); ?>)</p>
                        <?php endif; ?>
                    </td>
                    <td><?php echo !empty($ncs['ngay_sinh']) ? date('d/m/Y', strtotime($ncs['ngay_sinh'])) : 'N/A'; ?></td>
                    <td><?php echo htmlspecialchars($ncs['gioi_tinh'] ?? 'N/A'); ?></td>
                    <!-- <td><?php echo htmlspecialchars($ncs['cccd'] ?? 'N/A'); ?></td> -->
                    <td><?php echo htmlspecialchars($ncs['so_dien_thoai'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($ncs['dia_chi'] ?? 'N/A'); ?></td>
                    <td class="text-center">
                        <div class="flex justify-center items-center space-x-1">
                            <a href="<?php echo url('nguoi-cham-soc/edit/' . $ncs['id']); ?>" title="Sửa" class="table-action-button text-blue-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </a>
                            <form action="<?php echo url('nguoi-cham-soc/destroy/' . $ncs['id']); ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người chăm sóc này? Các hồ sơ liên quan sẽ không bị xóa.');" class="inline-block">
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

<?php include __DIR__ . '/../layouts/_pagination.php'; ?>