<?php
class CheckNoteController {
    private $db;
    private $checkModel;
    private $authMiddleware;

    public function __construct() {
        $this->db = new Database();
        $this->checkModel = new CheckModel();
        $this->authMiddleware = new ApiAuthMiddleware();
    }

    public function getChecksNotes($request) {
        $user = $this->authMiddleware->handle($request, 'check.view');
        $filters = $request->getQueryParams();
        $page = isset($filters['page']) ? max(1, (int)$filters['page']) : 1;
        $limit = isset($filters['limit']) ? max(1, min(100, (int)$filters['limit'])) : 20;
        $offset = ($page - 1) * $limit;

        $query = "SELECT cn.*, ca.title AS customer_title, ba.name AS bank_name
                  FROM checks_notes cn
                  JOIN customer_accounts ca ON cn.customer_id = ca.id
                  LEFT JOIN bank_accounts ba ON cn.bank_id = ba.id
                  WHERE 1=1";
        $params = [];

        if (!empty($filters['type'])) {
            $query .= " AND cn.type = :type";
            $params['type'] = $filters['type'];
        }
        if (!empty($filters['document_number'])) {
            $query .= " AND cn.document_number LIKE :document_number";
            $params['document_number'] = '%' . $filters['document_number'] . '%';
        }
        if (!empty($filters['customer_id'])) {
            $query .= " AND cn.customer_id = :customer_id";
            $params['customer_id'] = $filters['customer_id'];
        }
        if (!empty($filters['status'])) {
            $query .= " AND cn.status = :status";
            $params['status'] = $filters['status'];
        }

        $total = $this->db->queryOne("SELECT COUNT(*) AS count FROM ($query) AS sub", $params)['count'];
        $query .= " LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        $checks_notes = $this->db->query($query, $params);

        $this->sendResponse(200, [
            'checks_notes' => $checks_notes,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'last_page' => ceil($total / $limit)
            ]
        ]);
    }

    public function getCheckNote($request, $id) {
        $user = $this->authMiddleware->handle($request, 'check.view');
        $check_note = $this->checkModel->getCheckNoteById($id);
        if (!$check_note) {
            $this->sendError(404, 'Çek/Senet bulunamadı.');
        }
        $this->sendResponse(200, $check_note);
    }

    public function createCheckNote($request) {
        $user = $this->authMiddleware->handle($request, 'check.add');
        $data = $request->getBody();
        if (empty($data['type']) || empty($data['document_number']) || empty($data['customer_id']) || empty($data['issue_date']) || empty($data['due_date']) || empty($data['amount'])) {
            $this->sendError(400, 'Eksik veya geçersiz veri.');
        }

        $data['created_by'] = $user['id'];
        if ($this->checkModel->addCheckNote($data)) {
            $this->sendResponse(201, ['message' => 'Çek/Senet oluşturuldu.']);
        } else {
            $this->sendError(500, 'Çek/Senet oluşturma başarısız.');
        }
    }

    public function updateCheckNote($request, $id) {
        $user = $this->authMiddleware->handle($request, 'check.edit');
        $data = $request->getBody();
        if (empty($data['type']) || empty($data['document_number']) || empty($data['customer_id']) || empty($data['issue_date']) || empty($data['due_date']) || empty($data['amount'])) {
            $this->sendError(400, 'Eksik veya geçersiz veri.');
        }

        $data['updated_by'] = $user['id'];
        if ($this->checkModel->updateCheckNote($id, $data)) {
            $this->sendResponse(200, ['message' => 'Çek/Senet güncellendi.']);
        } else {
            $this->sendError(500, 'Çek/Senet güncelleme başarısız.');
        }
    }

    public function deleteCheckNote($request, $id) {
        $user = $this->authMiddleware->handle($request, 'check.delete');
        if ($this->checkModel->deleteCheckNote($id)) {
            $this->sendResponse(200, ['message' => 'Çek/Senet silindi.']);
        } else {
            $this->sendError(500, 'Çek/Senet silme başarısız.');
        }
    }

    public function getCheckNoteTransactions($request, $id) {
        $user = $this->authMiddleware->handle($request, 'check.view');
        $filters = $request->getQueryParams();
        $page = isset($filters['page']) ? max(1, (int)$filters['page']) : 1;
        $limit = isset($filters['limit']) ? max(1, min(100, (int)$filters['limit'])) : 20;
        $offset = ($page - 1) * $limit;

        $query = "SELECT cnt.*, ba.name AS bank_name, cr.name AS cash_register_name
                  FROM check_note_transactions cnt
                  LEFT JOIN bank_accounts ba ON cnt.bank_account_id = ba.id
                  LEFT JOIN cash_registers cr ON cnt.cash_register_id = cr.id
                  WHERE cnt.check_note_id = :check_note_id";
        $params = ['check_note_id' => $id];

        if (!empty($filters['type'])) {
            $query .= " AND cnt.type = :type";
            $params['type'] = $filters['type'];
        }
        if (!empty($filters['status'])) {
            $query .= " AND cnt.status = :status";
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