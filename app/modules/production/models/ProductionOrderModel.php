<?php
class ProductionOrderModel {
    private $db;
    private $session;

    public function __construct() {
        $this->db = new Database();
        $this->session = new Session();
    }

    public function addProductionOrder($data) {
        // Stok kontrolü
        $recipe = (new RecipeModel())->getRecipeById($data['recipe_id']);
        foreach ($recipe['ingredients'] as $ingredient) {
            $required_quantity = $ingredient['quantity'] * $data['quantity'];
            $stock = $this->db->queryOne("SELECT quantity FROM stock_products WHERE id = :id", ['id' => $ingredient['ingredient_id']]);
            if ($stock['quantity'] < $required_quantity) {
                return ['success' => false, 'message' => "Hammadde {$ingredient['ingredient_name']} için yetersiz stok: {$stock['quantity']} {$ingredient['unit']} mevcut, {$required_quantity} {$ingredient['unit']} gerekli."];
            }
        }

        // Üretim emri ekle
        $order_number = 'PRD-' . time();
        $query = "INSERT INTO production_orders (order_number, recipe_id, quantity, planned_date, tracking, status, created_by)
                  VALUES (:order_number, :recipe_id, :quantity, :planned_date, :tracking, :status, :created_by)";
        $params = [
            'order_number' => $order_number,
            'recipe_id' => $data['recipe_id'],
            'quantity' => $data['quantity'],
            'planned_date' => $data['planned_date'] ?? null,
            'tracking' => $data['tracking'],
            'status' => $data['tracking'] == 'no' ? 'completed' : 'pending',
            'created_by' => $this->session->get('user_id')
        ];
        if ($this->db->execute($query, $params)) {
            $order_id = $this->db->lastInsertId();
            if ($data['tracking'] == 'no') {
                // Hemen üretim tamamla
                $this->completeProduction($order_id);
            } else {
                // Takip adımlarını ekle
                $steps = ['Hazırlık', 'Montaj', 'Kalite Kontrol'];
                foreach ($steps as $step) {
                    $this->db->execute("INSERT INTO production_tracking (order_id, step, status, created_by)
                                        VALUES (:order_id, :step, 'pending', :created_by)", [
                        'order_id' => $order_id,
                        'step' => $step,
                        'created_by' => $this->session->get('user_id')
                    ]);
                }
            }
            return ['success' => true, 'order_id' => $order_id];
        }
        return ['success' => false, 'message' => 'Üretim emri ekleme başarısız.'];
    }

    public function completeProduction($order_id) {
        $order = $this->getProductionOrderById($order_id);
        $recipe = (new RecipeModel())->getRecipeById($order['recipe_id']);

        // Stok çıkışları (bileşenler)
        foreach ($recipe['ingredients'] as $ingredient) {
            $quantity = $ingredient['quantity'] * $order['quantity'];
            $this->db->execute("UPDATE stock_products SET quantity = quantity - :quantity WHERE id = :id", [
                'quantity' => $quantity,
                'id' => $ingredient['ingredient_id']
            ]);
            $this->db->execute("INSERT INTO stock_exits (product_id, quantity, exit_date, reason, created_by)
                                VALUES (:product_id, :quantity, NOW(), 'Üretim: {$order['order_number']}', :created_by)", [
                'product_id' => $ingredient['ingredient_id'],
                'quantity' => $quantity,
                'created_by' => $this->session->get('user_id')
            ]);
        }

        // Stok girişi (üretilen ürün)
        $this->db->execute("UPDATE stock_products SET quantity = quantity + :quantity WHERE id = :id", [
            'quantity' => $order['quantity'],
            'id' => $recipe['product_id']
        ]);
        $this->db->execute("INSERT INTO stock_entries (product_id, quantity, entry_date, created_by)
                            VALUES (:product_id, :quantity, NOW(), :created_by)", [
            'product_id' => $recipe['product_id'],
            'quantity' => $order['quantity'],
            'created_by' => $this->session->get('user_id')
        ]);

        // Üretim emri durumunu güncelle
        $this->db->execute("UPDATE production_orders SET status = 'completed', updated_by = :updated_by, updated_at = NOW() WHERE id = :id", [
            'id' => $order_id,
            'updated_by' => $this->session->get('user_id')
        ]);
    }

    public function updateTrackingStep($tracking_id, $status) {
        $query = "UPDATE production_tracking SET
                  status = :status, completed_at = :completed_at, created_by = :created_by
                  WHERE id = :id";
        $params = [
            'id' => $tracking_id,
            'status' => $status,
            'completed_at' => $status == 'completed' ? date('Y-m-d H:i:s') : null,
            'created_by' => $this->session->get('user_id')
        ];
        if ($this->db->execute($query, $params)) {
            // Tüm adımlar tamamlandıysa üretim emrini tamamla
            $tracking = $this->db->queryOne("SELECT order_id FROM production_tracking WHERE id = :id", ['id' => $tracking_id]);
            $pending_steps = $this->db->queryOne("SELECT COUNT(*) AS count FROM production_tracking WHERE order_id = :order_id AND status != 'completed'", ['order_id' => $tracking['order_id']]);
            if ($pending_steps['count'] == 0) {
                $this->completeProduction($tracking['order_id']);
            }
            return true;
        }
        return false;
    }

    public function getProductionOrderById($id) {
        $query = "SELECT po.*, r.code AS recipe_code, sp.name AS product_name
                  FROM production_orders po
                  JOIN recipes r ON po.recipe_id = r.id
                  JOIN stock_products sp ON r.product_id = sp.id
                  WHERE po.id = :id";
        return $this->db->queryOne($query, ['id' => $id]);
    }

    public function getAllProductionOrders($filters = []) {
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

        return $this->db->query($query, $params);
    }

    public function getTrackingByOrderId($order_id) {
        $query = "SELECT * FROM production_tracking WHERE order_id = :order_id";
        return $this->db->query($query, ['order_id' => $order_id]);
    }
}
?>