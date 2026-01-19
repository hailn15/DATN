<?php
// app/views/auth/login.php
// Biến $title được truyền từ AuthController
$pageTitle = isset($title) ? htmlspecialchars($title, ENT_QUOTES, 'UTF-8') : 'Đăng nhập Hệ thống';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Hệ thống Quản lý Bảo trợ Xã hội</title>
    <!-- Bạn vẫn cần file app.css này cho các class cơ bản như form-label, btn, v.v... -->
    <link rel="stylesheet" href="<?php echo asset('css/app.css'); ?>">
    <style>
        /* --- ĐÃ CHUYỂN SANG CSS THÔNG THƯỜNG --- */

        body.login-page {
            /* Fallback color, shows while image is loading */
            background-color: #f1f5f9; /* tương đương bg-slate-100 */
            
            /* Background image properties */
            background-image: url("<?php echo asset('images/login_bg.png'); ?>");
            background-size: 100% 100%; /* Stretch image to cover width and height */
            height: 100vh; /* Full height of the viewport */
            background-position: center;
            background-repeat: no-repeat;
        }
        
        .login-page {
            min-height: 100vh;
            align-items: center; /* Căn giữa các mục theo chiều dọc */
            padding-top: 3rem;    /* py-12 */
            padding-bottom: 3rem; /* py-12 */
            padding-left: 1rem;   /* px-4 */
            padding-right: 1rem;  /* px-4 */
            padding-left: 850px
        }

        @media (min-width: 640px) {
            .login-card {
                padding: 2rem; /* sm:p-8 */
            }
        }

        .login-header-icon {
            margin-left: auto;
            margin-right: auto;
            height: 3rem; /* h-12 */
            width: auto;
            color: #0284c7; /* text-sky-600 */
        }

        .login-subtitle {
            margin-top: 0.5rem; /* mt-2 */
            text-align: center;
            font-size: 0.875rem; /* text-sm */
            color: #475569; /* text-slate-600 */
        }

        /* Các class này để đảm bảo các thành phần trong form kế thừa đúng style */
        .login-card .form-input {
             width: 100%;
        }
        
        .login-card .btn-primary {
            width: 100%;
            background-color: #0284c7; /* bg-sky-600 */
        }

        .login-card .btn-primary:hover {
            background-color: #0369a1; /* hover:bg-sky-700 */
        }

    </style>
</head>
<body class="login-page">
    <div class="login-card">
        <div>
            <svg class="login-header-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
            </svg>
            <!-- Sửa lại kích thước chữ và khoảng cách của tiêu đề phụ -->
            <p class="login-subtitle" style="font-size: 1.125rem; margin-top: 1rem;">
                BAN VĂN HÓA - XÃ HỘI
            </p>
        </div>

        <form action="<?php echo url('auth/processLogin'); ?>" method="POST" style="margin-top: 1.5rem; --space-y: 1rem;">
            <div style="margin-top: var(--space-y);">
                <label for="username" class="form-label">Tên đăng nhập hoặc Email:</label>
                <input type="text" id="username" name="username" class="form-input mt-1" required autofocus
                        placeholder="nhap.ten"
                        value="">
            </div>

            <div style="margin-top: var(--space-y);">
                <label for="password" class="form-label">Mật khẩu:</label>
                <input type="password" id="password" name="password" class="form-input mt-1" required
                        placeholder="••••••••">
            </div>

            <div style="margin-top: var(--space-y);">
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2 -ml-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Đăng nhập
                </button>
            </div>
        </form>

        <div class="text-center text-sm text-slate-500" style="margin-top: 1.5rem;">
            © <?php echo date('Y'); ?> UBND XÃ KIM ĐƯỜNG
        </div>
    </div>
</body>
</html>