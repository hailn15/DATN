<?php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . ' - ' . (defined('APP_NAME') ? APP_NAME : 'MyApp') : (defined('APP_NAME') ? APP_NAME : 'MyApp'); ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>
      tailwind.config = { /* Có thể để trống hoặc cấu hình tối giản */ }
    </script>
    <style type="text/tailwindcss">
        /* Style cơ bản cho trang auth */
        body { @apply font-sans antialiased text-gray-800 bg-gray-100 flex items-center justify-center min-h-screen; }
        .auth-card { @apply bg-white p-8 rounded-lg shadow-xl w-full max-w-md; }
        .auth-title { @apply text-2xl font-bold text-center text-gray-700 mb-6; }

        /* Alert styles (cần cho flash messages) */
        .alert { @apply p-4 mb-4 border rounded text-sm relative; }
        .alert-success { @apply bg-green-100 border-green-400 text-green-700; }
        .alert-error { @apply bg-red-100 border-red-400 text-red-700; }
        .alert-warning { @apply bg-yellow-100 border-yellow-400 text-yellow-700; }
        .alert-info { @apply bg-blue-100 border-blue-400 text-blue-700; }
        .alert-close-btn { @apply absolute top-0 right-0 mt-2 mr-3 text-xl font-semibold leading-none text-inherit opacity-75 hover:opacity-100 cursor-pointer; }

        /* Button styles (cần cho nút submit) */
        .btn { @apply inline-flex items-center justify-center font-medium py-2 px-4 rounded shadow-sm transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed; }
        .btn-primary { @apply bg-blue-600 hover:bg-blue-700 text-white focus:ring-blue-500 w-full; } /* w-full cho nút login */

        /* Form styles (cần cho các input) */
        .form-input, .form-select, .form-textarea { @apply block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm disabled:bg-gray-100; }
        .form-label { @apply block text-sm font-medium text-gray-700 mb-1; }
    </style>
</head>
<body>
    <div class="auth-card">
        <?php
        if (session_status() == PHP_SESSION_ACTIVE && isset($_SESSION['flash_message'])):
            $flashMessage = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
        ?>
            <div class="alert alert-<?php echo htmlspecialchars($flashMessage['type'], ENT_QUOTES, 'UTF-8'); ?>" role="alert">
                <?php echo nl2br(htmlspecialchars($flashMessage['message'], ENT_QUOTES, 'UTF-8')); ?>
                <button type="button" class="alert-close-btn" onclick="this.parentElement.style.display='none';">×</button>
            </div>
        <?php endif; ?>

        <?php
        if (isset($content)) { echo $content; }
        else { echo '<div class="alert alert-error">Lỗi: Không tìm thấy nội dung trang.</div>'; }
        ?>
    </div>
</body>
</html>