<?php
class InvoiceController {
    private $db;
    private $invoiceModel;
    private $authMiddleware;

    public function __construct() {
        $this->db = new Database();
        $this->invoiceModel = new InvoiceModel();
        $this->authMiddleware = new ApiAuthMiddleware();
    }

    public function getInvoices($request) {
        $user = $this->authMiddleware->handle($request, 'invoices.view');
        $filters = $request->getQueryParams();
        $page = isset($filters['page']) ? max(1, (int)$filters['page']) : 1;
        $limit = isset($filters['limit']) ? max(1, min(100, (int)$filters['limit'])) : 20;
        $offset = ($page - 1) * $limit;

        $query = "SELECT i.*, ca.title AS customer_title
                  FROM invoices i
                  JOIN customer_accounts ca ON i.customer_id = ca.id
                  WHERE 1=1";
        $params = [];

        if (!empty($filters['invoice_no'])) {
            $query .= " AND i.invoice_no LIKE :invoice_no";
            $params['invoice_no'] = '%' . $filters['invoice_no'] . '%';
        }
        if (!empty($filters['type'])) {
            $query .= " AND i.type = :type";
            $params['type'] = $filters['type'];
        }
        if (!empty($filters['customer_id'])) {
            $query .= " AND i.customer_id = :customer_id";
            $params['customer_id'] = $filters['customer_id'];
        }

        $total = $this->db->queryOne("SELECT COUNT(*) AS count FROM ($query) AS sub", $params)['count'];
        $query .= " LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        $invoices = $this->db->query($query, $params);

        $this->sendResponse(200, [
            'invoices' => $invoices,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'last_page' => ceil($total / $limit)
            ]
        ]);
    }

    public function getInvoice($request, $id) {
        $user = $this->authMiddleware->handle($request, 'invoices.view');
        $invoice = $this->invoiceModel->getInvoiceById($id);
        if (!$invoice) {
            $this->sendError(404, 'Fatura bulunamadı.');
        }
        $items = $this->db->query("SELECT * FROM invoice_items WHERE invoice_id = :invoice_id", ['invoice_id' => $id]);
        $invoice['items'] = $items;
        $this->sendResponse(200, $invoice);
    }

    public function createInvoice($request) {
        $user = $this->authMiddleware->handle($request, 'invoices.add');
        $data = $request->getBody();
        if (empty($data['invoice_no']) || empty($data['type']) || empty($data['customer_id']) || empty($data['items'])) {
            $this->sendError(400, 'Eksik veya geçersiz veri.');
        }

        $data['created_by'] = $user['id'];
        if ($this->invoiceModel->addInvoice($data)) {
            $this->sendResponse(201, ['message' => 'Fatura oluşturuldu.']);
        } else {
            $this->sendError(500, 'Fatura oluşturma başarısız.');
        }
    }

    public function updateInvoice($request, $id) {
        $user = $this->authMiddleware->handle($request, 'invoices.edit');
        $data = $request->getBody();
        if (empty($data['invoice_no']) || empty($data['type']) || empty($data['customer_id']) || empty($data['items'])) {
            $this->sendError(400, 'Eksik veya geçersiz veri.');
        }

        $data['updated_by'] = $user['id'];
        if ($this->invoiceModel->updateInvoice($id, $data)) {
            $this->sendResponse(200, ['message' => 'Fatura güncellendi.']);
        } else {
            $this->sendError(500, 'Fatura güncelleme başarısız.');
        }
    }

    public function deleteInvoice($request, $id) {
        $user = $this->authMiddleware->handle($request, 'invoices.delete');
        if ($this->invoiceModel->deleteInvoice($id)) {
            $this->sendResponse(200, ['message' => 'Fatura silindi.']);
        } else {
            $this->sendError(500, 'Fatura silme başarısız.');
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