<?php
// Vars: $title, $users, $pagination, $quyenOptions, $currentFilters
?>
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-700"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
    <a href="<?php echo url('user/create'); ?>" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
        Thêm người dùng
    </a>
</div>

<!-- Form tìm kiếm và lọc -->
<div class="mb-4 bg-white p-4 rounded-lg shadow-sm">
    <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        <input type="hidden" name="url" value="user/index">
        <div>
            <label for="search" class="form-label">Tìm kiếm (Tên, Email...):</label>
            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($currentFilters['searchTerm'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="form-input">
        </div>
        <div>
            <label for="quyen_filter" class="form-label">Quyền:</label>
            <select id="quyen_filter" name="quyen" class="form-input">
                <option value="">-- Tất cả các quyền --</option>
                <?php foreach ($quyenOptions as $value => $label): ?>
                    <option value="<?php echo $value; ?>" <?php echo (isset($currentFilters['quyen']) && $currentFilters['quyen'] == $value) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex space-x-2">
            <button type="submit" class="btn btn-secondary flex-grow">Lọc</button>
            <a href="<?php echo url('user/index'); ?>" class="btn bg-gray-300 hover:bg-gray-400 text-gray-700 flex-grow">Xóa lọc</a>
        </div>
    </form>
</div>

<div class="overflow-x-auto bg-white rounded-lg shadow">
    <table class="table">
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên đăng nhập</th>
                <th>Họ và Tên</th>
                <th>Quyền</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th class="text-center">Hành động</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php if (empty($users)): ?>
                <tr><td colspan="7" class="text-center py-4">Không có người dùng nào.</td></tr>
            <?php else: ?>
                <?php $stt = ($pagination['currentPage'] - 1) * $pagination['limit'] + 1; ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="text-center"><?php echo $stt++; ?></td>
                        <td class="font-medium"><?php echo htmlspecialchars($user['ten_dang_nhap']); ?></td>
                        <td><?php echo htmlspecialchars($user['ho_ten']); ?></td>
                        <td>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?php echo $user['quyen'] == 'admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'; ?>">
                                <?php echo htmlspecialchars($quyenOptions[$user['quyen']] ?? ucfirst($user['quyen'])); ?>
                            </span>
                        </td>
                        <td>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $user['trang_thai'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                <?php echo $user['trang_thai'] ? 'Hoạt động' : 'Đã khóa'; ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($user['ngay_tao'])); ?></td>
                        <td class="text-center">
                            <div class="flex justify-center items-center space-x-1">
                                <a href="<?php echo url('user/edit/' . $user['id']); ?>" title="Sửa" class="text-blue-600 hover:text-blue-800 p-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </a>
                                <?php if (getCurrentUser()['id'] != $user['id']): // Không cho phép tự khóa ?>
                                <form action="<?php echo url('user/toggleStatus/' . $user['id']); ?>" method="POST" onsubmit="return confirm('Bạn có chắc muốn thay đổi trạng thái của người dùng này?');" class="inline-block">
                                    <button type="submit" title="<?php echo $user['trang_thai'] ? 'Khóa tài khoản' : 'Mở khóa tài khoản'; ?>" class="p-1 <?php echo $user['trang_thai'] ? 'text-yellow-600 hover:text-yellow-800' : 'text-green-600 hover:text-green-800'; ?>">
                                        <?php if ($user['trang_thai']): ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" /></svg>
                                        <?php else: ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a5 5 0 00-5 5v2a2 2 0 00-2 2v5a2 2 0 002 2h10a2 2 0 002-2v-5a2 2 0 00-2-2H7V7a3 3 0 015.905-.75 1 1 0 001.937-.5A5.002 5.002 0 0010 2z" /></svg>
                                        <?php endif; ?>
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