<?php
class InvoiceItemModel {
    private $db;
    private $session;

    public function __construct() {
        $this->db = new Database();
        $this->session = new Session();
    }

    public function addItem($data) {
        $query = "INSERT INTO invoice_items (invoice_id, product_id, quantity, unit_price, tax_rate, discount, total)
                  VALUES (:invoice_id, :product_id, :quantity, :unit_price, :tax_rate, :discount, :total)";
        $params = [
            'invoice_id' => $data['invoice_id'],
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'unit_price' => $data['unit_price'],
            'tax_rate' => $data['tax_rate'] ?? 0.00,
            'discount' => $data['discount'] ?? 0.00,
            'total' => $data['total']
        ];
        return $this->db->execute($query, $params);
    }

    public function updateItem($id, $data) {
        $query = "UPDATE invoice_items SET
                  product_id = :product_id, quantity = :quantity, unit_price = :unit_price,
                  tax_rate = :tax_rate, discount = :discount, total = :total
                  WHERE id = :id";
        $params = [
            'id' => $id,
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'unit_price' => $data['unit_price'],
            'tax_rate' => $data['tax_rate'] ?? 0.00,
            'discount' => $data['discount'] ?? 0.00,
            'total' => $data['total']
        ];
        return $this->db->execute($query, $params);
    }

    public function deleteItem($id) {
        $query = "DELETE FROM invoice_items WHERE id = :id";
        return $this->db->execute($query, ['id' => $id]);
    }

    public function getItemsByInvoice($invoice_id) {
        $query = "SELECT ii.*, sp.name AS product_name
                  FROM invoice_items ii
                  JOIN stock_products sp ON ii.product_id = sp.id
                  WHERE ii.invoice_id = :invoice_id";
        return $this->db->query($query, ['invoice_id' => $invoice_id]);
    }

    public function updateStock($product_id, $quantity, $type) {
        $table = ($type === 'sale' || $type === 'return' && $quantity < 0) ? 'stock_exits' : 'stock_entries';
        $query = "INSERT INTO $table (product_id, quantity, entry_date, created_by)
                  VALUES (:product_id, :quantity, NOW(), :created_by)";
        $params = [
            'product_id' => $product_id,
            'quantity' => abs($quantity),
            'created_by' => $this->session->get('user_id')
        ];
        return $this->db->execute($query, $params);
    }
}
?>