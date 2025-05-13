<?php
ini_set('session.gc_maxlifetime', 86400); // Session tồn tại 1 ngày
session_set_cookie_params(86400); // Cookie session tồn tại 1 ngày
session_start();

// Xử lý autoload các lớp (tùy chọn nhưng rất hữu ích cho MVC)
spl_autoload_register(function($class) {
    // Ánh xạ lớp tự động
    $paths = [
        'app/controllers/',
        'app/models/',
        'app/views/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Nhận tham số từ URL
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Tạo tên lớp controller
$controllerClass = ucfirst($controller) . 'Controller';
$controllerFile = 'app/controllers/' . $controllerClass . '.php';

try {
    // Kiểm tra xem controller có tồn tại không
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        
        // Tạo đối tượng controller
        if (class_exists($controllerClass)) {
            $controllerObj = new $controllerClass();
            
            // Kiểm tra xem action có tồn tại không
            if (method_exists($controllerObj, $action)) {
                $controllerObj->$action();
            } else {
                throw new Exception("Không tìm thấy action: $action");
            }
        } else {
            throw new Exception("Không tìm thấy controller: $controllerClass");
        }
    } else {
        throw new Exception("Không tìm thấy tệp controller: $controllerFile");
    }
} catch (Exception $e) {
    // Xử lý lỗi - hiển thị thông báo lỗi
    echo "<h1>Lỗi hệ thống:</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
