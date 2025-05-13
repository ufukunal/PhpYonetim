<?php
class OrderController extends BaseController {
    private $orderModel;
    private $itemModel;
    private $customerModel;
    private $invoiceModel;
    private $auth;

    public function __construct() {
        parent::__construct();
        $this->orderModel = new OrderModel();
        $this->itemModel = new OrderItemModel();
        $this->customerModel = new CustomerModel();
        $this->invoiceModel = new InvoiceModel();
        $this->auth = new Auth();
    }

    public function list() {
        if (!$this->auth->hasPermission('order.view')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('');
        }
        $filters = $_GET ?? [];
        $orders = $this->orderModel->getAllOrders($filters);
        $customers = $this->customerModel->getAllCustomers();
        $this->view('order/order_list', [
            'title' => 'Siparişler',
            'orders' => $orders,
            'customers' => $customers
        ]);
    }

    public function add() {
        if (!$this->auth->hasPermission('order.add')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('order/list');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'order_no' => $_POST['order_no'],
                'customer_id' => $_POST['customer_id'],
                'order_date' => $_POST['order_date'],
                'delivery_date' => $_POST['delivery_date'],
                'total_amount' => $_POST['total_amount'],
                'status' => $_POST['status'],
                'description' => $_POST['description']
            ];
            if ($this->orderModel->addOrder($data)) {
                $order_id = $this->db->getConnection()->lastInsertId();
                // Kalemleri kaydet ve stok rezervasyonu yap
                if (!empty($_POST['items'])) {
                    foreach ($_POST['items'] as $item) {
                        $item['order_id'] = $order_id;
                        if ($this->itemModel->reserveStock($item['product_id'], $item['quantity'])) {
                            $this->itemModel->addItem($item);
                        } else {
                            $this->session->setFlash('error', 'Stok yetersiz: ' . $item['product_id']);
                            $this->redirect('order/add');
                        }
                    }
                }
                $this->session->setFlash('success', 'Sipariş eklendi.');
                $this->redirect('order/list');
            } else {
                $this->session->setFlash('error', 'Sipariş ekleme başarısız.');
            }
        }
        $customers = $this->customerModel->getAllCustomers();
        $products = $this->db->query("SELECT * FROM stock_products");
        $this->view('order/order_add', [
            'title' => 'Sipariş Ekle',
            'customers' => $customers,
            'products' => $products
        ]);
    }

    public function edit($id) {
        if (!$this->auth->hasPermission('order.edit')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('order/list');
        }
        $order = $this->orderModel->getOrderById($id);
        if (!$order) {
            $this->session->setFlash('error', 'Sipariş bulunamadı.');
            $this->redirect('order/list');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'order_no' => $_POST['order_no'],
                'customer_id' => $_POST['customer_id'],
                'order_date' => $_POST['order_date'],
                'delivery_date' => $_POST['delivery_date'],
                'total_amount' => $_POST['total_amount'],
                'status' => $_POST['status'],
                'description' => $_POST['description']
            ];
            if ($this->orderModel->updateOrder($id, $data)) {
                // Kalemleri güncelle/sil
                if (!empty($_POST['items'])) {
                    foreach ($_POST['items'] as $item) {
                        if (isset($item['id']) && $item['id']) {
                            $existing_item = $this->db->queryOne("SELECT quantity FROM order_items WHERE id = :id", ['id' => $item['id']]);
                            $quantity_diff = $item['quantity'] - $existing_item['quantity'];
                            if ($quantity_diff != 0) {
                                if ($quantity_diff > 0) {
                                    $this->itemModel->reserveStock($item['product_id'], $quantity_diff);
                                } else {
                                    $this->itemModel->releaseStock($item['product_id'], abs($quantity_diff));
                                }
                            }
                            $this->itemModel->updateItem($item['id'], $item);
                        } else {
                            $item['order_id'] = $id;
                            if ($this->itemModel->reserveStock($item['product_id'], $item['quantity'])) {
                                $this->itemModel->addItem($item);
                            } else {
                                $this->session->setFlash('error', 'Stok yetersiz: ' . $item['product_id']);
                                $this->redirect('order/edit/' . $id);
                            }
                        }
                    }
                }
                if (!empty($_POST['delete_items'])) {
                    foreach ($_POST['delete_items'] as $item_id) {
                        $item = $this->db->queryOne("SELECT product_id, quantity FROM order_items WHERE id = :id", ['id' => $item_id]);
                        $this->itemModel->releaseStock($item['product_id'], $item['quantity']);
                        $this->itemModel->deleteItem($item_id);
                    }
                }
                $this->session->setFlash('success', 'Sipariş güncellendi.');
                $this->redirect('order/list');
            } else {
                $this->session->setFlash('error', 'Sipariş güncelleme başarısız.');
            }
        }
        $customers = $this->customerModel->getAllCustomers();
        $products = $this->db->query("SELECT * FROM stock_products");
        $items = $this->itemModel->getItemsByOrder($id);
        $this->view('order/order_edit', [
            'title' => 'Sipariş Düzenle',
            'order' => $order,
            'customers' => $customers,
            'products' => $products,
            'items' => $items
        ]);
    }

    public function detail($id) {
        if (!$this->auth->hasPermission('order.view')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('order/list');
        }
        $order = $this->orderModel->getOrderById($id);
        if (!$order) {
            $this->session->setFlash('error', 'Sipariş bulunamadı.');
            $this->redirect('order/list');
        }
        $items = $this->itemModel->getItemsByOrder($id);
        $invoices = $this->db->query("SELECT i.* FROM invoices i JOIN order_items oi ON i.id = oi.invoice_id WHERE oi.order_id = :order_id GROUP BY i.id", ['order_id' => $id]);
        $this->view('order/order_detail', [
            'title' => 'Sipariş Detayı',
            'order' => $order,
            'items' => $items,
            'invoices' => $invoices
        ]);
    }

    public function invoice($id) {
        if (!$this->auth->hasPermission('order.invoice')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('order/list');
        }
        $order = $this->orderModel->getOrderById($id);
        if (!$order) {
            $this->session->setFlash('error', 'Sipariş bulunamadı.');
            $this->redirect('order/list');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'invoice_no' => $_POST['invoice_no'],
                'type' => $_POST['type'],
                'customer_id' => $order['customer_id'],
                'invoice_date' => $_POST['invoice_date'],
                'due_date' => $_POST['due_date'],
                'currency' => $_POST['currency'],
                'subtotal' => $_POST['subtotal'],
                'tax_total' => $_POST['tax_total'],
                'discount_total' => $_POST['discount_total'],
                'net_total' => $_POST['net_total'],
                'invoice_address_id' => $_POST['invoice_address_id'],
                'delivery_address_id' => $_POST['delivery_address_id'],
                'description' => $_POST['description'],
                'status' => $_POST['status']
            ];
            if ($this->invoiceModel->addInvoice($data)) {
                $invoice_id = $this->db->getConnection()->lastInsertId();
                // Kalemleri kaydet ve stok çıkışı yap
                if (!empty($_POST['items'])) {
                    foreach ($_POST['items'] as $item) {
                        $item['invoice_id'] = $invoice_id;
                        $this->invoiceModel->addItem($item);
                        $this->itemModel->updateStockExit($item['product_id'], $item['quantity']);
                        $this->db->execute("UPDATE order_items SET invoiced = TRUE, invoice_id = :invoice_id WHERE id = :item_id", [
                            'invoice_id' => $invoice_id,
                            'item_id' => $item['order_item_id']
                        ]);
                    }
                }
                $this->invoiceModel->updateCustomerBalance($data['customer_id'], $data['net_total'], $data['type'], $data['currency']);
                $this->session->setFlash('success', 'Fatura oluşturuldu.');
                $this->redirect('order/detail/' . $id);
            } else {
                $this->session->setFlash('error', 'Fatura oluşturma başarısız.');
            }
        }
        $items = $this->itemModel->getItemsByOrder($id);
        $addresses = $this->db->query("SELECT * FROM customer_addresses WHERE customer_id = :customer_id", ['customer_id' => $order['customer_id']]);
        $this->view('order/order_detail', [
            'title' => 'Fatura Oluştur',
            'order' => $order,
            'items' => $items,
            'addresses' => $addresses,
            'invoice_form' => true
        ]);
    }

    public function openProducts() {
        if (!$this->auth->hasPermission('order.open_products')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('order/list');
        }
        $filters = $_GET ?? [];
        $products = $this->orderModel->getOpenOrderProducts($filters);
        $customers = $this->customerModel->getAllCustomers();
        $this->view('order/open_products', [
            'title' => 'Açık Sipariş Ürünleri',
            'products' => $products,
            'customers' => $customers
        ]);
    }
}
?>