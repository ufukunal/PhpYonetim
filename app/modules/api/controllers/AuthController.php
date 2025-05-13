<?php
class AuthController {
    private $db;
    private $auth;

    public function __construct() {
        $this->db = new Database();
        $this->auth = new Auth();
    }

    public function login($request) {
        $data = $request->getBody();
        if (empty($data['email']) || empty($data['password'])) {
            $this->sendError(400, 'Eksik veya geçersiz veri.');
        }

        $user = $this->db->queryOne("SELECT * FROM users WHERE email = :email", ['email' => $data['email']]);
        if (!$user || !password_verify($data['password'], $user['password'])) {
            $this->sendError(401, 'Geçersiz kimlik bilgileri.');
        }

        $token = $this->generateJwt($user['id']);
        $this->sendResponse(200, ['token' => $token]);
    }

    public function generateApiKey($request) {
        $middleware = new ApiAuthMiddleware();
        $user = $middleware->handle($request, 'api.key.generate');
        $key = bin2hex(random_bytes(32));
        $query = "INSERT INTO api_keys (user_id, key) VALUES (:user_id, :key)";
        $params = [
            'user_id' => $user['id'],
            'key' => $key
        ];
        if ($this->db->execute($query, $params)) {
            $this->sendResponse(201, ['api_key' => $key]);
        } else {
            $this->sendError(500, 'API anahtarı oluşturma başarısız.');
        }
    }

    private function generateJwt($user_id) {
        // Örnek JWT oluşturma (gerçek uygulamada kütüphane kullanılabilir)
        $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode([
            'sub' => $user_id,
            'iat' => time(),
            'exp' => time() + 3600 // 1 saat geçerlilik
        ]));
        $secret = 'your_jwt_secret';
        $signature = base64_encode(hash_hmac('sha256', "$header.$payload", $secret, true));
        return "$header.$payload.$signature";
    }

    private function sendResponse($code, $data) {
        header('Content-Type: application/json', true, $code);
        echo json_encode([
            'status' => 'success',
            'data' => $data,
            'timestamp' => date('c')
        ]);
        exit;
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