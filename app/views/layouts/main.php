<?php
// app/views/layouts/main.php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . ' - ' . (defined('APP_NAME') ? APP_NAME : 'MyApp') : (defined('APP_NAME') ? APP_NAME : 'MyApp'); ?></title>

    <!-- Tailwind CSS qua CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style type="text/tailwindcss">
        /* Style cơ bản */
        html, body { @apply h-full overflow-hidden; } /* Ngăn cuộn toàn trang */
        body { @apply font-sans antialiased text-gray-800 bg-gray-100 flex; } /* Thêm flex cho body */

        /* Alert styles */
        .alert { @apply p-4 mb-4 border rounded text-sm relative; }
        .alert-success { @apply bg-green-100 border-green-400 text-green-700; }
        .alert-error { @apply bg-red-100 border-red-400 text-red-700; }
        .alert-warning { @apply bg-yellow-100 border-yellow-400 text-yellow-700; }
        .alert-info { @apply bg-blue-100 border-blue-400 text-blue-700; }
        .alert-close-btn { @apply absolute top-0 right-0 mt-2 mr-3 text-xl font-semibold leading-none text-inherit opacity-75 hover:opacity-100 cursor-pointer; }

        /* Button styles */
        .btn { @apply inline-flex items-center justify-center font-medium py-2 px-4 rounded-md shadow-sm transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed; } /* rounded-md for general buttons */
        .btn-primary { @apply bg-blue-600 hover:bg-blue-700 text-white focus:ring-blue-500; }
        .btn-secondary { @apply bg-gray-600 hover:bg-gray-700 text-white focus:ring-gray-500; }
        .btn-danger { @apply bg-red-600 hover:bg-red-700 text-white focus:ring-red-500; }
        .btn-success { @apply bg-green-600 hover:bg-green-700 text-white focus:ring-green-500; }
        .btn-warning { @apply bg-yellow-500 hover:bg-yellow-600 text-white focus:ring-yellow-400; }
        .btn-link { @apply text-blue-600 hover:text-blue-800 hover:underline focus:outline-none focus:ring-2 focus:ring-blue-300 font-medium py-1 px-1; }
        .btn-sm { @apply py-1 px-3 text-sm; }

        /* Form styles */
        .form-input, .form-select, .form-textarea { @apply block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm disabled:bg-gray-100; }
        .form-label { @apply block text-sm font-medium text-gray-700 mb-1; }
        .form-check-input { @apply h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500; }
        .form-check-label { @apply ml-2 block text-sm text-gray-900; }

        /* Table styles */
        .table-container { @apply overflow-x-auto shadow border-b border-gray-200 rounded-lg; }
        .table { @apply min-w-full divide-y divide-gray-200; }
        .table thead th { @apply px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap; }
        .table tbody tr:nth-child(even) { @apply bg-gray-50; }
        .table tbody tr:hover { @apply bg-blue-50; }
        .table tbody td { @apply px-6 py-3 whitespace-nowrap text-sm text-gray-700; }
        .table tbody td a { @apply text-blue-600 hover:text-blue-800 font-medium; }
        .table-action-link { @apply inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 mr-1 mb-1; }
        .table-action-view { @apply text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-500; }
        .table-action-edit { @apply text-white bg-yellow-500 hover:bg-yellow-600 focus:ring-yellow-400; }
        .table-action-delete { @apply text-white bg-red-600 hover:bg-red-700 focus:ring-red-500; }
        .table-action-form { @apply inline-block; margin: 0; }

        /* Custom scrollbar (optional) */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #edf2f7; border-radius: 10px;}
        ::-webkit-scrollbar-thumb { background: #a0aec0; border-radius: 10px;}
        ::-webkit-scrollbar-thumb:hover { background: #718096; }

        /* Print styles */
        @media print {
            body { @apply block overflow-visible; } /* Cho phép in toàn bộ nội dung */
            .print\:hidden { @apply hidden; }
        }

        /* Sidebar specific styles for collapse/expand */
        .sidebar-collapsed { @apply w-20; } /* Chiều rộng khi thu gọn */
        .sidebar-expanded { @apply w-80; } /* Chiều rộng khi mở rộng */

        /* Ẩn text và điều chỉnh khi sidebar thu gọn */
        .sidebar-collapsed .sidebar-text,
        .sidebar-collapsed .sidebar-logo-text,
        .sidebar-collapsed .sidebar-user-info {
            @apply hidden;
        }
        .sidebar-collapsed .sidebar-menu-button svg.arrow-icon {
            @apply hidden; /* Ẩn mũi tên dropdown khi collapse */
        }
        .sidebar-collapsed .sidebar-menu-button > span > svg:first-child { /* Icon chính của menu item (button) */
            @apply mr-0; /* Bỏ margin phải của icon chính */
        }
        .sidebar-collapsed .sidebar-menu-item a > svg:first-child { /* Icon chính của menu item (link) */
            @apply mr-0; /* Bỏ margin của icon nếu nó là con trực tiếp của link */
        }
        .sidebar-collapsed .sidebar-menu-item a,
        .sidebar-collapsed .sidebar-menu-button {
            @apply justify-center px-2; /* Căn giữa icon và giảm padding ngang khi text ẩn */
        }


        .sidebar-collapsed ul[x-show] {
            @apply hidden !important; /* Luôn ẩn submenu khi sidebar collapsed */
        }

        /* Transition cho sidebar width */
        #desktop-sidebar { @apply transition-all duration-300 ease-in-out; }

    </style>
</head>
<body class="flex h-screen" x-data="{ sidebarOpen: JSON.parse(localStorage.getItem('sidebarOpen') || 'true') }" x-on:toggle-sidebar.window="sidebarOpen = !sidebarOpen; localStorage.setItem('sidebarOpen', JSON.stringify(sidebarOpen))">
    <!-- Sidebar -->
    <?php
        // Ensure $currentController is available for sidebar.php
        // It might be set by the router or a base controller.
        // If not, provide a default or ensure it's always set before this view.
        // For this example, assuming $currentController is passed to this main layout.
        $sidebarData = [];
        if (isset($currentController)) {
            $sidebarData['currentController'] = $currentController;
        }
        // A more robust way might be to extract $currentController in your bootstrap/front controller
        // and make it globally available or pass it specifically to views.

        $sidebarPath = __DIR__ . '/sidebar.php';
        if(file_exists($sidebarPath)) {
            // Pass variables to the included file if necessary (PHP's include scope)
            // $currentController should be accessible if set in the calling script of main.php
            include $sidebarPath;
        }
        else { echo '<!-- Sidebar file not found -->'; }
    ?>

    <!-- Main Content Area Wrapper -->
    <div class="flex flex-col flex-grow overflow-hidden"> <!-- Cho phép wrapper này chứa và quản lý cuộn của main -->
        <!-- Header (Cố định) -->
        <?php
            $headerPath = __DIR__ . '/header.php';
            if(file_exists($headerPath)) { include $headerPath; }
            else { echo '<!-- Header file not found -->'; }
        ?>

        <!-- Vùng nội dung chính (Cho phép cuộn) -->
        <main class="flex-grow overflow-y-auto p-3 md:p-4 bg-gray-100">
            <?php
            if (session_status() == PHP_SESSION_NONE) { // Start session if not already started
                session_start();
            }
            if (isset($_SESSION['flash_message'])):
                $flashMessage = $_SESSION['flash_message'];
                unset($_SESSION['flash_message']);
            ?>
                <div class="alert alert-<?php echo htmlspecialchars($flashMessage['type'], ENT_QUOTES, 'UTF-8'); ?> mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <?php echo nl2br(htmlspecialchars($flashMessage['message'], ENT_QUOTES, 'UTF-8')); ?>
                    <button type="button" class="alert-close-btn" onclick="this.parentElement.style.display='none';">×</button>
                </div>
            <?php endif; ?>

            <!-- Nơi nội dung của từng trang con được hiển thị -->
            <?php
            if (isset($content)) { echo $content; }
            else { echo '<div class="alert alert-error">Lỗi: Không tìm thấy nội dung trang.</div>'; }
            ?>
        </main>

        <!-- Footer (Cố định) -->
        <?php
            $footerPath = __DIR__ . '/footer.php';
            if(file_exists($footerPath)) { include $footerPath; }
            else { echo '<!-- Footer file not found -->'; }
        ?>
    </div> <!-- Hết Main Content Area Wrapper -->
</body>
</html>