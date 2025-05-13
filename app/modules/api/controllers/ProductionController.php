<?php
class ProductionController {
    private $db;
    private $recipeModel;
    private $productionOrderModel;
    private $authMiddleware;

    public function __construct() {
        $this->db = new Database();
        $this->recipeModel = new RecipeModel();
        $this->productionOrderModel = new ProductionOrderModel();
        $this->authMiddleware = new ApiAuthMiddleware();
    }

    public function getRecipes($request) {
        $user = $this->authMiddleware->handle($request, 'production.view');
        $filters = $request->getQueryParams();
        $page = isset($filters['page']) ? max(1, (int)$filters['page']) : 1;
        $limit = isset($filters['limit']) ? max(1, min(100, (int)$filters['limit'])) : 20;
        $offset = ($page - 1) * $limit;

        $query = "SELECT r.*, sp.name AS product_name
                  FROM recipes r
                  JOIN stock_products sp ON r.product_id = sp.id
                  WHERE 1=1";
        $params = [];

        if (!empty($filters['code'])) {
            $query .= " AND r.code LIKE :code";
            $params['code'] = '%' . $filters['code'] . '%';
        }
        if (!empty($filters['status'])) {
            $query .= " AND r.status = :status";
            $params['status'] = $filters['status'];
        }

        $total = $this->db->queryOne("SELECT COUNT(*) AS count FROM ($query) AS sub", $params)['count'];
        $query .= " LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        $recipes = $this->db->query($query, $params);

        $this->sendResponse(200, [
            'recipes' => $recipes,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'last_page' => ceil($total / $limit)
            ]
        ]);
    }

    public function getRecipe($request, $id) {
        $user = $this->authMiddleware->handle($request, 'production.view');
        $recipe = $this->recipeModel->getRecipeById($id);
        if (!$recipe) {
            $this->sendError(404, 'Reçete bulunamadı.');
        }
        $this->sendResponse(200, $recipe);
    }

    public function createRecipe($request) {
        $user = $this->authMiddleware->handle($request, 'production.add');
        $data = $request->getBody();
        if (empty($data['code']) || empty($data['product_id']) || empty($data['ingredients'])) {
            $this->sendError(400, 'Eksik veya geçersiz veri.');
        }

        $data['created_by'] = $user['id'];
        if ($this->recipeModel->addRecipe($data)) {
            $this->sendResponse(201, ['message' => 'Reçete oluşturuldu.']);
        } else {
            $this->sendError(500, 'Reçete oluşturma başarısız.');
        }
    }

    public function updateRecipe($request, $id) {
        $user = $this->authMiddleware->handle($request, 'production.edit');
        $data = $request->getBody();
        if (empty($data['code']) || empty($data['product_id']) || empty($data['ingredients'])) {
            $this->sendError(400, 'Eksik veya geçersiz veri.');
        }

        $data['updated_by'] = $user['id'];
        if ($this->recipeModel->updateRecipe($id, $data)) {
            $this->sendResponse(200, ['message' => 'Reçete güncellendi.']);
        } else {
            $this->sendError(500, 'Reçete güncelleme başarısız.');
        }
    }

    public function deleteRecipe($request, $id) {
        $user = $this->authMiddleware->handle($request, 'production.delete');
        if ($this->recipeModel->deleteRecipe($id)) {
            $this->sendResponse(200, ['message' => 'Reçete silindi.']);
        } else {
            $this->sendError(500, 'Reçete silme başarısız.');
        }
    }

    public function getProductionOrders($request) {
        $user = $this->authMiddleware->handle($request, 'production.view');
        $filters = $request->getQueryParams();
        $page = isset($filters['page']) ? max(1, (int)$filters['page']) : 1;
        $limit = isset($filters['limit']) ? max(1, min(100, (int)$filters['limit'])) : 20;
        $offset = ($page - 1) * $limit;

        $query = "SELECT po.*, r.code AS recipe_code, sp.name AS product_name
                  FROM production_orders po
                  JOIN recipes r ON po.recipe_id = r.id
                  JOIN stock_products sp ON r.product_id = sp.id
                  WHERE 1=1";
        $params = [];

        if (!empty($filters['order_number'])) {
            $query .= " AND po.order_number LIKE :order_number";
            $params['order_number'] = '%' . $filters['order_number'] . '%';
        }
        if (!empty($filters['status'])) {
            $query .= " AND po.status = :status";
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

    public function getProductionOrder($request, $id) {
        $user = $this->authMiddleware->handle($request, 'production.view');
        $order = $this->productionOrderModel->getProductionOrderById($id);
        if (!$order) {
            $this->sendError(404, 'Üretim emri bulunamadı.');
        }
        $this->sendResponse(200, $order);
    }

    public function createProductionOrder($request) {
        $user = $this->authMiddleware->handle($request, 'production.add');
        $data = $request->getBody();
        if (empty($data['recipe_id']) || empty($data['quantity']) || empty($data['tracking'])) {
            $this->sendError(400, 'Eksik veya geçersiz veri.');
        }

        $data['created_by'] = $user['id'];
        $result = $this->productionOrderModel->addProductionOrder($data);
        if ($result['success']) {
            $this->sendResponse(201, ['message' => 'Üretim emri oluşturuldu.', 'order_id' => $result['order_id']]);
        } else {
            $this->sendError(400, $result['message']);
        }
    }

    public function updateProductionOrder($request, $id) {
        $user = $this->authMiddleware->handle($request, 'production.edit');
        $data = $request->getBody();
        if (empty($data['recipe_id']) || empty($data['quantity']) || empty($data['tracking'])) {
            $this->sendError(400, 'Eksik veya geçersiz veri.');
        }

        $data['updated_by'] = $user['id'];
        if ($this->productionOrderModel->updateProductionOrder($id, $data)) {
            $this->sendResponse(200, ['message' => 'Üretim emri güncellendi.']);
        } else {
            $this->sendError(500, 'Üretim emri güncelleme başarısız.');
        }
    }

    public function deleteProductionOrder($request, $id) {
        $user = $this->authMiddleware->handle($request, 'production.delete');
        if ($this->productionOrderModel->deleteProductionOrder($id)) {
            $this->sendResponse(200, ['message' => 'Üretim emri silindi.']);
        } else {
            $this->sendError(500, 'Üretim emri silme başarısız.');
        }
    }

    public function getProductionTracking($request, $id) {
        $user = $this->authMiddleware->handle($request, 'production.view');
        $tracking_steps = $this->productionOrderModel->getTrackingByOrderId($id);
        if (empty($tracking_steps)) {
            $this->sendError(404, 'Üretim emri veya takip adımları bulunamadı.');
        }
        $this->sendResponse(200, ['tracking_steps' => $tracking_steps]);
    }

    public function updateProductionTracking($request, $id) {
        $user = $this->authMiddleware->handle($request, 'production.track');
        $data = $request->getBody();
        if (empty($data['status'])) {
            $this->sendError(400, 'Eksik veya geçersiz veri.');
        }

        if ($this->productionOrderModel->updateTrackingStep($id, $data['status'])) {
            $this->sendResponse(200, ['message' => 'Takip adımı güncellendi.']);
        } else {
            $this->sendError(500, 'Takip adımı güncelleme başarısız.');
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