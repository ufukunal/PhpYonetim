<?php
class BankController {
    private $db;
    private $bankModel;
    private $authMiddleware;

    public function __construct() {
        $this->db = new Database();
        $this->bankModel = new BankModel();
        $this->authMiddleware = new ApiAuthMiddleware();
    }

    public function getBankAccounts($request) {
        $user = $this->authMiddleware->handle($request, 'bank.view');
        $filters = $request->getQueryParams();
        $page = isset($filters['page']) ? max(1, (int)$filters['page']) : 1;
        $limit = isset($filters['limit']) ? max(1, min(100, (int)$filters['limit'])) : 20;
        $offset = ($page - 1) * $limit;

        $query = "SELECT * FROM bank_accounts WHERE 1=1";
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

        $bank_accounts = $this->db->query($query, $params);

        $this->sendResponse(200, [
            'bank_accounts' => $bank_accounts,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'last_page' => ceil($total / $limit)
            ]
        ]);
    }

    public function getBankAccount($request, $id) {
        $user = $this->authMiddleware->handle($request, 'bank.view');
        $bank_account = $this->bankModel->getBankAccountById($id);
        if (!$bank_account) {
            $this->sendError(404, 'Banka hesabı bulunamadı.');
        }
        $this->sendResponse(200, $bank_account);
    }

    public function createBankAccount($request) {
        $user = $this->authMiddleware->handle($request, 'bank.add');
        $data = $request->getBody();
        if (empty($data['code']) || empty($data['name']) || empty($data['bank_name']) || empty($data['iban']) || empty($data['currency'])) {
            $this->sendError(400, 'Eksik veya geçersiz veri.');
        }

        $data['created_by'] = $user['id'];
        if ($this->bankModel->addBankAccount($data)) {
            $this->sendResponse(201, ['message' => 'Banka hesabı oluşturuldu.']);
        } else {
            $this->sendError(500, 'Banka hesabı oluşturma başarısız.');
        }
    }

    public function updateBankAccount($request, $id) {
        $user = $this->authMiddleware->handle($request, 'bank.edit');
        $data = $request->getBody();
        if (empty($data['code']) || empty($data['name']) || empty($data['bank_name']) || empty($data['iban']) || empty($data['currency'])) {
            $this->sendError(400, 'Eksik veya geçersiz veri.');
        }

        $data['updated_by'] = $user['id'];
        if ($this->bankModel->updateBankAccount($id, $data)) {
            $this->sendResponse(200, ['message' => 'Banka hesabı güncellendi.']);
        } else {
            $this->sendError(500, 'Banka hesabı güncelleme başarısız.');
        }
    }

    public function deleteBankAccount($request, $id) {
        $user = $this->authMiddleware->handle($request, 'bank.delete');
        if ($this->bankModel->deleteBankAccount($id)) {
            $this->sendResponse(200, ['message' => 'Banka hesabı silindi.']);
        } else {
            $this->sendError(500, 'Banka hesabı silme başarısız.');
        }
    }

    public function getBankTransactions($request, $id) {
        $user = $this->authMiddleware->handle($request, 'bank.view');
        $filters = $request->getQueryParams();
        $page = isset($filters['page']) ? max(1, (int)$filters['page']) : 1;
        $limit = isset($filters['limit']) ? max(1, min(100, (int)$filters['limit'])) : 20;
        $offset = ($page - 1) * $limit;

        $query = "SELECT bt.*, ca.title AS customer_title, i.invoice_no, o.order_no
                  FROM bank_transactions bt
                  LEFT JOIN customer_accounts ca ON bt.customer_id = ca.id
                  LEFT JOIN invoices i ON bt.invoice_id = i.id
                  LEFT JOIN orders o ON bt.order_id = o.id
                  WHERE bt.bank_account_id = :bank_account_id";
        $params = ['bank_account_id' => $id];

        if (!empty($filters['type'])) {
            $query .= " AND bt.type = :type";
            $params['type'] = $filters['type'];
        }
        if (!empty($filters['status'])) {
            $query .= " AND bt.status = :status";
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