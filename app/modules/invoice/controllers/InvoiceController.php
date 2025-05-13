<?php
class InvoiceController extends BaseController {
    private $invoiceModel;
    private $itemModel;
    private $customerModel;
    private $auth;

    public function __construct() {
        parent::__construct();
        $this->invoiceModel = new InvoiceModel();
        $this->itemModel = new InvoiceItemModel();
        $this->customerModel = new CustomerModel();
        $this->auth = new Auth();
    }

    public function list() {
        if (!$this->auth->hasPermission('invoice.view')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('');
        }
        $filters = $_GET ?? [];
        $invoices = $this->invoiceModel->getAllInvoices($filters);
        $customers = $this->customerModel->getAllCustomers();
        $this->view('invoice/invoice_list', [
            'title' => 'Faturalar',
            'invoices' => $invoices,
            'customers' => $customers
        ]);
    }

    public function add() {
        if (!$this->auth->hasPermission('invoice.add')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('invoice/list');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'invoice_no' => $_POST['invoice_no'],
                'type' => $_POST['type'],
                'customer_id' => $_POST['customer_id'],
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
                // Kalemleri kaydet
                if (!empty($_POST['items'])) {
                    foreach ($_POST['items'] as $item) {
                        $item['invoice_id'] = $invoice_id;
                        $this->itemModel->addItem($item);
                        // Stok hareketlerini güncelle
                        $this->itemModel->updateStock($item['product_id'], $item['quantity'], $data['type']);
                    }
                }
                // Cari bakiyesini güncelle
                $this->invoiceModel->updateCustomerBalance($data['customer_id'], $data['net_total'], $data['type'], $data['currency']);
                $this->session->setFlash('success', 'Fatura eklendi.');
                $this->redirect('invoice/list');
            } else {
                $this->session->setFlash('error', 'Fatura ekleme başarısız.');
            }
        }
        $customers = $this->customerModel->getAllCustomers();
        $products = $this->db->query("SELECT * FROM stock_products");
        $this->view('invoice/invoice_add', [
            'title' => 'Fatura Ekle',
            'customers' => $customers,
            'products' => $products
        ]);
    }

    public function edit($id) {
        if (!$this->auth->hasPermission('invoice.edit')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('invoice/list');
        }
        $invoice = $this->invoiceModel->getInvoiceById($id);
        if (!$invoice) {
            $this->session->setFlash('error', 'Fatura bulunamadı.');
            $this->redirect('invoice/list');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'invoice_no' => $_POST['invoice_no'],
                'type' => $_POST['type'],
                'customer_id' => $_POST['customer_id'],
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
            if ($this->invoiceModel->updateInvoice($id, $data)) {
                // Kalemleri güncelle/sil
                if (!empty($_POST['items'])) {
                    foreach ($_POST['items'] as $item) {
                        if (isset($item['id']) && $item['id']) {
                            $this->itemModel->updateItem($item['id'], $item);
                        } else {
                            $item['invoice_id'] = $id;
                            $this->itemModel->addItem($item);
                        }
                        // Stok hareketlerini güncelle
                        $this->itemModel->updateStock($item['product_id'], $item['quantity'], $data['type']);
                    }
                }
                if (!empty($_POST['delete_items'])) {
                    foreach ($_POST['delete_items'] as $item_id) {
                        $this->itemModel->deleteItem($item_id);
                    }
                }
                // Cari bakiyesini güncelle
                $this->invoiceModel->updateCustomerBalance($data['customer_id'], $data['net_total'], $data['type'], $data['currency']);
                $this->session->setFlash('success', 'Fatura güncellendi.');
                $this->redirect('invoice/list');
            } else {
                $this->session->setFlash('error', 'Fatura güncelleme başarısız.');
            }
        }
        $customers = $this->customerModel->getAllCustomers();
        $products = $this->db->query("SELECT * FROM stock_products");
        $items = $this->itemModel->getItemsByInvoice($id);
        $addresses = $this->db->query("SELECT * FROM customer_addresses WHERE customer_id = :customer_id", ['customer_id' => $invoice['customer_id']]);
        $this->view('invoice/invoice_edit', [
            'title' => 'Fatura Düzenle',
            'invoice' => $invoice,
            'customers' => $customers,
            'products' => $products,
            'items' => $items,
            'addresses' => $addresses
        ]);
    }

    public function detail($id) {
        if (!$this->auth->hasPermission('invoice.view')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('invoice/list');
        }
        $invoice = $this->invoiceModel->getInvoiceById($id);
        if (!$invoice) {
            $this->session->setFlash('error', 'Fatura bulunamadı.');
            $this->redirect('invoice/list');
        }
        $items = $this->itemModel->getItemsByInvoice($id);
        $this->view('invoice/invoice_detail', [
            'title' => 'Fatura Detayı',
            'invoice' => $invoice,
            'items' => $items
        ]);
    }
}
?>