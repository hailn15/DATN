<?php
// app/views/thon_xom/create.php
// Vars: $title, $currentController, $oldData, $errors
$displayData = $oldData; // Data to repopulate form
$isEdit = false; // Flag for _form.php

// Lấy flash message một lần (nếu có) từ BaseController
// BaseController::getFlashMessage() sẽ tự động xóa message khỏi session.
$flash = $this->getFlashMessage(); 
$generalErrorMessageFromFlash = null;
if ($flash && $flash['type'] === 'error') {
    $generalErrorMessageFromFlash = $flash['message'];
}
?>
<h1 class="text-2xl font-semibold text-gray-700 mb-6"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>

<?php if ($generalErrorMessageFromFlash): ?>
    <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-400 rounded">
        <strong class="font-bold">Lỗi:</strong>
        <p><?php echo htmlspecialchars($generalErrorMessageFromFlash, ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
<?php endif; ?>

<?php if (!empty($errors)): // $errors này là từ session form_errors ?>
<div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-400 rounded">
    <strong class="font-bold">Vui lòng sửa các lỗi sau:</strong>
    <ul class="list-disc list-inside ml-4">
        <?php foreach ($errors as $field => $message): ?>
            <li><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form action="<?php echo url('thon-xom/store'); ?>" method="POST" class="bg-white p-6 rounded-lg shadow-md">
    <?php include '_form.php'; // Nhúng form dùng chung ?>
</form>