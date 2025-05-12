<?php
class Router {
    public function dispatch() {
        // URL’yi al ve temizle
        $url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'index';
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $urlParts = explode('/', $url);

        // Modül, controller ve aksiyon belirle
        $module = !empty($urlParts[0]) ? ucfirst(strtolower($urlParts[0])) : 'Index';
        $controller = !empty($urlParts[1]) ? ucfirst(strtolower($urlParts[1])) . 'Controller' : 'IndexController';
        $action = !empty($urlParts[2]) ? strtolower($urlParts[2]) : 'index';
        $params = array_slice($urlParts, 3);

        // Modül dosya yolu
        $modulePath = APP_PATH . '/modules/' . $module;
        $controllerFile = $modulePath . '/controllers/' . $controller . '.php';

        // Controller dosyasını kontrol et
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controllerClass = $controller;

            // Controller örneği oluştur
            if (class_exists($controllerClass)) {
                $controllerInstance = new $controllerClass();

                // Aksiyon kontrolü
                if (method_exists($controllerInstance, $action)) {
                    // Aksiyonu çağır
                    call_user_func_array([$controllerInstance, $action], $params);
                } else {
                    $this->handleError("Aksiyon bulunamadı: $action");
                }
            } else {
                $this->handleError("Controller sınıfı bulunamadı: $controllerClass");
            }
        } else {
            $this->handleError("Controller dosyası bulunamadı: $controllerFile");
        }
    }

    private function handleError($message) {
        // Hata loglama
        error_log($message, 3, BASE_PATH . '/logs/error.log');
        // 404 sayfasına yönlendir (geçici olarak basit hata mesajı)
        header('HTTP/1.0 404 Not Found');
        echo "<h1>404 - Sayfa Bulunamadı</h1><p>$message</p>";
        exit;
    }
}
?>