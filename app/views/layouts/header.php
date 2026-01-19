<?php
// File: app/views/layouts/header.php
$currentUser = getCurrentUser();
?>
<header class="bg-white shadow-sm print:hidden flex-shrink-0 border-b border-gray-200 z-10">
    <div class="px-2"> <!-- Chỉ còn padding ngang là px-2 -->
        <div class="flex justify-between h-16">
            <!-- Left side: Toggle Button and Page Title -->
            <div class="flex items-center">
                <!-- Sidebar Toggle Button -->
                <button 
                    x-data 
                    @click="$dispatch('toggle-sidebar')" 
                    type="button" 
                    class="p-2 mr-2 text-gray-500 rounded-md hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 lg:mr-4"
                    aria-label="Toggle sidebar"
                >
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <h1 class="text-lg font-semibold text-gray-700 hidden sm:block">
                    <?php echo isset($title) ? htmlspecialchars($title, ENT_QUOTES, 'UTF-8') : (defined('APP_NAME') ? APP_NAME : 'Dashboard'); ?>
                </h1>
            </div>

            <!-- Right side: User menu / Login -->
            <div class="flex items-center">
                <?php if ($currentUser): ?>
                    <span class="text-sm text-gray-600 mr-4 hidden sm:inline">
                        Chào, <span class="font-medium"><?php echo htmlspecialchars($currentUser['fullname'] ?? 'Bạn', ENT_QUOTES, 'UTF-8'); ?></span>!
                    </span>
                    <a href="<?php echo url('auth/logout'); ?>" class="text-sm text-red-600 hover:text-red-800 hover:bg-red-50 px-3 py-1.5 rounded-md font-medium transition-colors duration-150">
                        <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Đăng xuất
                    </a>
                <?php else: ?>
                     <a href="<?php echo url('auth/login'); ?>" class="text-sm text-blue-600 hover:text-blue-800 hover:bg-blue-50 px-3 py-1.5 rounded-md font-medium transition-colors duration-150">
                         <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        Đăng nhập
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>