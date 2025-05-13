<?php
class RecipeModel {
    private $db;
    private $session;

    public function __construct() {
        $this->db = new Database();
        $this->session = new Session();
    }

    public function addRecipe($data) {
        $query = "INSERT INTO recipes (code, product_id, description, status, created_by)
                  VALUES (:code, :product_id, :description, :status, :created_by)";
        $params = [
            'code' => $data['code'],
            'product_id' => $data['product_id'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'active',
            'created_by' => $this->session->get('user_id')
        ];
        if ($this->db->execute($query, $params)) {
            $recipe_id = $this->db->lastInsertId();
            foreach ($data['ingredients'] as $ingredient) {
                $this->addIngredient($recipe_id, $ingredient);
            }
            return true;
        }
        return false;
    }

    public function addIngredient($recipe_id, $ingredient) {
        $query = "INSERT INTO recipe_ingredients (recipe_id, ingredient_id, quantity, unit)
                  VALUES (:recipe_id, :ingredient_id, :quantity, :unit)";
        $params = [
            'recipe_id' => $recipe_id,
            'ingredient_id' => $ingredient['ingredient_id'],
            'quantity' => $ingredient['quantity'],
            'unit' => $ingredient['unit']
        ];
        return $this->db->execute($query, $params);
    }

    public function updateRecipe($id, $data) {
        $query = "UPDATE recipes SET
                  code = :code, product_id = :product_id, description = :description,
                  status = :status, updated_by = :updated_by, updated_at = NOW()
                  WHERE id = :id";
        $params = [
            'id' => $id,
            'code' => $data['code'],
            'product_id' => $data['product_id'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'active',
            'updated_by' => $this->session->get('user_id')
        ];
        if ($this->db->execute($query, $params)) {
            // Eski bileşenleri sil
            $this->db->execute("DELETE FROM recipe_ingredients WHERE recipe_id = :recipe_id", ['recipe_id' => $id]);
            // Yeni bileşenleri ekle
            foreach ($data['ingredients'] as $ingredient) {
                $this->addIngredient($id, $ingredient);
            }
            return true;
        }
        return false;
    }

    public function deleteRecipe($id) {
        $query = "DELETE FROM recipes WHERE id = :id AND NOT EXISTS (SELECT 1 FROM production_orders WHERE recipe_id = :id)";
        return $this->db->execute($query, ['id' => $id]);
    }

    public function getRecipeById($id) {
        $query = "SELECT r.*, sp.name AS product_name
                  FROM recipes r
                  JOIN stock_products sp ON r.product_id = sp.id
                  WHERE r.id = :id";
        $recipe = $this->db->queryOne($query, ['id' => $id]);
        if ($recipe) {
            $recipe['ingredients'] = $this->getIngredientsByRecipeId($id);
        }
        return $recipe;
    }

    public function getIngredientsByRecipeId($recipe_id) {
        $query = "SELECT ri.*, sp.name AS ingredient_name, sp.unit AS ingredient_unit
                  FROM recipe_ingredients ri
                  JOIN stock_products sp ON ri.ingredient_id = sp.id
                  WHERE ri.recipe_id = :recipe_id";
        return $this->db->query($query, ['recipe_id' => $recipe_id]);
    }

    public function getAllRecipes($filters = []) {
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

        return $this->db->query($query, $params);
    }
}
?>