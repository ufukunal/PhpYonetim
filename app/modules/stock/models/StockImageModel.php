<?php
class StockImageModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function addImage($product_id, $image_path) {
        $query = "INSERT INTO stock_product_images (product_id, image_path)
                  VALUES (:product_id, :image_path)";
        $params = [
            'product_id' => $product_id,
            'image_path' => $image_path
        ];
        return $this->db->execute($query, $params);
    }

    public function getImagesByProduct($product_id) {
        $query = "SELECT * FROM stock_product_images WHERE product_id = :product_id";
        return $this->db->query($query, ['product_id' => $product_id]);
    }

    public function deleteImage($id) {
        $query = "DELETE FROM stock_product_images WHERE id = :id";
        return $this->db->execute($query, ['id' => $id]);
    }

    public function deleteImagesByProduct($product_id) {
        $query = "DELETE FROM stock_product_images WHERE product_id = :product_id";
        return $this->db->execute($query, ['product_id' => $product_id]);
    }
}
?>