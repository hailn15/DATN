<?php
// app/views/nguoi_cham_soc/edit.php
// Vars: $title, $ncs, $errors, $id
?>
<h1 class="text-2xl font-semibold text-gray-700 mb-6"><?php echo htmlspecialchars($title); ?></h1>

<?php include __DIR__ . '/../layouts/_flash_messages.php'; ?>

<form action="<?php echo url('nguoi-cham-soc/update/' . $id); ?>" method="POST" class="bg-white p-6 rounded-lg shadow-md">
    <?php include '_form.php'; ?>
    <div class="mt-6 pt-4 border-t flex justify-end space-x-3">
        <a href="<?php echo url('nguoi-cham-soc/index'); ?>" class="btn btn-secondary">Quay lại danh sách</a>
        <button type="submit" class="btn btn-primary">Cập nhật</button>
    </div>
</form>