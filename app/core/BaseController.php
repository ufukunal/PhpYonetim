<?php
class BaseController {
    protected $db;

    public function __construct() {
        // Veritabanı bağlantısını başlat
        $this->db = new Database();
    }

    /**
     * View dosyasını yükler
     * @param string $view
     * @param array $data
     */
    protected function view($view, $data = []) {
        // Verileri extract et
        extract($data);

        // View dosyasını yükle
        $viewFile = APP_PATH . '/modules/' . explode('/', $view)[0] . '/views/' . explode('/', $view)[1] . '.php';
        if (file_exists($viewFile)) {
            require_once APP_PATH . '/templates/layout.php';
        } else {
            error_log("View bulunamadı: $viewFile", 3, BASE_PATH . '/logs/error.log');
            die("View dosyası bulunamadı.");
        }
    }

    /**
     * Yönlendirme yapar
     * @param string $url
     */
    protected function redirect($url) {
        header("Location: " . BASE_URL . $url);
        exit;
    }
}
?>