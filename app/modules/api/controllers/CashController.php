<?php
class CashController {
    private $db;
    private $cashModel;
    private $authMiddleware;

    public function __construct() {
        $this->db = new Database();
        $this->cashModel = new CashModel();
        $this->authMiddleware = new ApiAuthMiddleware();
    }

    public function getCashRegisters($request) {
        $user = $this->authMiddleware->handle($request, 'cash.view');
        $filters = $request->getQueryParams();
        $page = isset($filters['page']) ? max(1, (int)$filters['page']) : 1;
        $limit = isset($filters['limit']) ? max(1, min(100, (int)$filters['limit'])) : 20;
        $offset = ($page - 1) * $limit;

        $query = "SELECT * FROM cash_registers WHERE 1=1";
        $params = [];

        if (!empty($filters['code'])) {
            $query .= " AND code LIKE :code";
            $params['code'] = '%' . $filters['code'] . '%';
        }
        if (!empty($filters['name'])) {
            $query .= " AND name LIKE :name";
            $params['name'] = '%' . $filters['name'] . '%';
        }
        if (!empty($filters['status'])) {
            $query .= " AND status = :status";
            $params['status'] = $filters['status'];
        }

        $total = $this->db->queryOne("SELECT COUNT(*) AS count FROM ($query) AS sub", $params)['count'];
        $query .= " LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        $cash_registers = $this->db->query($query, $params);

        $this->sendResponse(200, [
            'cash_registers' => $cash_registers,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'last_page' => ceil($total / $limit)
            ]
        ]);
    }

    public function getCashRegister($request, $id) {
        $user = $this->authMiddleware->handle($request, 'cash.view');
        $cash_register = $this->cashModel->getCashRegisterById($id);
        if (!$cash_register) {
            $this->sendError(404, 'Kasa bulunamadı.');
        }
        $this->sendResponse(200, $cash_register);
    }

    public function createCashRegister($request) {
        $user = $this->authMiddleware->handle($request, 'cash.add');
        $data = $request->getBody();
        if (empty($data['code']) || empty($data['name']) || empty($data['currency'])) {
            $this->sendError(400, 'Eksik veya geçersiz veri.');
        }

        $data['created_by'] = $user['id'];
        if ($this->cashModel->addCashRegister($data)) {
            $this->sendResponse(201, ['message' => 'Kasa oluşturuldu.']);
        } else {
            $this->sendError(500, 'Kasa oluşturma başarısız.');
        }
    }

    public function updateCashRegister($request, $id) {
        $user = $this->authMiddleware->handle($request, 'cash.edit');
        $data = $request->getBody();
        if (empty($data['code']) || empty($data['name']) || empty($data['currency'])) {
            $this->sendError(400, 'Eksik veya geçersiz veri.');
        }

        $data['updated_by'] = $user['id'];
        if ($this->cashModel->updateCashRegister($id, $data)) {
            $this->sendResponse(200, ['message' => 'Kasa güncellendi.']);
        } else {
            $this->sendError(500, 'Kasa güncelleme başarısız.');
        }
    }

    public function deleteCashRegister($request, $id) {
        $user = $this->authMiddleware->handle($request, 'cash.delete');
        if ($this->cashModel->deleteCashRegister($id)) {
            $this->sendResponse(200, ['message' => 'Kasa silindi.']);
        } else {
            $this->sendError(500, 'Kasa silme başarısız.');
        }
    }

    public function getCashTransactions($request, $id) {
        $user = $this->authMiddleware->handle($request, 'cash.view');
        $filters = $request->getQueryParams();
        $page = isset($filters['page']) ? max(1, (int)$filters['page']) : 1;
        $limit = isset($filters['limit']) ? max(1, min(100, (int)$filters['limit'])) : 20;
        $offset = ($page - 1) * $limit;

        $query = "SELECT ct.*, ca.title AS customer_title, i.invoice_no, o.order_no
                  FROM cash_transactions ct
                  LEFT JOIN customer_accounts ca ON ct.customer_id = ca.id
                  LEFT JOIN invoices i ON ct.invoice_id = i.id
                  LEFT JOIN orders o ON ct.order_id = o.id
                  WHERE ct.cash_register_id = :cash_register_id";
        $params = ['cash_register_id' => $id];

        if (!empty($filters['type'])) {
            $query .= " AND ct.type = :type";
            $params['type'] = $filters['type'];
        }
        if (!empty($filters['status'])) {
            $query .= " AND ct.status = :status";
            $params['status'] = $filters['status'];
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