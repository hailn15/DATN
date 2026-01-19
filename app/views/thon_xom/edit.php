<?php
// app/views/thon_xom/edit.php
// Vars: $title, $thonXom, $currentController, $oldData, $errors
$displayData = $oldData ?? $thonXom; 
$isEdit = true; 

// Lấy flash message một lần
$flash = $this->getFlashMessage(); 
$successMessage = null;
$generalErrorMessageFromFlash = null;
if ($flash) {
    if ($flash['type'] === 'success') {
        $successMessage = $flash['message'];
    } elseif ($flash['type'] === 'error') {
        $generalErrorMessageFromFlash = $flash['message'];
    }
}
?>
<h1 class="text-2xl font-semibold text-gray-700 mb-6"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>

<?php if ($successMessage): ?>
    <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-400 rounded">
        <?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php endif; ?>

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

<form action="<?php echo url('thon-xom/update/' . $thonXom['id']); ?>" method="POST" class="bg-white p-6 rounded-lg shadow-md">
    <?php include '_form.php'; ?>
</form>