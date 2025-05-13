<?php
class OrderController {
    private $db;
    private $orderModel;
    private $authMiddleware;

    public function __construct() {
        $this->db = new Database();
        $this->orderModel = new OrderModel();
        $this->authMiddleware = new ApiAuthMiddleware();
    }

    public function getOrders($request) {
        $user = $this->authMiddleware->handle($request, 'orders.view');
        $filters = $request->getQueryParams();
        $page = isset($filters['page']) ? max(1, (int)$filters['page']) : 1;
        $limit = isset($filters['limit']) ? max(1, min(100, (int)$filters['limit'])) : 20;
        $offset = ($page - 1) * $limit;

        $query = "SELECT o.*, ca.title AS customer_title
                  FROM orders o
                  JOIN customer_accounts ca ON o.customer_id = ca.id
                  WHERE 1=1";
        $params = [];

        if (!empty($filters['order_no'])) {
            $query .= " AND o.order_no LIKE :order_no";
            $params['order_no'] = '%' . $filters['order_no'] . '%';
        }
        if (!empty($filters['customer_id'])) {
            $query .= " AND o.customer_id = :customer_id";
            $params['customer_id'] = $filters['customer_id'];
        }
        if (!empty($filters['status'])) {
            $query .= " AND o.status = :status";
            $params['status'] = $filters['status'];
        }

        $total = $this->db->queryOne("SELECT COUNT(*) AS count FROM ($query) AS sub", $params)['count'];
        $query .= " LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        $orders = $this->db->query($query, $params);

        $this->sendResponse(200, [
            'orders' => $orders,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'last_page' => ceil($total / $limit)
            ]
        ]);
    }

    public function getOrder($request, $id) {
        $user = $this->authMiddleware->handle($request, 'orders.view');
        $order = $this->orderModel->getOrderById($id);
        if (!$order) {
            $this->sendError(404, 'Sipariş bulunamadı.');
        }
        $items = $this->db->query("SELECT * FROM order_items WHERE order_id = :order_id", ['order_id' => $id]);
        $order['items'] = $items;
        $this->sendResponse(200, $order);
    }

    public function createOrder($request) {
        $user = $this->authMiddleware->handle($request, 'orders.add');
        $data = $request->getBody();
        if (empty($data['order_no']) || empty($data['customer_id']) || empty($data['items'])) {
            $this->sendError(400, 'Eksik veya geçersiz veri.');
        }

        $data['created_by'] = $user['id'];
        if ($this->orderModel->addOrder($data)) {
            $this->sendResponse(201, ['message' => 'Sipariş oluşturuldu.']);
        } else {
            $this->sendError(500, 'Sipariş oluşturma başarısız.');
        }
    }

    public function updateOrder($request, $id) {
        $user = $this->authMiddleware->handle($request, 'orders.edit');
        $data = $request->getBody();
        if (empty($data['order_no']) || empty($data['customer_id']) || empty($data['items'])) {
            $this->sendError(400, 'Eksik veya geçersiz veri.');
        }

        $data['updated_by'] = $user['id'];
        if ($this->orderModel->updateOrder($id, $data)) {
            $this->sendResponse(200, ['message' => 'Sipariş güncellendi.']);
        } else {
            $this->sendError(500, 'Sipariş güncelleme başarısız.');
        }
    }

    public function deleteOrder($request, $id) {
        $user = $this->authMiddleware->handle($request, 'orders.delete');
        if ($this->orderModel->deleteOrder($id)) {
            $this->sendResponse(200, ['message' => 'Sipariş silindi.']);
        } else {
            $this->sendError(500, 'Sipariş silme başarısız.');
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