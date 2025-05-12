<?php
class Auth {
    private $session;
    private $db;

    public function __construct() {
        $this->session = new Session();
        $this->db = new Database();
    }

    /**
     * Kullanıcı girişi
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function login($email, $password) {
        $query = "SELECT * FROM users WHERE email = :email";
        $user = $this->db->queryOne($query, ['email' => $email]);

        if ($user && password_verify($password, $user['password'])) {
            $this->session->set('user_id', $user['id']);
            $this->session->set('user_email', $user['email']);
            return true;
        }

        return false;
    }

    /**
     * Kullanıcı çıkışı
     */
    public function logout() {
        $this->session->destroy();
    }

    /**
     * Kullanıcı oturumunu kontrol eder
     * @return bool
     */
    public function isLoggedIn() {
        return $this->session->get('user_id') !== null;
    }

    /**
     * Kullanıcı iznini kontrol eder
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission) {
        if (!$this->isLoggedIn()) {
            return false;
        }

        $userId = $this->session->get('user_id');
        $query = "SELECT p.name FROM permissions p
                  JOIN role_permissions rp ON p.id = rp.permission_id
                  JOIN user_roles ur ON rp.role_id = ur.role_id
                  WHERE ur.user_id = :user_id AND p.name = :permission";
        $result = $this->db->queryOne($query, [
            'user_id' => $userId,
            'permission' => $permission
        ]);

        return $result !== null;
    }
}
?>