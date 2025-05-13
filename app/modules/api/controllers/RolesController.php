<?php
class RolesController {
    private $db;
    private $authMiddleware;

    public function __construct() {
        $this->db = new Database();
        $this->authMiddleware = new ApiAuthMiddleware();
    }

    public function getRoles($request) {
        $user = $this->authMiddleware->handle($request, 'roles.view');
        $filters = $request->getQueryParams();
        $page = isset($filters['page']) ? max(1, (int)$filters['page']) : 1;
        $limit = isset($filters['limit']) ? max(1, min(100, (int)$filters['limit'])) : 20;
        $offset = ($page - 1) * $limit;

        $query = "SELECT id, name, description, created_at FROM roles WHERE 1=1";
        $params = [];

        if (!empty($filters['name'])) {
            $query .= " AND name LIKE :name";
            $params['name'] = '%' . $filters['name'] . '%';
        }

        $total = $this->db->queryOne("SELECT COUNT(*) AS count FROM ($query) AS sub", $params)['count'];
        $query .= " LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        $roles = $this->db->query($query, $params);

        $this->sendResponse(200, [
            'roles' => $roles,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'last_page' => ceil($total / $limit)
            ]
        ]);
    }

    public function getRole($request, $id) {
        $user = $this->authMiddleware->handle($request, 'roles.view');
        $role = $this->db->queryOne("SELECT id, name, description, created_at FROM roles WHERE id = :id", ['id' => $id]);
        if (!$role) {
            $this->sendError(404, 'Rol bulunamadı.');
        }
        $this->sendResponse(200, $role);
    }

    public function createRole($request) {
        $user = $this->authMiddleware->handle($request, 'roles.add');
        $data = $request->getBody();
        if (empty($data['name'])) {
            $this->sendError(400, 'Eksik veya geçersiz veri.');
        }

        $query = "INSERT INTO roles (name, description, created_at)
                  VALUES (:name, :description, NOW())";
        $params = [
            'name' => $data['name'],
            'description' => $data['description'] ?? null
        ];

        if ($this->db->execute($query, $params)) {
            $role_id = $this->db->lastInsertId();
            if (!empty($data['permissions'])) {
                foreach ($data['permissions'] as $permission_id) {
                    $this->db->execute("INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)", [
                        'role_id' => $role_id,
                        'permission_id' => $permission_id
                    ]);
                }
            }
            $this->sendResponse(201, ['message' => 'Rol oluşturuldu.']);
        } else {
            $this->sendError(500, 'Rol oluşturma başarısız.');
        }
    }

    public function updateRole($request, $id) {
        $user = $this->authMiddleware->handle($request, 'roles.edit');
        $data = $request->getBody();
        if (empty($data['name'])) {
            $this->sendError(400, 'Eksik veya geçersiz veri.');
        }

        $query = "UPDATE roles SET name = :name, description = :description WHERE id = :id";
        $params = [
            'id' => $id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null
        ];

        if ($this->db->execute($query, $params)) {
            $this->db->execute("DELETE FROM role_permissions WHERE role_id = :role_id", ['role_id' => $id]);
            if (!empty($data['permissions'])) {
                foreach ($data['permissions'] as $permission_id) {
                    $this->db->execute("INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)", [
                        'role_id' => $id,
                        'permission_id' => $permission_id
                    ]);
                }
            }
            $this->sendResponse(200, ['message' => 'Rol güncellendi.']);
        } else {
            $this->sendError(500, 'Rol güncelleme başarısız.');
        }
    }

    public function deleteRole($request, $id) {
        $user = $this->authMiddleware->handle($request, 'roles.delete');
        $query = "DELETE FROM roles WHERE id = :id AND NOT EXISTS (SELECT 1 FROM user_roles WHERE role_id = :id)";
        if ($this->db->execute($query, ['id' => $id])) {
            $this->sendResponse(200, ['message' => 'Rol silindi.']);
        } else {
            $this->sendError(500, 'Rol silme başarısız.');
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