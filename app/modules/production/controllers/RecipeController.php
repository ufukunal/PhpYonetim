<?php
class RecipeController extends BaseController {
    private $recipeModel;
    private $auth;

    public function __construct() {
        parent::__construct();
        $this->recipeModel = new RecipeModel();
        $this->auth = new Auth();
    }

    public function list() {
        if (!$this->auth->hasPermission('production.view')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('');
        }
        $filters = $_GET ?? [];
        $recipes = $this->recipeModel->getAllRecipes($filters);
        $this->view('production/recipe_list', [
            'title' => 'Reçeteler',
            'recipes' => $recipes
        ]);
    }

    public function add() {
        if (!$this->auth->hasPermission('production.add')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('production/recipe_list');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'code' => $_POST['code'],
                'product_id' => $_POST['product_id'],
                'description' => $_POST['description'],
                'status' => $_POST['status'],
                'ingredients' => []
            ];
            foreach ($_POST['ingredients'] as $ingredient) {
                $data['ingredients'][] = [
                    'ingredient_id' => $ingredient['ingredient_id'],
                    'quantity' => $ingredient['quantity'],
                    'unit' => $ingredient['unit']
                ];
            }
            if ($this->recipeModel->addRecipe($data)) {
                $this->session->setFlash('success', 'Reçete eklendi.');
                $this->redirect('production/recipe_list');
            } else {
                $this->session->setFlash('error', 'Reçete ekleme başarısız.');
            }
        }
        $products = $this->db->query("SELECT * FROM stock_products");
        $this->view('production/recipe_add', [
            'title' => 'Reçete Ekle',
            'products' => $products
        ]);
    }

    public function edit($id) {
        if (!$this->auth->hasPermission('production.edit')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('production/recipe_list');
        }
        $recipe = $this->recipeModel->getRecipeById($id);
        if (!$recipe) {
            $this->session->setFlash('error', 'Reçete bulunamadı.');
            $this->redirect('production/recipe_list');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'code' => $_POST['code'],
                'product_id' => $_POST['product_id'],
                'description' => $_POST['description'],
                'status' => $_POST['status'],
                'ingredients' => []
            ];
            foreach ($_POST['ingredients'] as $ingredient) {
                $data['ingredients'][] = [
                    'ingredient_id' => $ingredient['ingredient_id'],
                    'quantity' => $ingredient['quantity'],
                    'unit' => $ingredient['unit']
                ];
            }
            if ($this->recipeModel->updateRecipe($id, $data)) {
                $this->session->setFlash('success', 'Reçete güncellendi.');
                $this->redirect('production/recipe_list');
            } else {
                $this->session->setFlash('error', 'Reçete güncelleme başarısız.');
            }
        }
        $products = $this->db->query("SELECT * FROM stock_products");
        $this->view('production/recipe_edit', [
            'title' => 'Reçete Düzenle',
            'recipe' => $recipe,
            'products' => $products
        ]);
    }
}
?>