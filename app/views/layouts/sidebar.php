<?php
// app/views/layouts/sidebar.php
$currentUser = getCurrentUser();

// New color scheme and styles for sidebar items
$activeClass = 'bg-indigo-600 text-white shadow-sm'; // Active menu item: Indigo background, white text
$inactiveClass = 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700'; // Inactive: Slate text, light indigo hover

// Icon colors
$activeIconClass = 'text-indigo-100'; // Slightly softer white for active icons if preferred, or just 'text-white'
$inactiveIconClass = 'text-slate-400 group-hover:text-indigo-500';

if (!function_exists('isControllerActiveForSidebar')) {
    function isControllerActiveForSidebar($controllerNameToCheck, $currentControllerNameFromView) {
        $expectedClassName = ucfirst($controllerNameToCheck) . 'Controller';
        return strtolower($expectedClassName) === strtolower($currentControllerNameFromView ?? '');
    }
}
?>
<!-- Sidebar Luôn hiển thị -->
<aside
    id="desktop-sidebar"
    class="bg-white text-slate-700 border-r border-slate-200 flex flex-col flex-shrink-0 print:hidden shadow-xl h-screen overflow-y-auto"
    :class="sidebarOpen ? 'sidebar-expanded' : 'sidebar-collapsed'"
    x-cloak
>
    <!-- Logo/Brand -->
    <div class="h-20 flex items-center justify-center flex-shrink-0 px-4 border-b border-slate-200" :class="sidebarOpen ? '' : 'px-2'">
         <a href="<?php echo url('home/index'); ?>" class="text-xl font-bold text-indigo-600 hover:opacity-80 transition-opacity flex items-center">
             <img src="/images/logo.png" 
                  alt="<?php echo defined('APP_NAME') ? htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') . ' Logo' : 'App Logo'; ?>" 
                  class="h-9 w-9 object-contain shrink-0" 
                  :class="sidebarOpen ? 'mr-2' : 'mx-auto'">
             <span class="sidebar-logo-text"><?php echo defined('APP_NAME') ? htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') : 'App Name'; ?></span>
         </a>
    </div>
    <!-- Navigation Links -->
    <nav class="flex-grow py-4 px-2 space-y-1.5"> <!-- Slightly more space between items -->
        <ul>
            <!-- Trang chủ -->
            <li class="sidebar-menu-item">
                <a href="<?php echo url('home/index'); ?>"
                   title="TRANG CHỦ"
                   class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-150 <?php echo isControllerActiveForSidebar('Home', $currentController ?? '') || empty($currentController) ? $activeClass : $inactiveClass; ?>">
                   <svg class="shrink-0 h-5 w-5 <?php echo isControllerActiveForSidebar('Home', $currentController ?? '') || empty($currentController) ? $activeIconClass : $inactiveIconClass; ?>" :class="sidebarOpen ? 'mr-3' : ''" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                   <span class="sidebar-text">TRANG CHỦ</span>
                </a>
            </li>

            <!-- QL Đối tượng BTXH -->
            <li x-data="{ open: <?php echo isControllerActiveForSidebar('DoiTuong', $currentController ?? '') ? 'true' : 'false'; ?> }"
                @toggle-sidebar.window="if (!sidebarOpen) open = false" class="sidebar-menu-item">
                <button @click="sidebarOpen ? (open = !open) : $dispatch('toggle-sidebar')"
                        title="QUẢN LÝ HỒ SƠ ĐỐI TƯỢNG"
                        class="sidebar-menu-button group w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg focus:outline-none transition-colors duration-150 <?php echo isControllerActiveForSidebar('DoiTuong', $currentController ?? '') ? $activeClass : $inactiveClass; ?>">
                   <span class="flex items-center">
                       <svg class="shrink-0 h-5 w-5 <?php echo isControllerActiveForSidebar('DoiTuong', $currentController ?? '') ? $activeIconClass : $inactiveIconClass; ?>" :class="sidebarOpen ? 'mr-3' : ''" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0110 13v-2.26a3.001 3.001 0 00-1.293-2.438l-1.756-1.054A4 4 0 006 5H4a4 4 0 00-3.78 5.28L2 14a6.97 6.97 0 00-1 1v1a1 1 0 001 1h10.93zM16 14a1 1 0 11-2 0 1 1 0 012 0z"></path></svg>
                       <span class="sidebar-text">QUẢN LÝ HỒ SƠ ĐỐI TƯỢNG</span>
                   </span>
                   <svg class="arrow-icon shrink-0 ml-auto h-5 w-5 transform transition-transform duration-150 <?php echo isControllerActiveForSidebar('DoiTuong', $currentController ?? '') ? (strpos($activeClass, 'text-white') !== false ? 'text-indigo-200' : 'text-slate-500') : 'text-slate-400 group-hover:text-slate-500'; ?>" :class="{'rotate-180': open && sidebarOpen, 'rotate-0': !open || !sidebarOpen}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
                <ul x-show="open && sidebarOpen" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="mt-1 pl-10 pr-2 space-y-1"> <!-- Adjusted padding for submenu -->
                    <li>
                        <a href="<?php echo url('doi-tuong/index'); ?>" class="block px-3 py-2 text-sm rounded-md 
                            <?php 
                            // SỬA Ở ĐÂY: Active khi là controller DoiTuong VÀ action KHÔNG PHẢI là 'create'
                            echo isControllerActiveForSidebar('DoiTuong', $currentController ?? '') && (!isset($action) || $action !== 'create') ? $activeClass : $inactiveClass; 
                            ?> 
                            hover:bg-indigo-100 transition-colors duration-150">
                            Danh sách Đối tượng
                        </a>
                    </li>
                    <li><a href="<?php echo url('doi-tuong/create'); ?>" class="block px-3 py-2 text-sm rounded-md <?php echo isControllerActiveForSidebar('DoiTuong', $currentController ?? '') && (isset($action) && $action == 'create') ? $activeClass : $inactiveClass; ?> hover:bg-indigo-100 transition-colors duration-150">Thêm mới Đối tượng</a></li>
                </ul>
            </li>

            <!-- QL Hồ sơ trợ cấp Hàng tháng -->
            <?php
                $isHoSoTroCapActive = isControllerActiveForSidebar('HoSoTroCap', $currentController ?? '');
                ?>
                <li class="sidebar-menu-item">
                    <a href="<?php echo url('ho-so-tro-cap/index'); ?>"
                    title="HỖ TRỢ THƯỜNG XUYÊN"
                    class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-150 <?php echo $isHoSoTroCapActive ? $activeClass : $inactiveClass; ?>">
                    <svg class="shrink-0 h-5 w-5 <?php echo $isHoSoTroCapActive ? $activeIconClass : $inactiveIconClass; ?>" :class="sidebarOpen ? 'mr-3' : ''" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>
                    <span class="sidebar-text">HỖ TRỢ THƯỜNG XUYÊN</span>
                    </a>
                </li>

            <!-- QL Hỗ trợ Khẩn cấp -->
            <?php
            $isHoTroKhanCapParentActive = isControllerActiveForSidebar('HoTroKhanCap', $currentController ?? '');
            ?>
            <li x-data="{ open: <?php echo $isHoTroKhanCapParentActive ? 'true' : 'false'; ?> }"
                 @toggle-sidebar.window="if (!sidebarOpen) open = false" class="sidebar-menu-item">
                <button @click="sidebarOpen ? (open = !open) : $dispatch('toggle-sidebar')"
                        title="QL Hỗ trợ Khẩn cấp"
                        class="sidebar-menu-button group w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg focus:outline-none transition-colors duration-150 <?php echo $isHoTroKhanCapParentActive ? $activeClass : $inactiveClass; ?>">
                   <span class="flex items-center">
                     <svg class="shrink-0 h-5 w-5 <?php echo $isHoTroKhanCapParentActive ? $activeIconClass : $inactiveIconClass; ?>" :class="sidebarOpen ? 'mr-3' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                       <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286zm0 13.036h.008v.008H12v-.008z" />
                     </svg>
                     <span class="sidebar-text">HỖ TRỢ KHẨN CẤP</span>
                   </span>
                   <svg class="arrow-icon shrink-0 ml-auto h-5 w-5 transform transition-transform duration-150 <?php echo $isHoTroKhanCapParentActive ? (strpos($activeClass, 'text-white') !== false ? 'text-indigo-200' : 'text-slate-500') : 'text-slate-400 group-hover:text-slate-500'; ?>" :class="{'rotate-180': open && sidebarOpen, 'rotate-0': !open || !sidebarOpen}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
                <ul x-show="open && sidebarOpen" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="mt-1 pl-10 pr-2 space-y-1">
                <li>
        <a href="<?php echo url('ho-tro-khan-cap/index'); ?>" class="block px-3 py-2 text-sm rounded-md 
            <?php 
            // SỬA Ở ĐÂY: Logic tương tự
            echo isControllerActiveForSidebar('HoTroKhanCap', $currentController ?? '') && (!isset($action) || $action !== 'create') ? $activeClass : $inactiveClass; 
            ?> 
            hover:bg-indigo-100 transition-colors duration-150">
            Danh sách hồ sơ
        </a>
    </li>
                    <li><a href="<?php echo url('ho-tro-khan-cap/create'); ?>" class="block px-3 py-2 text-sm rounded-md <?php echo isControllerActiveForSidebar('HoTroKhanCap', $currentController ?? '') && (isset($action) && $action == 'create') ? $activeClass : $inactiveClass; ?> hover:bg-indigo-100 transition-colors duration-150">Tạo mới hồ sơ</a></li>
                </ul>
            </li>
             <!-- QL Hồ sơ Chăm sóc CĐ -->
             <?php
                $isHoSoChamSocParentActive = isControllerActiveForSidebar('HoSoChamSocCongDong', $currentController ?? '') || isControllerActiveForSidebar('NguoiChamSoc', $currentController ?? '');
                ?>
             <li x-data="{ open: <?php echo $isHoSoChamSocParentActive ? 'true' : 'false'; ?> }"
                 @toggle-sidebar.window="if (!sidebarOpen) open = false" class="sidebar-menu-item">
                <button @click="sidebarOpen ? (open = !open) : $dispatch('toggle-sidebar')"
                        title="CHĂM SÓC TẠI CỘNG ĐỒNG"
                        class="sidebar-menu-button group w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg focus:outline-none transition-colors duration-150 <?php echo $isHoSoChamSocParentActive ? $activeClass : $inactiveClass; ?>">
                   <span class="flex items-center">
                     <svg class="shrink-0 h-5 w-5 <?php echo $isHoSoChamSocParentActive ? $activeIconClass : $inactiveIconClass; ?>" :class="sidebarOpen ? 'mr-3' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                       <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1M14.25 3l1.5.545m0 6.205l-3 1m-6.75 0l-3 1m0 0l-1.5.545M3.75 3l1.5.545m0 6.205l-1.5.545" />
                     </svg>
                     <span class="sidebar-text">CHĂM SÓC TẠI CỘNG ĐỒNG</span>
                   </span>
                   <svg class="arrow-icon shrink-0 ml-auto h-5 w-5 transform transition-transform duration-150 <?php echo $isHoSoChamSocParentActive ? (strpos($activeClass, 'text-white') !== false ? 'text-indigo-200' : 'text-slate-500') : 'text-slate-400 group-hover:text-slate-500'; ?>" :class="{'rotate-180': open && sidebarOpen, 'rotate-0': !open || !sidebarOpen}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
                <ul x-show="open && sidebarOpen" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="mt-1 pl-10 pr-2 space-y-1">
                    <li><a href="<?php echo url('ho-so-cham-soc-cong-dong/index'); ?>" class="block px-3 py-2 text-sm rounded-md <?php echo isControllerActiveForSidebar('HoSoChamSocCongDong', $currentController ?? '') ? $activeClass : $inactiveClass; ?> hover:bg-indigo-100 transition-colors duration-150">Danh sách Hồ sơ</a></li>
                    <li>
                        <a href="<?php echo url('nguoi-cham-soc/index'); ?>" class="block px-3 py-2 text-sm rounded-md <?php echo isControllerActiveForSidebar('NguoiChamSoc', $currentController ?? '') ? $activeClass : $inactiveClass; ?> hover:bg-indigo-100 transition-colors duration-150">Người Chăm Sóc</a>
                    </li>
                </ul>
            </li>

            <!-- Quản lý Chính sách -->
            <?php
            $isQuanLyChinhSachParentActive = ($currentUser && $currentUser['role'] == 'admin' &&
                                             (isControllerActiveForSidebar('MucTroCapHangThang', $currentController ?? '') ||
                                              isControllerActiveForSidebar('LoaiHinhHoTroKhanCap', $currentController ?? '') ||
                                              isControllerActiveForSidebar('LoaiHinhChamSocCD', $currentController ?? '')));
            ?>
            
            <li x-data="{ open: <?php echo $isQuanLyChinhSachParentActive ? 'true' : 'false'; ?> }"
                @toggle-sidebar.window="if (!sidebarOpen) open = false" class="sidebar-menu-item">
                <button @click="sidebarOpen ? (open = !open) : $dispatch('toggle-sidebar')"
                        title="QUẢN LÝ CHÍNH SÁCH"
                        class="sidebar-menu-button group w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg focus:outline-none transition-colors duration-150 <?php echo $isQuanLyChinhSachParentActive ? $activeClass : $inactiveClass; ?>">
                   <span class="flex items-center">
                     <svg class="shrink-0 h-5 w-5 <?php echo $isQuanLyChinhSachParentActive ? $activeIconClass : $inactiveIconClass; ?>" :class="sidebarOpen ? 'mr-3' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                         <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h12A2.25 2.25 0 0020.25 14.25V3M3.75 20.25v-4.5A2.25 2.25 0 016 13.5h12a2.25 2.25 0 012.25 2.25v4.5M3.75 12H21m-8.625-9.75h5.625c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125h-5.625a1.125 1.125 0 01-1.125-1.125v-1.5c0-.621.504-1.125 1.125-1.125z" />
                     </svg>
                     <span class="sidebar-text">QUẢN LÝ CHÍNH SÁCH</span>
                   </span>
                   <svg class="arrow-icon shrink-0 ml-auto h-5 w-5 transform transition-transform duration-150 <?php echo $isQuanLyChinhSachParentActive ? (strpos($activeClass, 'text-white') !== false ? 'text-indigo-200' : 'text-slate-500') : 'text-slate-400 group-hover:text-slate-500'; ?>" :class="{'rotate-180': open && sidebarOpen, 'rotate-0': !open || !sidebarOpen}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
                <ul x-show="open && sidebarOpen" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="mt-1 pl-10 pr-2 space-y-1">
                    <li><a href="<?php echo url('muc-tro-cap-hang-thang/index'); ?>" title="QL Mức Trợ Cấp HT" class="block px-3 py-2 text-sm rounded-md <?php echo isControllerActiveForSidebar('MucTroCapHangThang', $currentController ?? '') ? $activeClass : $inactiveClass; ?> hover:bg-indigo-100 transition-colors duration-150">Mức trợ cấp hàng tháng</a></li>
                    <li><a href="<?php echo url('loai-hinh-ho-tro-khan-cap/index'); ?>" title="QL Loại Hình Hỗ Trợ KC" class="block px-3 py-2 text-sm rounded-md <?php echo isControllerActiveForSidebar('LoaiHinhHoTroKhanCap', $currentController ?? '') ? $activeClass : $inactiveClass; ?> hover:bg-indigo-100 transition-colors duration-150">Loại hỗ trợ khẩn cấp</a></li>
                    <li><a href="<?php echo url('loai-hinh-cham-soc-cd/index'); ?>" title="QL Loại Hình Chăm Sóc CĐ" class="block px-3 py-2 text-sm rounded-md <?php echo isControllerActiveForSidebar('LoaiHinhChamSocCD', $currentController ?? '') ? $activeClass : $inactiveClass; ?> hover:bg-indigo-100 transition-colors duration-150">Loại hình chăm sóc tại cộng đồng</a></li>
                </ul>
            </li>
            

            <!-- QL Thôn/Xóm -->
            <li class="sidebar-menu-item">
                <a href="<?php echo url('thon-xom/index'); // Hoặc url('ThonXom/index') tùy cấu hình router của bạn ?>"
                   title="QUẢN LÝ ĐỊA PHƯƠNG"
                   class="group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-150 <?php echo isControllerActiveForSidebar('ThonXom', $currentController ?? '') ? $activeClass : $inactiveClass; ?>">
                   <svg class="shrink-0 h-5 w-5 <?php echo isControllerActiveForSidebar('ThonXom', $currentController ?? '') ? $activeIconClass : $inactiveIconClass; ?>" :class="sidebarOpen ? 'mr-3' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                       <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.125-.504 1.125-1.125V14.25m-17.25 4.5h10.5m.375-11.25V5.625c0-1.036.84-1.875 1.875-1.875h1.5c1.036 0 1.875.84 1.875 1.875v7.5m-9.75-7.5h1.5M9 7.5h1.5m-1.5 3h1.5m-7.5 4.5v-3.75c0-.621.504-1.125 1.125-1.125h9.75c.621 0 1.125.504 1.125 1.125V18.75m-9.75 0h-1.5M14.25 9.75h-1.5M14.25 12.75h-1.5" />
                   </svg>
                   <span class="sidebar-text">QUẢN LÝ ĐỊA PHƯƠNG</span>
                </a>
            </li>

             <!-- <?php if ($currentUser && isset($currentUser['role']) && $currentUser['role'] == 'admin'): ?>
                 <li x-data="{ open: <?php echo isControllerActiveForSidebar('User', $currentController ?? '') ? 'true' : 'false'; ?> }"
                    @toggle-sidebar.window="if (!sidebarOpen) open = false" class="sidebar-menu-item">
                     <button @click="sidebarOpen ? (open = !open) : $dispatch('toggle-sidebar')"
                             title="QL Người dùng"
                             class="sidebar-menu-button group w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg focus:outline-none transition-colors duration-150 <?php echo isControllerActiveForSidebar('User', $currentController ?? '') ? $activeClass : $inactiveClass; ?>">
                       <span class="flex items-center">
                         <svg class="shrink-0 h-5 w-5 <?php echo isControllerActiveForSidebar('User', $currentController ?? '') ? $activeIconClass : $inactiveIconClass; ?>" :class="sidebarOpen ? 'mr-3' : ''" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0012 11z" clip-rule="evenodd"></path></svg>
                         <span class="sidebar-text">Người dùng</span>
                       </span>
                       <svg class="arrow-icon shrink-0 ml-auto h-5 w-5 transform transition-transform duration-150 <?php echo isControllerActiveForSidebar('User', $currentController ?? '') ? (strpos($activeClass, 'text-white') !== false ? 'text-indigo-200' : 'text-slate-500') : 'text-slate-400 group-hover:text-slate-500'; ?>" :class="{'rotate-180': open && sidebarOpen, 'rotate-0': !open || !sidebarOpen}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                     </button>
                     <ul x-show="open && sidebarOpen" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="mt-1 pl-10 pr-2 space-y-1">
                         <li><a href="<?php echo url('user/index'); ?>" class="block px-3 py-2 text-sm rounded-md <?php echo $inactiveClass; ?> hover:bg-indigo-100 transition-colors duration-150">Danh sách</a></li>
                         <li><a href="<?php echo url('user/create'); ?>" class="block px-3 py-2 text-sm rounded-md <?php echo $inactiveClass; ?> hover:bg-indigo-100 transition-colors duration-150">Thêm mới</a></li>
                     </ul>
                 </li>
             <?php endif; ?> -->


             <?php if ($currentUser && isset($currentUser['role']) && $currentUser['role'] == 'admin'): ?>
                 <li x-data="{ open: <?php echo isControllerActiveForSidebar('User', $currentController ?? '') ? 'true' : 'false'; ?> }"
                    @toggle-sidebar.window="if (!sidebarOpen) open = false" class="sidebar-menu-item">
                     <button @click="sidebarOpen ? (open = !open) : $dispatch('toggle-sidebar')"
                             title="QUẢN LÝ NGƯỜI DÙNG"
                             class="sidebar-menu-button group w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg focus:outline-none transition-colors duration-150 <?php echo isControllerActiveForSidebar('User', $currentController ?? '') ? $activeClass : $inactiveClass; ?>">
                       <span class="flex items-center">
                         <svg class="shrink-0 h-5 w-5 <?php echo isControllerActiveForSidebar('User', $currentController ?? '') ? $activeIconClass : $inactiveIconClass; ?>" :class="sidebarOpen ? 'mr-3' : ''" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0012 11z" clip-rule="evenodd"></path></svg>
                         <span class="sidebar-text">QUẢN LÝ NGƯỜI DÙNG</span>
                       </span>
                       <svg class="arrow-icon shrink-0 ml-auto h-5 w-5 transform transition-transform duration-150 <?php echo isControllerActiveForSidebar('User', $currentController ?? '') ? (strpos($activeClass, 'text-white') !== false ? 'text-indigo-200' : 'text-slate-500') : 'text-slate-400 group-hover:text-slate-500'; ?>" :class="{'rotate-180': open && sidebarOpen, 'rotate-0': !open || !sidebarOpen}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                     </button>
                     <ul x-show="open && sidebarOpen" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="mt-1 pl-10 pr-2 space-y-1">
                         <li><a href="<?php echo url('user/index'); ?>" class="block px-3 py-2 text-sm rounded-md <?php echo isControllerActiveForSidebar('User', $currentController ?? '') && (isset($action) && $action == 'index') ? $activeClass : $inactiveClass; ?> hover:bg-indigo-100 transition-colors duration-150">Danh sách</a></li>
                         <li><a href="<?php echo url('user/create'); ?>" class="block px-3 py-2 text-sm rounded-md <?php echo isControllerActiveForSidebar('User', $currentController ?? '') && (isset($action) && $action == 'create') ? $activeClass : $inactiveClass; ?> hover:bg-indigo-100 transition-colors duration-150">Thêm mới</a></li>
                     </ul>
                 </li>
             <?php endif; ?>


            <!-- Tiện ích -->
            <?php
            $isVanBanChinhSachActiveForUtility = (isset($action) && $action === 'vanBanChinhSach' && isControllerActiveForSidebar('Utility', $currentController ?? ''));
            $isBieuMauActiveForUtility = (isset($action) && $action === 'bieuMau' && isControllerActiveForSidebar('Utility', $currentController ?? ''));
            $isHuongDanActiveForUtility = (isset($action) && $action === 'huongDan' && isControllerActiveForSidebar('Utility', $currentController ?? ''));
            
            $isTienIchParentActive = $isVanBanChinhSachActiveForUtility || $isBieuMauActiveForUtility || $isHuongDanActiveForUtility;
            ?>
            <li class="sidebar-menu-item">
                <details class="group" <?php echo $isTienIchParentActive ? 'open' : ''; ?>>
                    <summary class="flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg cursor-pointer list-none <?php echo $isTienIchParentActive ? $activeClass : $inactiveClass; ?>">
                       <span class="flex items-center">
                         <svg class="shrink-0 h-5 w-5 <?php echo $isTienIchParentActive ? $activeIconClass : $inactiveIconClass; ?>" :class="sidebarOpen ? 'mr-3' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.646.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.333.183-.582.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                           <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                         </svg>
                         <span class="sidebar-text">TIỆN ÍCH</span>
                       </span>
                       <svg class="arrow-icon shrink-0 ml-auto h-5 w-5 transform transition-transform duration-150 group-open:rotate-180 <?php echo $isTienIchParentActive ? (strpos($activeClass, 'text-white') !== false ? 'text-indigo-200' : 'text-slate-500') : 'text-slate-400 group-hover:text-slate-500'; ?>" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    </summary>
                    <ul class="mt-1 pl-10 pr-2 space-y-1" :class="sidebarOpen ? '' : 'hidden'">
                        <li><a href="<?php echo url('utility/vanBanChinhSach'); ?>" title="Văn bản Chính sách" class="block px-3 py-2 text-sm rounded-md <?php echo $isVanBanChinhSachActiveForUtility ? $activeClass.' text-white bg-opacity-75' : $inactiveClass; ?> hover:bg-indigo-100 transition-colors duration-150">Văn bản Chính sách</a></li>
                        <li><a href="<?php echo url('utility/bieuMau'); ?>" class="block px-3 py-2 text-sm rounded-md <?php echo $isBieuMauActiveForUtility ? $activeClass.' text-white bg-opacity-75' : $inactiveClass; ?> hover:bg-indigo-100 transition-colors duration-150">Biểu mẫu </a></li>
                        <li><a href="<?php echo url('utility/huongDan'); ?>" class="block px-3 py-2 text-sm rounded-md <?php echo $isHuongDanActiveForUtility ? $activeClass.' text-white bg-opacity-75' : $inactiveClass; ?> hover:bg-indigo-100 transition-colors duration-150">Hướng dẫn Sử dụng</a></li>
                    </ul>
                </details>
            </li>
        </ul>
    </nav>

     <!-- User Info / Logout -->
     <div class="flex-shrink-0 p-4 border-t border-slate-200" :class="sidebarOpen ? '' : 'p-2'">
         <?php if ($currentUser): ?>
             <div class="flex items-center group" :class="sidebarOpen ? '' : 'justify-center'">
                  <a href="#" title="<?php echo htmlspecialchars($currentUser['fullname'] ?? 'Người dùng', ENT_QUOTES, 'UTF-8'); ?>"
                     class="shrink-0 inline-block h-10 w-10 rounded-full overflow-hidden bg-slate-200"
                     :class="sidebarOpen ? 'mr-3' : ''">
                      <svg class="h-full w-full text-slate-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                      </svg>
                 </a>
                 <div class="sidebar-user-info flex-grow" :class="sidebarOpen ? '' : 'hidden'">
                     <p class="text-sm font-semibold text-slate-800 truncate"><?php echo htmlspecialchars($currentUser['fullname'] ?? 'Người dùng', ENT_QUOTES, 'UTF-8'); ?></p>
                     <a href="<?php echo url('auth/logout'); ?>" class="text-xs text-red-500 hover:text-red-600 hover:underline">Đăng xuất</a>
                 </div>
             </div>
         <?php else: ?>
             <a href="<?php echo url('auth/login'); ?>"
                title="Đăng nhập"
                class="block w-full text-center px-4 py-3 text-sm font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors duration-150">
                 <svg class="shrink-0 h-5 w-5" :class="sidebarOpen ? 'hidden' : 'mx-auto'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                </svg>
                 <span class="sidebar-text">ĐĂNG NHẬP</span>
             </a>
         <?php endif; ?>
     </div>
</aside>