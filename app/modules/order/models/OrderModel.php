<?php
class OrderModel {
    private $db;
    private $session;

    public function __construct() {
        $this->db = new Database();
        $this->session = new Session();
    }

    public function addOrder($data) {
        $query = "INSERT INTO orders (order_no, customer_id, order_date, delivery_date, total_amount, status, description, created_by)
                  VALUES (:order_no, :customer_id, :order_date, :delivery_date, :total_amount, :status, :description, :created_by)";
        $params = [
            'order_no' => $data['order_no'],
            'customer_id' => $data['customer_id'],
            'order_date' => $data['order_date'],
            'delivery_date' => $data['delivery_date'] ?? null,
            'total_amount' => $data['total_amount'],
            'status' => $data['status'] ?? 'pending',
            'description' => $data['description'] ?? null,
            'created_by' => $this->session->get('user_id')
        ];
        return $this->db->execute($query, $params);
    }

    public function updateOrder($id, $data) {
        $query = "UPDATE orders SET
                  order_no = :order_no, customer_id = :customer_id, order_date = :order_date,
                  delivery_date = :delivery_date, total_amount = :total_amount, status = :status,
                  description = :description, updated_by = :updated_by, updated_at = NOW()
                  WHERE id = :id";
        $params = [
            'id' => $id,
            'order_no' => $data['order_no'],
            'customer_id' => $data['customer_id'],
            'order_date' => $data['order_date'],
            'delivery_date' => $data['delivery_date'] ?? null,
            'total_amount' => $data['total_amount'],
            'status' => $data['status'] ?? 'pending',
            'description' => $data['description'] ?? null,
            'updated_by' => $this->session->get('user_id')
        ];
        return $this->db->execute($query, $params);
    }

    public function deleteOrder($id) {
        $query = "DELETE FROM orders WHERE id = :id";
        return $this->db->execute($query, ['id' => $id]);
    }

    public function getOrderById($id) {
        $query = "SELECT o.*, c.title AS customer_title
                  FROM orders o
                  JOIN customer_accounts c ON o.customer_id = c.id
                  WHERE o.id = :id";
        return $this->db->queryOne($query, ['id' => $id]);
    }

    public function getAllOrders($filters = []) {
        $query = "SELECT o.*, c.title AS customer_title
                  FROM orders o
                  JOIN customer_accounts c ON o.customer_id = c.id
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
        if (!empty($filters['start_date'])) {
            $query .= " AND o.order_date >= :start_date";
            $params['start_date'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $query .= " AND o.order_date <= :end_date";
            $params['end_date'] = $filters['end_date'];
        }

        return $this->db->query($query, $params);
    }

    public function getOpenOrderProducts($filters = []) {
        $query = "SELECT sp.id AS product_id, sp.code AS product_code, sp.name AS product_name, sp.unit,
                         SUM(oi.quantity) AS total_quantity,
                         SUM(CASE WHEN oi.invoiced = TRUE THEN oi.quantity ELSE 0 END) AS invoiced_quantity,
                         SUM(CASE WHEN oi.invoiced = FALSE THEN oi.quantity ELSE 0 END) AS uninvoiced_quantity,
                         COUNT(DISTINCT o.customer_id) AS customer_count
                  FROM order_items oi
                  JOIN orders o ON oi.order_id = o.id
                  JOIN stock_products sp ON oi.product_id = sp.id
                  WHERE o.status NOT IN ('completed', 'canceled')
                  GROUP BY sp.id, sp.code, sp.name, sp.unit";
        $params = [];

        if (!empty($filters['product_id'])) {
            $query .= " AND sp.id = :product_id";
            $params['product_id'] = $filters['product_id'];
        }
        if (!empty($filters['product_code'])) {
            $query .= " AND sp.code LIKE :product_code";
            $params['product_code'] = '%' . $filters['product_code'] . '%';
        }
        if (!empty($filters['product_name'])) {
            $query .= " AND sp.name LIKE :product_name";
            $params['product_name'] = '%' . $filters['product_name'] . '%';
        }
        if (!empty($filters['customer_id'])) {
            $query .= " AND o.customer_id = :customer_id";
            $params['customer_id'] = $filters['customer_id'];
        }
        if (!empty($filters['start_date'])) {
            $query .= " AND o.order_date >= :start_date";
            $params['start_date'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $query .= " AND o.order_date <= :end_date";
            $params['end_date'] = $filters['end_date'];
        }

        return $this->db->query($query, $params);
    }

    public function getOpenOrdersByProduct($product_id) {
        $query = "SELECT o.id, o.order_no, c.title AS customer_title, oi.quantity, o.status
                  FROM order_items oi
                  JOIN orders o ON oi.order_id = o.id
                  JOIN customer_accounts c ON o.customer_id = c.id
                  WHERE oi.product_id = :product_id AND o.status NOT IN ('completed', 'canceled')";
        return $this->db->query($query, ['product_id' => $product_id]);
    }
}
?>