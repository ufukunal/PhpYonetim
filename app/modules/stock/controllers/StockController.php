<?php
class StockController extends BaseController {
    private $stockModel;
    private $groupModel;
    private $attributeModel;
    private $imageModel;
    private $auth;

    public function __construct() {
        parent::__construct();
        $this->stockModel = new StockModel();
        $this->groupModel = new StockGroupModel();
        $this->attributeModel = new StockAttributeModel();
        $this->imageModel = new StockImageModel();
        $this->auth = new Auth();
    }

    public function list() {
        if (!$this->auth->hasPermission('stock.view')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('');
        }
        $filters = $_GET ?? [];
        $products = $this->stockModel->getAllProducts($filters);
        $groups = $this->groupModel->getAllGroups();
        $this->view('stock/product_list', [
            'title' => 'Ürünler',
            'products' => $products,
            'groups' => $groups
        ]);
    }

    public function add() {
        if (!$this->auth->hasPermission('stock.edit')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('stock/list');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'code' => $_POST['code'],
                'name' => $_POST['name'],
                'unit' => $_POST['unit'],
                'quantity' => $_POST['quantity'],
                'min_quantity' => $_POST['min_quantity'],
                'description' => $_POST['description'],
                'stock_group_id' => $_POST['stock_group_id'],
                'sub_group_id' => $_POST['sub_group_id'],
                'sub_sub_group_id' => $_POST['sub_sub_group_id']
            ];
            if ($this->stockModel->addProduct($data)) {
                $product_id = $this->db->getConnection()->lastInsertId();
                // Özellikleri kaydet
                if (!empty($_POST['colors'])) {
                    foreach ($_POST['colors'] as $color) {
                        $this->attributeModel->addAttribute($product_id, 'color', $color);
                    }
                }
                if (!empty($_POST['size_length'])) {
                    $size = $_POST['size_length'] . 'x' . $_POST['size_width'] . 'x' . $_POST['size_height'];
                    $this->attributeModel->addAttribute($product_id, 'size', $size);
                }
                if (!empty($_POST['weight'])) {
                    $this->attributeModel->addAttribute($product_id, 'weight', $_POST['weight']);
                }
                // Resimleri kaydet
                if (!empty($_FILES['images']['name'][0])) {
                    foreach ($_FILES['images']['name'] as $key => $name) {
                        if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                            $ext = pathinfo($name, PATHINFO_EXTENSION);
                            $filename = 'product_' . $product_id . '_' . time() . '_' . $key . '.' . $ext;
                            $destination = BASE_PATH . '/public/assets/uploads/products/' . $filename;
                            if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $destination)) {
                                $this->imageModel->addImage($product_id, $filename);
                            }
                        }
                    }
                }
                $this->session->setFlash('success', 'Ürün eklendi.');
                $this->redirect('stock/list');
            } else {
                $this->session->setFlash('error', 'Ürün ekleme başarısız.');
            }
        }
        $groups = $this->groupModel->getAllGroups();
        $this->view('stock/product_add', [
            'title' => 'Ürün Ekle',
            'groups' => $groups
        ]);
    }

    public function edit($id) {
        if (!$this->auth->hasPermission('stock.edit')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('stock/list');
        }
        $product = $this->stockModel->getProductById($id);
        if (!$product) {
            $this->session->setFlash('error', 'Ürün bulunamadı.');
            $this->redirect('stock/list');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'code' => $_POST['code'],
                'name' => $_POST['name'],
                'unit' => $_POST['unit'],
                'quantity' => $_POST['quantity'],
                'min_quantity' => $_POST['min_quantity'],
                'description' => $_POST['description'],
                'stock_group_id' => $_POST['stock_group_id'],
                'sub_group_id' => $_POST['sub_group_id'],
                'sub_sub_group_id' => $_POST['sub_sub_group_id']
            ];
            if ($this->stockModel->updateProduct($id, $data)) {
                // Eski özellikleri sil
                $this->attributeModel->deleteAttributesByProduct($id);
                // Yeni özellikleri kaydet
                if (!empty($_POST['colors'])) {
                    foreach ($_POST['colors'] as $color) {
                        $this->attributeModel->addAttribute($id, 'color', $color);
                    }
                }
                if (!empty($_POST['size_length'])) {
                    $size = $_POST['size_length'] . 'x' . $_POST['size_width'] . 'x' . $_POST['size_height'];
                    $this->attributeModel->addAttribute($id, 'size', $size);
                }
                if (!empty($_POST['weight'])) {
                    $this->attributeModel->addAttribute($id, 'weight', $_POST['weight']);
                }
                // Yeni resimleri kaydet
                if (!empty($_FILES['images']['name'][0])) {
                    foreach ($_FILES['images']['name'] as $key => $name) {
                        if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                            $ext = pathinfo($name, PATHINFO_EXTENSION);
                            $filename = 'product_' . $id . '_' . time() . '_' . $key . '.' . $ext;
                            $destination = BASE_PATH . '/public/assets/uploads/products/' . $filename;
                            if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $destination)) {
                                $this->imageModel->addImage($id, $filename);
                            }
                        }
                    }
                }
                $this->session->setFlash('success', 'Ürün güncellendi.');
                $this->redirect('stock/list');
            } else {
                $this->session->setFlash('error', 'Ürün güncelleme başarısız.');
            }
        }
        $groups = $this->groupModel->getAllGroups();
        $attributes = $this->attributeModel->getAttributesByProduct($id);
        $images = $this->imageModel->getImagesByProduct($id);
        $this->view('stock/product_edit', [
            'title' => 'Ürün Düzenle',
            'product' => $product,
            'groups' => $groups,
            'attributes' => $attributes,
            'images' => $images
        ]);
    }

    public function detail($id) {
        if (!$this->auth->hasPermission('stock.view')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('stock/list');
        }
        $product = $this->stockModel->getProductById($id);
        if (!$product) {
            $this->session->setFlash('error', 'Ürün bulunamadı.');
            $this->redirect('stock/list');
        }
        $attributes = $this->attributeModel->getAttributesByProduct($id);
        $images = $this->imageModel->getImagesByProduct($id);
        $entries = $this->stockModel->getEntries($id);
        $exits = $this->stockModel->getExits($id);
        $this->view('stock/product_detail', [
            'title' => 'Ürün Detayı',
            'product' => $product,
            'attributes' => $attributes,
            'images' => $images,
            'entries' => $entries,
            'exits' => $exits
        ]);
    }

    public function entryList() {
        if (!$this->auth->hasPermission('stock.view')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('stock/list');
        }
        $entries = $this->stockModel->getEntries();
        $this->view('stock/entry_list', [
            'title' => 'Stok Girişleri',
            'entries' => $entries
        ]);
    }

    public function entryAdd() {
        if (!$this->auth->hasPermission('stock.entry')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('stock/entryList');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'product_id' => $_POST['product_id'],
                'quantity' => $_POST['quantity'],
                'entry_date' => $_POST['entry_date'],
                'invoice_no' => $_POST['invoice_no']
            ];
            if ($this->stockModel->addEntry($data)) {
                $this->session->setFlash('success', 'Stok girişi eklendi.');
                $this->redirect('stock/entryList');
            } else {
                $this->session->setFlash('error', 'Stok girişi ekleme başarısız.');
            }
        }
        $products = $this->stockModel->getAllProducts();
        $this->view('stock/entry_add', [
            'title' => 'Stok Girişi Ekle',
            'products' => $products
        ]);
    }

    public function exitList() {
        if (!$this->auth->hasPermission('stock.view')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('stock/list');
        }
        $exits = $this->stockModel->getExits();
        $this->view('stock/exit_list', [
            'title' => 'Stok Çıkışları',
            'exits' => $exits
        ]);
    }

    public function exitAdd() {
        if (!$this->auth->hasPermission('stock.exit')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('stock/exitList');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'product_id' => $_POST['product_id'],
                'quantity' => $_POST['quantity'],
                'exit_date' => $_POST['exit_date'],
                'reason' => $_POST['reason']
            ];
            if ($this->stockModel->addExit($data)) {
                $this->session->setFlash('success', 'Stok çıkışı eklendi.');
                $this->redirect('stock/exitList');
            } else {
                $this->session->setFlash('error', 'Stok çıkışı ekleme başarısız.');
            }
        }
        $products = $this->stockModel->getAllProducts();
        $this->view('stock/exit_add', [
            'title' => 'Stok Çıkışı Ekle',
            'products' => $products
        ]);
    }

    public function inventoryCount() {
        if (!$this->auth->hasPermission('stock.edit')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('stock/list');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'product_id' => $_POST['product_id'],
                'quantity' => $_POST['quantity']
            ];
            if ($this->stockModel->addInventoryCount($data)) {
                $this->session->setFlash('success', 'Envanter sayımı kaydedildi.');
                $this->redirect('stock/list');
            } else {
                $this->session->setFlash('error', 'Envanter sayımı başarısız.');
            }
        }
        $products = $this->stockModel->getAllProducts();
        $this->view('stock/inventory_count', [
            'title' => 'Envanter Sayımı',
            'products' => $products
        ]);
    }
}
?>