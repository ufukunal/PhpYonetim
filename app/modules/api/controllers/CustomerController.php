<?php
class CustomerController {
    private $db;
    private $customerModel;
    private $authMiddleware;

    public function __construct() {
        $this->db = new Database();
        $this->customerModel = new CustomerModel();
        $this->authMiddleware = new ApiAuthMiddleware();
    }

    public function getCustomers($request) {
        $user = $this->authMiddleware->handle($request, 'customers.view');
        $filters = $request->getQueryParams();
        $page = isset($filters['page']) ? max(1, (int)$filters['page']) : 1;
        $limit = isset($filters['limit']) ? max(1, min(100, (int)$filters['limit'])) : 20;
        $offset = ($page - 1) * $limit;

        $query = "SELECT ca.*, cg.name AS group_name
                  FROM customer_accounts ca
                  LEFT JOIN customer_groups cg ON ca.group_id = cg.id
                  WHERE 1=1";
        $params = [];

        if (!empty($filters['code'])) {
            $query .= " AND ca.code LIKE :code";
            $params['code'] = '%' . $filters['code'] . '%';
        }
        if (!empty($filters['title'])) {
            $query .= " AND ca.title LIKE :title";
            $params['title'] = '%' . $filters['title'] . '%';
        }
        if (!empty($filters['type'])) {
            $query .= " AND ca.type = :type";
            $params['type'] = $filters['type'];
        }

        $total = $this->db->queryOne("SELECT COUNT(*) AS count FROM ($query) AS sub", $params)['count'];
        $query .= " LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        $customers = $this->db->query($query, $params);

        $this->sendResponse(200, [
            'customers' => $customers,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'last_page' => ceil($total / $limit)
            ]
        ]);
    }

    public function getCustomer($request, $id) {
        $user = $this->authMiddleware->handle($request, 'customers.view');
        $customer = $this->customerModel->getCustomerById($id);
        if (!$customer) {
            $this->sendError(404, 'Müşteri bulunamadı.');
        }
        $this->sendResponse(200, $customer);
    }

    public function createCustomer($request) {
        $user = $this->authMiddleware->handle($request, 'customers.add');
        $data = $request->getBody();
        if (empty($data['code']) || empty($data['type']) || empty($data['title'])) {
            $this->sendError(400, 'Eksik veya geçersiz veri.');
        }

        $data['created_by'] = $user['id'];
        if ($this->customerModel->addCustomer($data)) {
            $this->sendResponse(201, ['message' => 'Müşteri oluşturuldu.']);
        } else {
            $this->sendError(500, 'Müşteri oluşturma başarısız.');
        }
    }

    public function updateCustomer($request, $id) {
        $user = $this->authMiddleware->handle($request, 'customers.edit');
        $data = $request->getBody();
        if (empty($data['code']) || empty($data['type']) || empty($data['title'])) {
            $this->sendError(400, 'Eksik veya geçersiz veri.');
        }

        $data['updated_by'] = $user['id'];
        if ($this->customerModel->updateCustomer($id, $data)) {
            $this->sendResponse(200, ['message' => 'Müşteri güncellendi.']);
        } else {
            $this->sendError(500, 'Müşteri güncelleme başarısız.');
        }
    }

    public function deleteCustomer($request, $id) {
        $user = $this->authMiddleware->handle($request, 'customers.delete');
        if ($this->customerModel->deleteCustomer($id)) {
            $this->sendResponse(200, ['message' => 'Müşteri silindi.']);
        } else {
            $this->sendError(500, 'Müşteri silme başarısız.');
        }
    }

    public function getCustomerTransactions($request, $id) {
        $user = $this->authMiddleware->handle($request, 'customers.view');
        $filters = $request->getQueryParams();
        $page = isset($filters['page']) ? max(1, (int)$filters['page']) : 1;
        $limit = isset($filters['limit']) ? max(1, min(100, (int)$filters['limit'])) : 20;
        $offset = ($page - 1) * $limit;

        $query = "SELECT * FROM customer_transactions WHERE customer_id = :customer_id";
        $params = ['customer_id' => $id];

        if (!empty($filters['type'])) {
            $query .= " AND type = :type";
            $params['type'] = $filters['type'];
        }

        $total = $this->db->queryOne("SELECT COUNT(*) AS count FROM ($query) AS sub", $params)['count'];
        $query .= " LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        $transactions = $this->db->query($query, $params);

        $this->sendResponse(200, [
            'transactions' => $transactions,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'last_page' => ceil($total / $limit)
            ]
        ]);
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