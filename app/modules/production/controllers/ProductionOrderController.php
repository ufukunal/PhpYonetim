<?php
class ProductionOrderController extends BaseController {
    private $productionOrderModel;
    private $recipeModel;
    private $auth;

    public function __construct() {
        parent::__construct();
        $this->productionOrderModel = new ProductionOrderModel();
        $this->recipeModel = new RecipeModel();
        $this->auth = new Auth();
    }

    public function list() {
        if (!$this->auth->hasPermission('production.view')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('');
        }
        $filters = $_GET ?? [];
        $orders = $this->productionOrderModel->getAllProductionOrders($filters);
        $this->view('production/order_list', [
            'title' => 'Üretim Emirleri',
            'orders' => $orders
        ]);
    }

    public function create() {
        if (!$this->auth->hasPermission('production.add')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('production/order_list');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'recipe_id' => $_POST['recipe_id'],
                'quantity' => $_POST['quantity'],
                'planned_date' => $_POST['planned_date'],
                'tracking' => $_POST['tracking']
            ];
            $result = $this->productionOrderModel->addProductionOrder($data);
            if ($result['success']) {
                $this->session->setFlash('success', 'Üretim emri oluşturuldu.');
                $this->redirect('production/order_list');
            } else {
                $this->session->setFlash('error', $result['message']);
            }
        }
        $recipes = $this->recipeModel->getAllRecipes(['status' => 'active']);
        $this->view('production/order_create', [
            'title' => 'Üretim Emri Oluştur',
            'recipes' => $recipes
        ]);
    }

    public function tracking($order_id) {
        if (!$this->auth->hasPermission('production.view')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('production/order_list');
        }
        $order = $this->productionOrderModel->getProductionOrderById($order_id);
        if (!$order) {
            $this->session->setFlash('error', 'Üretim emri bulunamadı.');
            $this->redirect('production/order_list');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tracking_id = $_POST['tracking_id'];
            $status = $_POST['status'];
            if ($this->productionOrderModel->updateTrackingStep($tracking_id, $status)) {
                $this->session->setFlash('success', 'Takip adımı güncellendi.');
            } else {
                $this->session->setFlash('error', 'Takip adımı güncelleme başarısız.');
            }
            $this->redirect('production/tracking/' . $order_id);
        }
        $tracking_steps = $this->productionOrderModel->getTrackingByOrderId($order_id);
        $this->view('production/order_tracking', [
            'title' => 'Üretim Takibi - ' . sanitize($order['order_number']),
            'order' => $order,
            'tracking_steps' => $tracking_steps
        ]);
    }
}
?>