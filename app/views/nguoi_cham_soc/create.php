<?php
// app/views/nguoi_cham_soc/create.php
// Vars: $title, $ncs, $errors, $oldData
$ncs = $oldData; // Ưu tiên dữ liệu cũ nếu có lỗi
?>
<h1 class="text-2xl font-semibold text-gray-700 mb-6"><?php echo htmlspecialchars($title); ?></h1>

<?php include __DIR__ . '/../layouts/_flash_messages.php'; ?>

<form action="<?php echo url('nguoi-cham-soc/store'); ?>" method="POST" class="bg-white p-6 rounded-lg shadow-md">
    <?php include '_form.php'; ?>
    <div class="mt-6 pt-4 border-t flex justify-end space-x-3">
        <a href="<?php echo url('nguoi-cham-soc/index'); ?>" class="btn btn-secondary">Hủy</a>
        <button type="submit" class="btn btn-primary">Lưu</button>
    </div>
</form>