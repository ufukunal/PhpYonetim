<?php
class ApiAuthMiddleware {
    private $db;
    private $auth;

    public function __construct() {
        $this->db = new Database();
        $this->auth = new Auth();
    }

    public function handle($request, $required_permission = null) {
        // API anahtarı kontrolü
        $api_key = $request->getHeader('X-API-Key');
        if (!$api_key) {
            $this->sendError(401, 'API anahtarı eksik.');
        }

        $key = $this->db->queryOne("SELECT * FROM api_keys WHERE key = :key", ['key' => $api_key]);
        if (!$key) {
            $this->sendError(401, 'Geçersiz API anahtarı.');
        }

        // JWT kontrolü
        $auth_header = $request->getHeader('Authorization');
        if (!$auth_header || !preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) {
            $this->sendError(401, 'JWT token eksik.');
        }

        $token = $matches[1];
        $decoded = $this->verifyJwt($token);
        if (!$decoded) {
            $this->sendError(401, 'Geçersiz veya süresi dolmuş JWT token.');
        }

        // Kullanıcıyı yükle
        $user_id = $decoded->sub;
        $user = $this->db->queryOne("SELECT * FROM users WHERE id = :id", ['id' => $user_id]);
        if (!$user) {
            $this->sendError(401, 'Kullanıcı bulunamadı.');
        }

        // Yetki kontrolü
        if ($required_permission && !$this->auth->hasPermission($required_permission, $user_id)) {
            $this->sendError(403, 'Bu işlem için yetkiniz yok.');
        }

        // Rate limiting (dakikada 100 istek)
        $this->checkRateLimit($api_key);

        return $user;
    }

    private function verifyJwt($token) {
        // JWT doğrulama (örnek, gerçek uygulamada kütüphane kullanılabilir)
        // Burada basit bir doğrulama simüle ediyoruz
        try {
            // Örnek: JWT_SECRET ile doğrulama
            $secret = 'your_jwt_secret';
            $decoded = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $token)[1]))));
            if ($decoded->exp < time()) {
                return false;
            }
            return $decoded;
        } catch (Exception $e) {
            return false;
        }
    }

    private function checkRateLimit($api_key) {
        // Basit rate limiting (örnek: dosya tabanlı)
        $cache_file = "/tmp/rate_limit_{$api_key}.json";
        $limit = 100; // Dakikada 100 istek
        $window = 60; // 1 dakika

        if (file_exists($cache_file)) {
            $data = json_decode(file_get_contents($cache_file), true);
            if ($data['time'] > time() - $window) {
                if ($data['count'] >= $limit) {
                    $this->sendError(429, 'İstek limiti aşıldı. Lütfen bir süre bekleyin.');
                }
                $data['count']++;
            } else {
                $data = ['time' => time(), 'count' => 1];
            }
        } else {
            $data = ['time' => time(), 'count' => 1];
        }

        file_put_contents($cache_file, json_encode($data));
    }

    private function sendError($code, $message) {
        header('Content-Type: application/json', true, $code);
        echo json_encode([
            'status' => 'error',
            'message' => $message,
            'timestamp' => date('c')
        ]);
        exit;
    }
}
?>