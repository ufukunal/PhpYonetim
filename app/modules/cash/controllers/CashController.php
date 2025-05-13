<?php
class CashController extends BaseController {
    private $cashModel;
    private $customerModel;
    private $auth;

    public function __construct() {
        parent::__construct();
        $this->cashModel = new CashModel();
        $this->customerModel = new CustomerModel();
        $this->auth = new Auth();
    }

    public function list() {
        if (!$this->auth->hasPermission('cash.view')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('');
        }
        $filters = $_GET ?? [];
        $cash_registers = $this->cashModel->getAllCashRegisters($filters);
        $this->view('cash/cash_list', [
            'title' => 'Kasalar',
            'cash_registers' => $cash_registers
        ]);
    }

    public function add() {
        if (!$this->auth->hasPermission('cash.add')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('cash/list');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'code' => $_POST['code'],
                'name' => $_POST['name'],
                'currency' => $_POST['currency'],
                'branch_id' => $_POST['branch_id'],
                'balance' => $_POST['balance'],
                'status' => $_POST['status']
            ];
            if ($this->cashModel->addCashRegister($data)) {
                $this->session->setFlash('success', 'Kasa eklendi.');
                $this->redirect('cash/list');
            } else {
                $this->session->setFlash('error', 'Kasa ekleme başarısız.');
            }
        }
        $this->view('cash/cash_add', [
            'title' => 'Kasa Ekle'
        ]);
    }

    public function edit($id) {
        if (!$this->auth->hasPermission('cash.edit')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('cash/list');
        }
        $cash_register = $this->cashModel->getCashRegisterById($id);
        if (!$cash_register) {
            $this->session->setFlash('error', 'Kasa bulunamadı.');
            $this->redirect('cash/list');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'code' => $_POST['code'],
                'name' => $_POST['name'],
                'currency' => $_POST['currency'],
                'branch_id' => $_POST['branch_id'],
                'status' => $_POST['status']
            ];
            if ($this->cashModel->updateCashRegister($id, $data)) {
                $this->session->setFlash('success', 'Kasa güncellendi.');
                $this->redirect('cash/list');
            } else {
                $this->session->setFlash('error', 'Kasa güncelleme başarısız.');
            }
        }
        $this->view('cash/cash_edit', [
            'title' => 'Kasa Düzenle',
            'cash_register' => $cash_register
        ]);
    }

    public function transactions($id) {
        if (!$this->auth->hasPermission('cash.view')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('cash/list');
        }
        $cash_register = $this->cashModel->getCashRegisterById($id);
        if (!$cash_register) {
            $this->session->setFlash('error', 'Kasa bulunamadı.');
            $this->redirect('cash/list');
        }
        $filters = $_GET ?? [];
        $transactions = $this->cashModel->getTransactionsByCashRegister($id, $filters);
        $customers = $this->customerModel->getAllCustomers();
        $this->view('cash/transaction_list', [
            'title' => 'Kasa İşlemleri - ' . sanitize($cash_register['name']),
            'cash_register' => $cash_register,
            'transactions' => $transactions,
            'customers' => $customers
        ]);
    }

    public function addTransaction($cash_register_id) {
        if (!$this->auth->hasPermission('cash.add')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('cash/transactions/' . $cash_register_id);
        }
        $cash_register = $this->cashModel->getCashRegisterById($cash_register_id);
        if (!$cash_register) {
            $this->session->setFlash('error', 'Kasa bulunamadı.');
            $this->redirect('cash/list');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'cash_register_id' => $cash_register_id,
                'type' => $_POST['type'],
                'amount' => $_POST['amount'],
                'currency' => $_POST['currency'],
                'transaction_date' => $_POST['transaction_date'],
                'customer_id' => $_POST['customer_id'],
                'invoice_id' => $_POST['invoice_id'],
                'order_id' => $_POST['order_id'],
                'description' => $_POST['description'],
                'status' => $_POST['status']
            ];
            if ($this->cashModel->addTransaction($data)) {
                if ($data['customer_id'] && in_array($data['type'], ['tahsilat', 'odeme'])) {
                    $this->cashModel->updateCustomerBalance($data['customer_id'], $data['amount'], $data['type'], $data['currency']);
                }
                $this->session->setFlash('success', 'İşlem eklendi.');
                $this->redirect('cash/transactions/' . $cash_register_id);
            } else {
                $this->session->setFlash('error', 'İşlem ekleme başarısız.');
            }
        }
        $customers = $this->customerModel->getAllCustomers();
        $invoices = $this->db->query("SELECT * FROM invoices WHERE status != 'canceled'");
        $orders = $this->db->query("SELECT * FROM orders WHERE status != 'canceled'");
        $cash_registers = $this->cashModel->getAllCashRegisters(['status' => 'active']);
        $this->view('cash/transaction_add', [
            'title' => 'Kasa İşlemi Ekle',
            'cash_register' => $cash_register,
            'customers' => $customers,
            'invoices' => $invoices,
            'orders' => $orders,
            'cash_registers' => $cash_registers
        ]);
    }

    public function editTransaction($id) {
        if (!$this->auth->hasPermission('cash.edit')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('cash/list');
        }
        $transaction = $this->cashModel->getTransactionById($id);
        if (!$transaction) {
            $this->session->setFlash('error', 'İşlem bulunamadı.');
            $this->redirect('cash/list');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'cash_register_id' => $_POST['cash_register_id'],
                'type' => $_POST['type'],
                'amount' => $_POST['amount'],
                'currency' => $_POST['currency'],
                'transaction_date' => $_POST['transaction_date'],
                'customer_id' => $_POST['customer_id'],
                'invoice_id' => $_POST['invoice_id'],
                'order_id' => $_POST['order_id'],
                'description' => $_POST['description'],
                'status' => $_POST['status']
            ];
            if ($this->cashModel->updateTransaction($id, $data)) {
                if ($data['customer_id'] && in_array($data['type'], ['tahsilat', 'odeme'])) {
                    $this->cashModel->updateCustomerBalance($data['customer_id'], $data['amount'], $data['type'], $data['currency']);
                }
                $this->session->setFlash('success', 'İşlem güncellendi.');
                $this->redirect('cash/transactions/' . $data['cash_register_id']);
            } else {
                $this->session->setFlash('error', 'İşlem güncelleme başarısız.');
            }
        }
        $cash_registers = $this->cashModel->getAllCashRegisters();
        $customers = $this->customerModel->getAllCustomers();
        $invoices = $this->db->query("SELECT * FROM invoices WHERE status != 'canceled'");
        $orders = $this->db->query("SELECT * FROM orders WHERE status != 'canceled'");
        $this->view('cash/transaction_edit', [
            'title' => 'Kasa İşlemi Düzenle',
            'transaction' => $transaction,
            'cash_registers' => $cash_registers,
            'customers' => $customers,
            'invoices' => $invoices,
            'orders' => $orders
        ]);
    }

    public function controlTransaction($id, $status) {
        if (!$this->auth->hasPermission('cash.control')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('cash/list');
        }
        $transaction = $this->cashModel->getTransactionById($id);
        if (!$transaction) {
            $this->session->setFlash('error', 'İşlem bulunamadı.');
            $this->redirect('cash/list');
        }
        if ($this->cashModel->controlTransaction($id, $status)) {
            $this->session->setFlash('success', 'İşlem ' . ($status == 'controlled' ? 'kontrol edildi' : 'reddedildi') . '.');
            $this->redirect('cash/transactions/' . $transaction['cash_register_id']);
        } else {
            $this->session->setFlash('error', 'İşlem kontrol başarısız.');
            $this->redirect('cash/transactions/' . $transaction['cash_register_id']);
        }
    }
}
?>