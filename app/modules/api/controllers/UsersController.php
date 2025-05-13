<?php
class UsersController {
    private $db;
    private $authMiddleware;

    public function __construct() {
        $this->db = new Database();
        $this->authMiddleware = new ApiAuthMiddleware();
    }

    public function getUsers($request) {
        $user = $this->authMiddleware->handle($request, 'users.view');
        $filters = $request->getQueryParams();
        $page = isset($filters['page']) ? max(1, (int)$filters['page']) : 1;
        $limit = isset($filters['limit']) ? max(1, min(100, (int)$filters['limit'])) : 20;
        $offset = ($page - 1) * $limit;

        $query = "SELECT id, email, first_name, last_name, created_at FROM users WHERE 1=1";
        $params = [];

        if (!empty($filters['email'])) {
            $query .= " AND email LIKE :email";
            $params['email'] = '%' . $filters['email'] . '%';
        }
        if (!empty($filters['first_name'])) {
            $query .= " AND first_name LIKE :first_name";
            $params['first_name'] = '%' . $filters['first_name'] . '%';
        }

        $total = $this->db->queryOne("SELECT COUNT(*) AS count FROM ($query) AS sub", $params)['count'];
        $query .= " LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        $users = $this->db->query($query, $params);

        $this->sendResponse(200, [
            'users' => $users,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'last_page' => ceil($total / $limit)
            ]
        ]);
    }

    public function getUser($request, $id) {
        $user = $this->authMiddleware->handle($request, 'users.view');
        $user_data = $this->db->queryOne("SELECT id, email, first_name, last_name, created_at FROM users WHERE id = :id", ['id' => $id]);
        if (!$user_data) {
            $this->sendError(404, 'Kullanıcı bulunamadı.');
        }
        $this->sendResponse(200, $user_data);
    }

    public function createUser($request) {
        $user = $this->authMiddleware->handle($request, 'users.add');
        $data = $request->getBody();
        if (empty($data['email']) || empty($data['password']) || empty($data['first_name']) || empty($data['last_name'])) {
            $this->sendError(400, 'Eksik veya geçersiz veri.');
        }

        $query = "INSERT INTO users (email, password, first_name, last_name, created_at)
                  VALUES (:email, :password, :first_name, :last_name, NOW())";
        $params = [
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name']
        ];

        if ($this->db->execute($query, $params)) {
            $this->sendResponse(201, ['message' => 'Kullanıcı oluşturuldu.']);
        } else {
            $this->sendError(500, 'Kullanıcı oluşturma başarısız.');
        }
    }

    public function updateUser($request, $id) {
        $user = $this->authMiddleware->handle($request, 'users.edit');
        $data = $request->getBody();
        if (empty($data['email']) || empty($data['first_name']) || empty($data['last_name'])) {
            $this->sendError(400, 'Eksik veya geçersiz veri.');
        }

        $query = "UPDATE users SET
                  email = :email,
                  first_name = :first_name,
                  last_name = :last_name";
        $params = [
            'id' => $id,
            'email' => $data['email'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name']
        ];

        if (!empty($data['password'])) {
            $query .= ", password = :password";
            $params['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $query .= " WHERE id = :id";

        if ($this->db->execute($query, $params)) {
            $this->sendResponse(200, ['message' => 'Kullanıcı güncellendi.']);
        } else {
            $this->sendError(500, 'Kullanıcı güncelleme başarısız.');
        }
    }

    public function deleteUser($request, $id) {
        $user = $this->authMiddleware->handle($request, 'users.delete');
        $query = "DELETE FROM users WHERE id = :id AND NOT EXISTS (
                    SELECT 1 FROM user_roles WHERE user_id = :id
                    UNION SELECT 1 FROM api_keys WHERE user_id = :id
                )";
        if ($this->db->execute($query, ['id' => $id])) {
            $this->sendResponse(200, ['message' => 'Kullanıcı silindi.']);
        } else {
            $this->sendError(500, 'Kullanıcı silme başarısız.');
        }
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