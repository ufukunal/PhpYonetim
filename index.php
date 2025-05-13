<?php
// Hata raporlamasını etkinleştir (geliştirme için)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Sabitler
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('CORE_PATH', APP_PATH . '/core');
define('PUBLIC_PATH', BASE_PATH . '/public');

// Otomatik yükleme için basit bir autoloader
spl_autoload_register(function ($class) {
    $file = CORE_PATH . '/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Oturum başlatma
session_start();

// Config dosyasını yükle
require_once CORE_PATH . '/Config.php';

// Router’ı başlat
$router = new Router();
$router->dispatch();
?>