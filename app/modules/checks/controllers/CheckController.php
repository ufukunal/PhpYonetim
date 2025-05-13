<?php
class CheckController extends BaseController {
    private $checkModel;
    private $customerModel;
    private $bankModel;
    private $cashModel;
    private $auth;

    public function __construct() {
        parent::__construct();
        $this->checkModel = new CheckModel();
        $this->customerModel = new CustomerModel();
        $this->bankModel = new BankModel();
        $this->cashModel = new CashModel();
        $this->auth = new Auth();
    }

    public function list() {
        if (!$this->auth->hasPermission('check.view')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('');
        }
        $filters = $_GET ?? [];
        $check_notes = $this->checkModel->getAllCheckNotes($filters);
        $customers = $this->customerModel->getAllCustomers();
        $this->view('check/check_list', [
            'title' => 'Çek/Senet Listesi',
            'check_notes' => $check_notes,
            'customers' => $customers
        ]);
    }

    public function add() {
        if (!$this->auth->hasPermission('check.add')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('check/list');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'type' => $_POST['type'],
                'document_number' => $_POST['document_number'],
                'customer_id' => $_POST['customer_id'],
                'issue_date' => $_POST['issue_date'],
                'due_date' => $_POST['due_date'],
                'amount' => $_POST['amount'],
                'currency' => $_POST['currency'],
                'bank_id' => $_POST['bank_id'],
                'check_number' => $_POST['check_number'],
                'serial_number' => $_POST['serial_number'],
                'invoice_id' => $_POST['invoice_id'],
                'order_id' => $_POST['order_id'],
                'description' => $_POST['description'],
                'status' => $_POST['status']
            ];
            if ($this->checkModel->addCheckNote($data)) {
                $this->session->setFlash('success', ucfirst($data['type']) . ' eklendi.');
                $this->redirect('check/list');
            } else {
                $this->session->setFlash('error', ucfirst($data['type']) . ' ekleme başarısız.');
            }
        }
        $customers = $this->customerModel->getAllCustomers();
        $banks = $this->bankModel->getAllBankAccounts(['status' => 'active']);
        $invoices = $this->db->query("SELECT * FROM invoices WHERE status != 'canceled'");
        $orders = $this->db->query("SELECT * FROM orders WHERE status != 'canceled'");
        $this->view('check/check_add', [
            'title' => 'Çek/Senet Ekle',
            'customers' => $customers,
            'banks' => $banks,
            'invoices' => $invoices,
            'orders' => $orders
        ]);
    }

    public function edit($id) {
        if (!$this->auth->hasPermission('check.edit')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('check/list');
        }
        $check_note = $this->checkModel->getCheckNoteById($id);
        if (!$check_note) {
            $this->session->setFlash('error', 'Çek/Senet bulunamadı.');
            $this->redirect('check/list');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'type' => $_POST['type'],
                'document_number' => $_POST['document_number'],
                'customer_id' => $_POST['customer_id'],
                'issue_date' => $_POST['issue_date'],
                'due_date' => $_POST['due_date'],
                'amount' => $_POST['amount'],
                'currency' => $_POST['currency'],
                'bank_id' => $_POST['bank_id'],
                'check_number' => $_POST['check_number'],
                'serial_number' => $_POST['serial_number'],
                'invoice_id' => $_POST['invoice_id'],
                'order_id' => $_POST['order_id'],
                'description' => $_POST['description'],
                'status' => $_POST['status']
            ];
            if ($this->checkModel->updateCheckNote($id, $data)) {
                $this->session->setFlash('success', ucfirst($data['type']) . ' güncellendi.');
                $this->redirect('check/list');
            } else {
                $this->session->setFlash('error', ucfirst($data['type']) . ' güncelleme başarısız.');
            }
        }
        $customers = $this->customerModel->getAllCustomers();
        $banks = $this->bankModel->getAllBankAccounts(['status' => 'active']);
        $invoices = $this->db->query("SELECT * FROM invoices WHERE status != 'canceled'");
        $orders = $this->db->query("SELECT * FROM orders WHERE status != 'canceled'");
        $this->view('check/check_edit', [
            'title' => 'Çek/Senet Düzenle',
            'check_note' => $check_note,
            'customers' => $customers,
            'banks' => $banks,
            'invoices' => $invoices,
            'orders' => $orders
        ]);
    }

    public function transactions($id) {
        if (!$this->auth->hasPermission('check.view')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('check/list');
        }
        $check_note = $this->checkModel->getCheckNoteById($id);
        if (!$check_note) {
            $this->session->setFlash('error', 'Çek/Senet bulunamadı.');
            $this->redirect('check/list');
        }
        $filters = $_GET ?? [];
        $transactions = $this->checkModel->getTransactionsByCheckNote($id, $filters);
        $this->view('check/transaction_list', [
            'title' => ucfirst($check_note['type']) . ' İşlemleri - ' . sanitize($check_note['document_number']),
            'check_note' => $check_note,
            'transactions' => $transactions
        ]);
    }

    public function addTransaction($check_note_id) {
        if (!$this->auth->hasPermission('check.add')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('check/transactions/' . $check_note_id);
        }
        $check_note = $this->checkModel->getCheckNoteById($check_note_id);
        if (!$check_note) {
            $this->session->setFlash('error', 'Çek/Senet bulunamadı.');
            $this->redirect('check/list');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'check_note_id' => $check_note_id,
                'type' => $_POST['type'],
                'amount' => $_POST['amount'],
                'currency' => $_POST['currency'],
                'transaction_date' => $_POST['transaction_date'],
                'method' => $_POST['method'],
                'bank_account_id' => $_POST['bank_account_id'],
                'cash_register_id' => $_POST['cash_register_id'],
                'description' => $_POST['description'],
                'status' => $_POST['status']
            ];
            if ($this->checkModel->addTransaction($data)) {
                $this->session->setFlash('success', ucfirst($data['type'] == 'collection' ? 'Tahsilat' : 'Ödeme') . ' eklendi.');
                $this->redirect('check/transactions/' . $check_note_id);
            } else {
                $this->session->setFlash('error', ucfirst($data['type'] == 'collection' ? 'Tahsilat' : 'Ödeme') . ' ekleme başarısız.');
            }
        }
        $banks = $this->bankModel->getAllBankAccounts(['status' => 'active']);
        $cash_registers = $this->cashModel->getAllCashRegisters(['status' => 'active']);
        $this->view('check/transaction_add', [
            'title' => ucfirst($check_note['type']) . ' İşlemi Ekle',
            'check_note' => $check_note,
            'banks' => $banks,
            'cash_registers' => $cash_registers
        ]);
    }

    public function editTransaction($id) {
        if (!$this->auth->hasPermission('check.edit')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('check/list');
        }
        $transaction = $this->checkModel->getTransactionById($id);
        if (!$transaction) {
            $this->session->setFlash('error', 'İşlem bulunamadı.');
            $this->redirect('check/list');
        }
        $check_note = $this->checkModel->getCheckNoteById($transaction['check_note_id']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'check_note_id' => $transaction['check_note_id'],
                'type' => $_POST['type'],
                'amount' => $_POST['amount'],
                'currency' => $_POST['currency'],
                'transaction_date' => $_POST['transaction_date'],
                'method' => $_POST['method'],
                'bank_account_id' => $_POST['bank_account_id'],
                'cash_register_id' => $_POST['cash_register_id'],
                'description' => $_POST['description'],
                'status' => $_POST['status']
            ];
            if ($this->checkModel->updateTransaction($id, $data)) {
                $this->session->setFlash('success', ucfirst($data['type'] == 'collection' ? 'Tahsilat' : 'Ödeme') . ' güncellendi.');
                $this->redirect('check/transactions/' . $transaction['check_note_id']);
            } else {
                $this->session->setFlash('error', ucfirst($data['type'] == 'collection' ? 'Tahsilat' : 'Ödeme') . ' güncelleme başarısız.');
            }
        }
        $banks = $this->bankModel->getAllBankAccounts(['status' => 'active']);
        $cash_registers = $this->cashModel->getAllCashRegisters(['status' => 'active']);
        $this->view('check/transaction_edit', [
            'title' => ucfirst($check_note['type']) . ' İşlemi Düzenle',
            'transaction' => $transaction,
            'check_note' => $check_note,
            'banks' => $banks,
            'cash_registers' => $cash_registers
        ]);
    }

    public function controlTransaction($id, $status) {
        if (!$this->auth->hasPermission('check.control')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('check/list');
        }
        $transaction = $this->checkModel->getTransactionById($id);
        if (!$transaction) {
            $this->session->setFlash('error', 'İşlem bulunamadı.');
            $this->redirect('check/list');
        }
        if ($this->checkModel->controlTransaction($id, $status)) {
            $this->session->setFlash('success', 'İşlem ' . ($status == 'controlled' ? 'kontrol edildi' : 'reddedildi') . '.');
            $this->redirect('check/transactions/' . $transaction['check_note_id']);
        } else {
            $this->session->setFlash('error', 'İşlem kontrol başarısız.');
            $this->redirect('check/transactions/' . $transaction['check_note_id']);
        }
    }
}
?>