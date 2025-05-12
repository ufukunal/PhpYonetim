<?php
class StockModel {
    private $db;
    private $session;

    public function __construct() {
        $this->db = new Database();
        $this->session = new Session();
    }

    public function addProduct($data) {
        $query = "INSERT INTO stock_products (code, name, unit, quantity, min_quantity, description, stock_group_id, sub_group_id, sub_sub_group_id, created_by)
                  VALUES (:code, :name, :unit, :quantity, :min_quantity, :description, :stock_group_id, :sub_group_id, :sub_sub_group_id, :created_by)";
        $params = [
            'code' => $data['code'],
            'name' => $data['name'],
            'unit' => $data['unit'],
            'quantity' => $data['quantity'] ?? 0,
            'min_quantity' => $data['min_quantity'] ?? 0,
            'description' => $data['description'],
            'stock_group_id' => $data['stock_group_id'],
            'sub_group_id' => $data['sub_group_id'],
            'sub_sub_group_id' => $data['sub_sub_group_id'],
            'created_by' => $this->session->get('user_id')
        ];
        return $this->db->execute($query, $params);
    }

    public function updateProduct($id, $data) {
        $query = "UPDATE stock_products SET
                  code = :code, name = :name, unit = :unit, quantity = :quantity, min_quantity = :min_quantity,
                  description = :description, stock_group_id = :stock_group_id, sub_group_id = :sub_group_id,
                  sub_sub_group_id = :sub_sub_group_id, updated_by = :updated_by, updated_at = NOW()
                  WHERE id = :id";
        $params = [
            'id' => $id,
            'code' => $data['code'],
            'name' => $data['name'],
            'unit' => $data['unit'],
            'quantity' => $data['quantity'],
            'min_quantity' => $data['min_quantity'],
            'description' => $data['description'],
            'stock_group_id' => $data['stock_group_id'],
            'sub_group_id' => $data['sub_group_id'],
            'sub_sub_group_id' => $data['sub_sub_group_id'],
            'updated_by' => $this->session->get('user_id')
        ];
        return $this->db->execute($query, $params);
    }

    public function deleteProduct($id) {
        $query = "DELETE FROM stock_products WHERE id = :id";
        return $this->db->execute($query, ['id' => $id]);
    }

    public function getProductById($id) {
        $query = "SELECT * FROM stock_products WHERE id = :id";
        return $this->db->queryOne($query, ['id' => $id]);
    }

    public function getAllProducts($filters = []) {
        $query = "SELECT p.*, g.name AS group_name, sg.name AS sub_group_name, ssg.name AS sub_sub_group_name
                  FROM stock_products p
                  JOIN stock_groups g ON p.stock_group_id = g.id
                  JOIN stock_sub_groups sg ON p.sub_group_id = sg.id
                  JOIN stock_sub_sub_groups ssg ON p.sub_sub_group_id = ssg.id
                  WHERE 1=1";
        $params = [];

        if (!empty($filters['min_quantity'])) {
            $query .= " AND p.quantity < :min_quantity";
            $params['min_quantity'] = $filters['min_quantity'];
        }
        if (!empty($filters['stock_group_id'])) {
            $query .= " AND p.stock_group_id = :stock_group_id";
            $params['stock_group_id'] = $filters['stock_group_id'];
        }
        if (!empty($filters['sub_group_id'])) {
            $query .= " AND p.sub_group_id = :sub_group_id";
            $params['sub_group_id'] = $filters['sub_group_id'];
        }
        if (!empty($filters['sub_sub_group_id'])) {
            $query .= " AND p.sub_sub_group_id = :sub_sub_group_id";
            $params['sub_sub_group_id'] = $filters['sub_sub_group_id'];
        }

        return $this->db->query($query, $params);
    }

    public function addEntry($data) {
        $query = "INSERT INTO stock_entries (product_id, quantity, entry_date, invoice_no, created_by)
                  VALUES (:product_id, :quantity, :entry_date, :invoice_no, :created_by)";
        $params = [
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'entry_date' => $data['entry_date'],
            'invoice_no' => $data['invoice_no'],
            'created_by' => $this->session->get('user_id')
        ];
        if ($this->db->execute($query, $params)) {
            $updateQuery = "UPDATE stock_products SET quantity = quantity + :quantity WHERE id = :product_id";
            return $this->db->execute($updateQuery, ['quantity' => $data['quantity'], 'product_id' => $data['product_id']]);
        }
        return false;
    }

    public function addExit($data) {
        $query = "INSERT INTO stock_exits (product_id, quantity, exit_date, reason, created_by)
                  VALUES (:product_id, :quantity, :exit_date, :reason, :created_by)";
        $params = [
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'exit_date' => $data['exit_date'],
            'reason' => $data['reason'],
            'created_by' => $this->session->get('user_id')
        ];
        if ($this->db->execute($query, $params)) {
            $updateQuery = "UPDATE stock_products SET quantity = quantity - :quantity WHERE id = :product_id";
            return $this->db->execute($updateQuery, ['quantity' => $data['quantity'], 'product_id' => $data['product_id']]);
        }
        return false;
    }

    public function getEntries($product_id = null) {
        $query = "SELECT e.*, p.name AS product_name FROM stock_entries e
                  JOIN stock_products p ON e.product_id = p.id";
        $params = [];
        if ($product_id) {
            $query .= " WHERE e.product_id = :product_id";
            $params['product_id'] = $product_id;
        }
        return $this->db->query($query, $params);
    }

    public function getExits($product_id = null) {
        $query = "SELECT e.*, p.name AS product_name FROM stock_exits e
                  JOIN stock_products p ON e.product_id = p.id";
        $params = [];
        if ($product_id) {
            $query .= " WHERE e.product_id = :product_id";
            $params['product_id'] = $product_id;
        }
        return $this->db->query($query, $params);
    }

    public function addInventoryCount($data) {
        // Envanter sayımı için özel bir tablo kullanılabilir, burada örnek olarak ürün miktarını güncelliyoruz
        $query = "UPDATE stock_products SET quantity = :quantity, updated_by = :updated_by, updated_at = NOW()
                  WHERE id = :product_id";
        $params = [
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'updated_by' => $this->session->get('user_id')
        ];
        return $this->db->execute($query, $params);
    }
}
?>