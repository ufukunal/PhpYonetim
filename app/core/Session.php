<?php
class Session {
    public function __construct() {
        // Oturum zaten başlatılmadıysa başlat
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Oturum değerini ayarlar
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    /**
     * Oturum değerini alır
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    /**
     * Oturum değerini siler
     * @param string $key
     */
    public function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Oturumu tamamen yok eder
     */
    public function destroy() {
        session_destroy();
    }

    /**
     * Flash mesaj ayarlar
     * @param string $key
     * @param string $message
     */
    public function setFlash($key, $message) {
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * Flash mesaj alır ve siler
     * @param string $key
     * @param string $default
     * @return string
     */
    public function getFlash($key, $default = '') {
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        return $default;
    }
}
?>