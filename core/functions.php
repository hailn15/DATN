<?php
/**
 * Tạo URL phù hợp cho ứng dụng (kể cả khi chạy với php -S)
 * Sẽ tạo ra dạng /index.php?url=controller/action¶m1=value1
 *
 * @param string $path Đường dẫn mong muốn (ví dụ: 'doi-tuong/create', 'auth/login', 'ho-so-tro-cap/index?doi_tuong_id=1')
 * @return string URL hoàn chỉnh
 */
function url(string $path = ''): string {
    $base = '/index.php'; // Hoặc $_SERVER['SCRIPT_NAME'] nếu bạn muốn linh hoạt hơn
                         // với tên file index.php, nhưng '/index.php' thường ổn định.
    $mainPathPortion = '';
    $queryStringPortion = '';

    // Tách path chính và query string nếu có trong $path đầu vào
    if (strpos($path, '?') !== false) {
        list($mainPathPortion, $queryStringPortion) = explode('?', $path, 2);
    } else {
        $mainPathPortion = $path;
    }

    $mainPathPortion = ltrim($mainPathPortion, '/'); // Loại bỏ dấu / ở đầu nếu có

    $generatedUrl = '';

    // Xử lý path chính
    if (empty($mainPathPortion) || $mainPathPortion === '/') {
        // Trường hợp path rỗng hoặc chỉ là '/', trỏ về controller/action mặc định
        $defaultController = defined('DEFAULT_CONTROLLER') ? strtolower(str_replace('Controller', '', DEFAULT_CONTROLLER)) : 'home';
        $defaultAction = defined('DEFAULT_ACTION') ? DEFAULT_ACTION : 'index';

        if (empty($defaultController) || empty($defaultAction)) {
            // Nếu không có controller/action mặc định hợp lệ, chỉ trả về base
            $generatedUrl = $base;
        } else {
            $generatedUrl = $base . '?url=' . $defaultController . '/' . $defaultAction;
        }
    } else {
        // Trường hợp có path cụ thể
        $generatedUrl = $base . '?url=' . $mainPathPortion;
    }

    // Nối lại query string đã tách ra từ $path ban đầu (nếu có)
    if (!empty($queryStringPortion)) {
        // Đảm bảo URL đã có '?' để nối thêm bằng '&', nếu chưa thì dùng '?'
        // (Trường hợp này, do cách chúng ta xây $generatedUrl ở trên, nó luôn có '?' nếu không phải là base path rỗng)
        // Tuy nhiên, để an toàn, kiểm tra vẫn tốt.
        // $generatedUrl sẽ luôn có '?' nếu $mainPathPortion không rỗng hoặc default controller/action được set.
        // Chỉ trường hợp $base (vd: /index.php) không có '?'
        if (strpos($generatedUrl, '?') === false) {
             $generatedUrl .= '?' . $queryStringPortion;
        } else {
             $generatedUrl .= '&' . $queryStringPortion;
        }
    }

    return $generatedUrl;
}

/**
 * Tạo URL cho các tài nguyên tĩnh (CSS, JS, Images)
 * Sẽ tạo ra dạng /css/style.css?v=timestamp
 *
 * @param string $assetPath Đường dẫn tài nguyên từ thư mục public (ví dụ: 'css/style.css')
 * @return string URL hoàn chỉnh
 */
function asset(string $assetPath): string {
    $assetPath = ltrim($assetPath, '/');
    // Giả sử thư mục public của bạn nằm cùng cấp với core, app
    // Nếu cấu trúc khác, điều chỉnh đường dẫn này
    // __DIR__ trong trường hợp này sẽ là thư mục chứa file functions.php
    // Giả sử functions.php nằm trong thư mục gốc hoặc thư mục core.
    // Nếu functions.php nằm trong /app/helpers/ thì __DIR__ . '/../../public/'
    // Cần điều chỉnh $publicRoot cho đúng.
    // Ví dụ: nếu functions.php nằm ở thư mục gốc của dự án (cùng cấp với public, app, core)
    // $publicRoot = __DIR__ . '/public/';
    // Nếu functions.php nằm trong /core/
    $publicRoot = dirname(__DIR__) . '/public/'; // Giả định functions.php nằm trong thư mục con của thư mục gốc (ví dụ: /core)


    $filePath = rtrim($publicRoot, '/') . '/' . $assetPath;
    $version = '';

    if (file_exists($filePath)) {
      $version = '?v=' . filemtime($filePath);
    } else {
      // Để tránh lỗi nếu file không tồn tại, có thể không thêm version hoặc log lỗi
      // error_log("Asset file not found: " . $filePath);
      $version = '?v=' . time(); // Hoặc để trống $version = '';
    }
    // Đường dẫn trả về nên bắt đầu từ gốc web, không phải đường dẫn file hệ thống
    return '/' . $assetPath . $version;
}

/**
 * Lấy thông tin người dùng đang đăng nhập từ Session
 * @return array|null Trả về mảng thông tin user hoặc null nếu chưa đăng nhập
 */
if (!function_exists('getCurrentUser')) {
    function getCurrentUser() {
        if (session_status() == PHP_SESSION_NONE) { // Đảm bảo session đã được start
            session_start();
        }
        if (isset($_SESSION['user_id'])) { // Kiểm tra user_id
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'] ?? null,
                'fullname' => $_SESSION['fullname'] ?? null,
                'role' => $_SESSION['role'] ?? null
                // Thêm các thông tin khác bạn lưu trong session
            ];
        }
        return null;
    }
}
?>