<?php
class StockAttributeModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function addAttribute($product_id, $type, $value) {
        $query = "INSERT INTO stock_product_attributes (product_id, attribute_type, attribute_value)
                  VALUES (:product_id, :attribute_type, :attribute_value)";
        $params = [
            'product_id' => $product_id,
            'attribute_type' => $type,
            'attribute_value' => $value
        ];
        return $this->db->execute($query, $params);
    }

    public function getAttributesByProduct($product_id) {
        $query = "SELECT * FROM stock_product_attributes WHERE product_id = :product_id";
        return $this->db->query($query, ['product_id' => $product_id]);
    }

    public function deleteAttributesByProduct($product_id) {
        $query = "DELETE FROM stock_product_attributes WHERE product_id = :product_id";
        return $this->db->execute($query, ['product_id' => $product_id]);
    }
}
?>