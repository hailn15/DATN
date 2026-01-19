<?php
// $title, $vanBanList được truyền từ UtilityController
?>
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-700"><?php echo htmlspecialchars($title ?? 'Văn bản Chính sách Tham khảo', ENT_QUOTES, 'UTF-8'); ?></h1>
</div>

<?php $flash = $this->getFlashMessage(); ?>
<?php if ($flash): ?>
    <div class="mb-4 p-4 rounded <?php echo $flash['type'] == 'success' ? 'bg-green-100 text-green-700 border-green-400' : 'bg-red-100 text-red-700 border-red-400'; ?>">
        <?php echo htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php endif; ?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <p class="text-gray-700 mb-4">
        Dưới đây là danh mục một số văn bản chính sách quan trọng đang được áp dụng liên quan đến công tác bảo trợ xã hội.
        Cán bộ cần nắm vững các quy định này để thực hiện đúng quy trình và đảm bảo quyền lợi cho đối tượng.
    </p>

    <?php if (empty($vanBanList)): ?>
        <p class="text-gray-600">Hiện chưa có thông tin văn bản chính sách nào được cập nhật.</p>
    <?php else: ?>
        <ul class="space-y-5">
    <?php foreach ($vanBanList as $index => $vb): ?>
        <li class="pb-4 <?php if($index < count($vanBanList) - 1) echo 'border-b border-gray-200'; ?>">
            <h3 class="text-md font-semibold text-indigo-700 mb-1">
                <?php echo ($index + 1) . ". " . htmlspecialchars($vb['ten_van_ban'] ?? 'Chưa có tên văn bản', ENT_QUOTES, 'UTF-8'); ?>
            </h3>

            <?php if (!empty($vb['ghi_chu'])): ?>
                <p class="text-sm text-gray-600 pl-4 italic">
                    <?php echo htmlspecialchars($vb['ghi_chu'], ENT_QUOTES, 'UTF-8'); ?>
                </p>
            <?php endif; ?>

            <?php if (!empty($vb['file_path'])): ?>
                <p class="text-sm text-blue-600 hover:underline pl-4 mt-1">
                    <a href="<?php echo BASE_URL . '/' . ltrim($vb['file_path'], '/'); ?>" 
                       download 
                       target="_blank">
                       📄 Tải file PDF đính kèm
                    </a>
                </p>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>

    <?php endif; ?>

    <div class="mt-8 text-sm text-gray-500">
        <p><strong>Lưu ý:</strong> Đây là danh sách tham khảo. Cán bộ cần thường xuyên cập nhật các văn bản mới nhất từ các cơ quan có thẩm quyền và các văn bản hướng dẫn cụ thể của địa phương (tỉnh, huyện, xã).</p>
        <?php if (isset($currentUser) && $currentUser['role'] == 'admin'): ?>
             <p class="mt-2">Quản trị viên có thể cập nhật danh sách này bằng cách sửa đổi trực tiếp trong file controller (`UtilityController.php`, action `vanBanChinhSach`) hoặc phát triển chức năng quản lý văn bản từ cơ sở dữ liệu.</p>
        <?php endif; ?>
    </div>
</div>