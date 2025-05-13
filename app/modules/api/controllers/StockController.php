<?php
class StockController {
    private $db;
    private $stockModel;
    private $authMiddleware;

    public function __construct() {
        $this->db = new Database();
        $this->stockModel = new StockModel();
        $this->authMiddleware = new ApiAuthMiddleware();
    }

    public function getProducts($request) {
        $user = $this->authMiddleware->handle($request, 'stock.view');
        $filters = $request->getQueryParams();
        $page = isset($filters['page']) ? max(1, (int)$filters['page']) : 1;
        $limit = isset($filters['limit']) ? max(1, min(100, (int)$filters['limit'])) : 20;
        $offset = ($page - 1) * $limit;

        $query = "SELECT sp.*, sg.name AS group_name, ssg.name AS sub_group_name, sssg.name AS sub_sub_group_name
                  FROM stock_products sp
                  JOIN stock_groups sg ON sp.stock_group_id = sg.id
                  JOIN stock_sub_groups ssg ON sp.sub_group_id = ssg.id
                  JOIN stock_sub_sub_groups sssg ON sp.sub_sub_group_id = sssg.id
                  WHERE 1=1";
        $params = [];

        if (!empty($filters['code'])) {
            $query .= " AND sp.code LIKE :code";
            $params['code'] = '%' . $filters['code'] . '%';
        }
        if (!empty($filters['name'])) {
            $query .= " AND sp.name LIKE :name";
            $params['name'] = '%' . $filters['name'] . '%';
        }

        $total = $this->db->queryOne("SELECT COUNT(*) AS count FROM ($query) AS sub", $params)['count'];
        $query .= " LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        $products = $this->db->query($query, $params);

        $this->sendResponse(200, [
            'products' => $products,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'last_page' => ceil($total / $limit)
            ]
        ]);
    }

    public function getProduct($request, $id) {
        $user = $this->authMiddleware->handle($request, 'stock.view');
        $product = $this->stockModel->getProductById($id);
        if (!$product) {
            $this->sendError(404, 'Ürün bulunamadı.');
        }
        $this->sendResponse(200, $product);
    }

    public function createProduct($request) {
        $user = $this->authMiddleware->handle($request, 'stock.add');
        $data = $request->getBody();
        if (empty($data['code']) || empty($data['name']) || empty($data['unit']) || empty($data['stock_group_id']) || empty($data['sub_group_id']) || empty($data['sub_sub_group_id'])) {
            $this->sendError(400, 'Eksik veya geçersiz veri.');
        }

        $data['created_by'] = $user['id'];
        if ($this->stockModel->addProduct($data)) {
            $this->sendResponse(201, ['message' => 'Ürün oluşturuldu.']);
        } else {
            $this->sendError(500, 'Ürün oluşturma başarısız.');
        }
    }

    public function updateProduct($request, $id) {
        $user = $this->authMiddleware->handle($request, 'stock.edit');
        $data = $request->getBody();
        if (empty($data['code']) || empty($data['name']) || empty($data['unit']) || empty($data['stock_group_id']) || empty($data['sub_group_id']) || empty($data['sub_sub_group_id'])) {
            $this->sendError(400, 'Eksik veya geçersiz veri.');
        }

        $data['updated_by'] = $user['id'];
        if ($this->stockModel->updateProduct($id, $data)) {
            $this->sendResponse(200, ['message' => 'Ürün güncellendi.']);
        } else {
            $this->sendError(500, 'Ürün güncelleme başarısız.');
        }
    }

    public function deleteProduct($request, $id) {
        $user = $this->authMiddleware->handle($request, 'stock.delete');
        if ($this->stockModel->deleteProduct($id)) {
            $this->sendResponse(200, ['message' => 'Ürün silindi.']);
        } else {
            $this->sendError(500, 'Ürün silme başarısız.');
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