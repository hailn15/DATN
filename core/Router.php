<?php
// core/Router.php

class Router {
    protected $controller = DEFAULT_CONTROLLER; // Ví dụ: 'HomeController'
    protected $action = DEFAULT_ACTION;       // Ví dụ: 'index'
    protected $params = [];

    public function __construct() {
        $this->parseUrl();
    }

    protected function parseUrl() {
        $url = '';
        if (isset($_GET['url'])) {
            $url = filter_var(trim($_GET['url'], '/'), FILTER_SANITIZE_URL);
        }

        $urlParts = $url ? explode('/', $url) : [];

        // --- 1. Xác định Controller (ĐÃ SỬA) ---
        if (!empty($urlParts[0])) {
            // Chuyển đổi 'kebab-case' (vd: doi-tuong) hoặc 'snake_case'
            // thành 'PascalCase' (vd: DoiTuong)
            // Bước 1: Viết hoa ký tự đầu mỗi phần ngăn cách bởi '-' hoặc '_'
            // Ví dụ: 'doi-tuong' -> 'Doi-Tuong'
            $pascalCasePart = ucwords($urlParts[0], '-_');
            // Bước 2: Loại bỏ các dấu '-' và '_'
            // Ví dụ: 'Doi-Tuong' -> 'DoiTuong'
            $controllerNamePart = str_replace(['-', '_'], '', $pascalCasePart);

            // Tạo tên class Controller đầy đủ (vd: DoiTuongController)
            $potentialControllerName = $controllerNamePart . 'Controller';

            // Tạo đường dẫn đến file controller dự kiến
            $controllerFilePath = '../app/controllers/' . $potentialControllerName . '.php';

            // Kiểm tra file tồn tại (vd: '../app/controllers/DoiTuongController.php')
            if (file_exists($controllerFilePath)) {
                $this->controller = $potentialControllerName; // Đặt controller đúng
                unset($urlParts[0]);
            } else {
                // Controller không tìm thấy, sẽ dùng default. Ghi log nếu cần.
                // error_log("Router Warning: Controller file not found for '{$urlParts[0]}'. Path checked: {$controllerFilePath}. Using default controller '{$this->controller}'.");
            }
        }
        // Controller mặc định sẽ được sử dụng nếu không tìm thấy

        // --- 2. Xác định Action (Method) ---
        if (isset($urlParts[1])) {
            $potentialAction = $urlParts[1];
            // Bạn có thể thêm chuyển đổi kebab-case sang camelCase cho action ở đây nếu muốn
            // $potentialAction = lcfirst(str_replace('-', '', ucwords($potentialAction, '-')));

            $this->action = $potentialAction;
            unset($urlParts[1]);
        }
        // Action mặc định sẽ được sử dụng nếu không tìm thấy

        // --- 3. Lấy các tham số còn lại ---
        $this->params = $urlParts ? array_values($urlParts) : [];
    }

    // --- dispatch() và các hàm khác giữ nguyên như phiên bản trước ---
    public function dispatch() {
         // Kiểm tra class controller tồn tại
         if (!class_exists($this->controller)) {
            // Thử load file thủ công nếu autoloader lỗi hoặc không có
            $controllerFilePath = '../app/controllers/' . $this->controller . '.php';
            if (file_exists($controllerFilePath)) {
                require_once $controllerFilePath;
            }
            // Kiểm tra lại sau khi require
             if (!class_exists($this->controller)) {
                 error_log("Router Dispatch Error: Controller class '{$this->controller}' not found even after manual require. Path: {$controllerFilePath}");
                 $this->handleNotFound("Controller class '{$this->controller}' not found or failed to load.");
                 return; // Thêm return để chắc chắn dừng lại
             }
         }

         // Khởi tạo controller
         try {
            $controllerInstance = new $this->controller;
         } catch (Throwable $e) {
             error_log("Router Dispatch Error: Failed to instantiate controller '{$this->controller}'. Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
             $this->handleSystemError("Lỗi khởi tạo hệ thống.");
             return; // Thêm return
         }


         // Kiểm tra action method tồn tại
         if (!method_exists($controllerInstance, $this->action)) {
             error_log("Router Dispatch Error: Action method '{$this->action}' not found in controller '" . get_class($controllerInstance) . "'.");
             $this->handleNotFound("Action method '{$this->action}' not found in controller '{$this->controller}'.");
             return; // Thêm return
         }

         // Gọi action và truyền tham số
         try {
             call_user_func_array([$controllerInstance, $this->action], $this->params);
         } catch (ArgumentCountError $e) {
             error_log("Router Dispatch Error: Incorrect number of parameters for action '{$this->controller}::{$this->action}'. URL Params: [" . implode(', ', $this->params) . "]. Error: " . $e->getMessage());
             $this->handleNotFound("Incorrect number of parameters for action '{$this->action}'.");
         } catch (Throwable $e) {
            error_log("Error executing action '{$this->controller}::{$this->action}': " . $e->getMessage() . "\n" . $e->getTraceAsString());
            $this->handleSystemError("Lỗi xử lý yêu cầu.");
        }
    }

    // handleNotFound() và handleSystemError() giữ nguyên
     protected function handleNotFound($message = "Trang bạn tìm kiếm không tồn tại.") {
        http_response_code(404);
        $view404 = '../app/views/error/404.php';
        if (file_exists($view404)) {
             $errorMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
             include $view404; // Sử dụng include thay vì require_once để tránh lỗi nếu gọi nhiều lần
        } else {
            header('Content-Type: text/html; charset=utf-8');
            echo "<!DOCTYPE html><html><head><title>404 Not Found</title></head><body>";
            echo "<h1>404 Không Tìm Thấy</h1>";
            echo "<p>" . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . "</p>";
            echo "</body></html>";
        }
        exit();
    }

    protected function handleSystemError($userMessage = "Lỗi hệ thống. Vui lòng thử lại sau.") {
        if (http_response_code() < 500) {
           http_response_code(500);
        }
        $view500 = '../app/views/error/500.php';
        if (file_exists($view500)) {
            $errorMessage = htmlspecialchars($userMessage, ENT_QUOTES, 'UTF-8');
            include $view500;
        } else {
            header('Content-Type: text/html; charset=utf-8');
            echo "<!DOCTYPE html><html><head><title>Lỗi Hệ Thống</title></head><body>";
            echo "<h1>Lỗi Hệ Thống</h1>";
            echo "<p>" . htmlspecialchars($userMessage, ENT_QUOTES, 'UTF-8') . "</p>";
            echo "</body></html>";
        }
        exit();
    }

}
?>