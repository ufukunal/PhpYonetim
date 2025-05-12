<?php
/**
 * XSS koruması için veri filtreleme
 * @param string $data
 * @return string
 */
function sanitize($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Yönlendirme yapar
 * @param string $url
 */
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit;
}

/**
 * URL oluşturur
 * @param string $path
 * @return string
 */
function url($path) {
    return BASE_URL . $path;
}

/**
 * Varlık dosyasını bağlar
 * @param string $file
 * @return string
 */
function asset($file) {
    return BASE_URL . 'public/assets/' . $file;
}

/**
 * Hata mesajı loglar
 * @param string $message
 */
function logError($message) {
    if (LOG_ERRORS) {
        error_log($message, 3, BASE_PATH . '/logs/error.log');
    }
}
?>