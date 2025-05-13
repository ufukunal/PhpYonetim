<?php
class OrderItemModel {
    private $db;
    private $session;

    public function __construct() {
        $this->db = new Database();
        $this->session = new Session();
    }

    public function addItem($data) {
        $query = "INSERT INTO order_items (order_id, product_id, quantity, unit_price, tax_rate, discount, total, invoiced)
                  VALUES (:order_id, :product_id, :quantity, :unit_price, :tax_rate, :discount, :total, :invoiced)";
        $params = [
            'order_id' => $data['order_id'],
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'unit_price' => $data['unit_price'],
            'tax_rate' => $data['tax_rate'] ?? 0.00,
            'discount' => $data['discount'] ?? 0.00,
            'total' => $data['total'],
            'invoiced' => $data['invoiced'] ?? false
        ];
        return $this->db->execute($query, $params);
    }

    public function updateItem($id, $data) {
        $query = "UPDATE order_items SET
                  product_id = :product_id, quantity = :quantity, unit_price = :unit_price,
                  tax_rate = :tax_rate, discount = :discount, total = :total, invoiced = :invoiced
                  WHERE id = :id";
        $params = [
            'id' => $id,
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'unit_price' => $data['unit_price'],
            'tax_rate' => $data['tax_rate'] ?? 0.00,
            'discount' => $data['discount'] ?? 0.00,
            'total' => $data['total'],
            'invoiced' => $data['invoiced'] ?? false
        ];
        return $this->db->execute($query, $params);
    }

    public function deleteItem($id) {
        $query = "DELETE FROM order_items WHERE id = :id";
        return $this->db->execute($query, ['id' => $id]);
    }

    public function getItemsByOrder($order_id) {
        $query = "SELECT oi.*, sp.name AS product_name, sp.unit
                  FROM order_items oi
                  JOIN stock_products sp ON oi.product_id = sp.id
                  WHERE oi.order_id = :order_id";
        return $this->db->query($query, ['order_id' => $order_id]);
    }

    public function reserveStock($product_id, $quantity) {
        $query = "UPDATE stock_products SET quantity = quantity - :quantity WHERE id = :product_id AND quantity >= :quantity";
        $params = [
            'product_id' => $product_id,
            'quantity' => $quantity
        ];
        return $this->db->execute($query, $params);
    }

    public function releaseStock($product_id, $quantity) {
        $query = "UPDATE stock_products SET quantity = quantity + :quantity WHERE id = :product_id";
        $params = [
            'product_id' => $product_id,
            'quantity' => $quantity
        ];
        return $this->db->execute($query, $params);
    }

    public function updateStockExit($product_id, $quantity) {
        $query = "INSERT INTO stock_exits (product_id, quantity, exit_date, reason, created_by)
                  VALUES (:product_id, :quantity, NOW(), 'Sipariş faturalandırma', :created_by)";
        $params = [
            'product_id' => $product_id,
            'quantity' => $quantity,
            'created_by' => $this->session->get('user_id')
        ];
        return $this->db->execute($query, $params);
    }
}
?>