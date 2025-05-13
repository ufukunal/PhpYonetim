<?php
class BankController extends BaseController {
    private $bankModel;
    private $customerModel;
    private $cashModel;
    private $auth;

    public function __construct() {
        parent::__construct();
        $this->bankModel = new BankModel();
        $this->customerModel = new CustomerModel();
        $this->cashModel = new CashModel();
        $this->auth = new Auth();
    }

    public function list() {
        if (!$this->auth->hasPermission('bank.view')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('');
        }
        $filters = $_GET ?? [];
        $bank_accounts = $this->bankModel->getAllBankAccounts($filters);
        $this->view('bank/bank_list', [
            'title' => 'Banka Hesapları',
            'bank_accounts' => $bank_accounts
        ]);
    }

    public function add() {
        if (!$this->auth->hasPermission('bank.add')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('bank/list');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'code' => $_POST['code'],
                'name' => $_POST['name'],
                'bank_name' => $_POST['bank_name'],
                'branch_code' => $_POST['branch_code'],
                'branch_name' => $_POST['branch_name'],
                'account_number' => $_POST['account_number'],
                'iban' => $_POST['iban'],
                'currency' => $_POST['currency'],
                'branch_id' => $_POST['branch_id'],
                'balance' => $_POST['balance'],
                'status' => $_POST['status']
            ];
            if ($this->bankModel->addBankAccount($data)) {
                $this->session->setFlash('success', 'Banka hesabı eklendi.');
                $this->redirect('bank/list');
            } else {
                $this->session->setFlash('error', 'Banka hesabı ekleme başarısız.');
            }
        }
        $this->view('bank/bank_add', [
            'title' => 'Banka Hesabı Ekle'
        ]);
    }

    public function edit($id) {
        if (!$this->auth->hasPermission('bank.edit')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('bank/list');
        }
        $bank_account = $this->bankModel->getBankAccountById($id);
        if (!$bank_account) {
            $this->session->setFlash('error', 'Banka hesabı bulunamadı.');
            $this->redirect('bank/list');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'code' => $_POST['code'],
                'name' => $_POST['name'],
                'bank_name' => $_POST['bank_name'],
                'branch_code' => $_POST['branch_code'],
                'branch_name' => $_POST['branch_name'],
                'account_number' => $_POST['account_number'],
                'iban' => $_POST['iban'],
                'currency' => $_POST['currency'],
                'branch_id' => $_POST['branch_id'],
                'status' => $_POST['status']
            ];
            if ($this->bankModel->updateBankAccount($id, $data)) {
                $this->session->setFlash('success', 'Banka hesabı güncellendi.');
                $this->redirect('bank/list');
            } else {
                $this->session->setFlash('error', 'Banka hesabı güncelleme başarısız.');
            }
        }
        $this->view('bank/bank_edit', [
            'title' => 'Banka Hesabı Düzenle',
            'bank_account' => $bank_account
        ]);
    }

    public function transactions($id) {
        if (!$this->auth->hasPermission('bank.view')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('bank/list');
        }
        $bank_account = $this->bankModel->getBankAccountById($id);
        if (!$bank_account) {
            $this->session->setFlash('error', 'Banka hesabı bulunamadı.');
            $this->redirect('bank/list');
        }
        $filters = $_GET ?? [];
        $transactions = $this->bankModel->getTransactionsByBankAccount($id, $filters);
        $customers = $this->customerModel->getAllCustomers();
        $this->view('bank/transaction_list', [
            'title' => 'Banka İşlemleri - ' . sanitize($bank_account['name']),
            'bank_account' => $bank_account,
            'transactions' => $transactions,
            'customers' => $customers
        ]);
    }

    public function addTransaction($bank_account_id) {
        if (!$this->auth->hasPermission('bank.add')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('bank/transactions/' . $bank_account_id);
        }
        $bank_account = $this->bankModel->getBankAccountById($bank_account_id);
        if (!$bank_account) {
            $this->session->setFlash('error', 'Banka hesabı bulunamadı.');
            $this->redirect('bank/list');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'bank_account_id' => $bank_account_id,
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
            if ($this->bankModel->addTransaction($data)) {
                if ($data['customer_id'] && in_array($data['type'], ['tahsilat', 'odeme'])) {
                    $this->bankModel->updateCustomerBalance($data['customer_id'], $data['amount'], $data['type'], $data['currency']);
                }
                // Virman işlemi için hedef hesabı güncelle
                if ($data['type'] == 'virman' && !empty($_POST['target_bank_account_id'])) {
                    $target_data = [
                        'bank_account_id' => $_POST['target_bank_account_id'],
                        'type' => 'in',
                        'amount' => $_POST['amount'],
                        'currency' => $_POST['currency'],
                        'transaction_date' => $_POST['transaction_date'],
                        'description' => 'Virman: ' . $bank_account['name'] . ' -> ' . $data['description'],
                        'status' => $_POST['status']
                    ];
                    $this->bankModel->addTransaction($target_data);
                }
                // Kasa-banka virmanı için kasa işlemini güncelle
                if ($data['type'] == 'virman' && !empty($_POST['cash_register_id'])) {
                    $cash_data = [
                        'cash_register_id' => $_POST['cash_register_id'],
                        'type' => 'in',
                        'amount' => $_POST['amount'],
                        'currency' => $_POST['currency'],
                        'transaction_date' => $_POST['transaction_date'],
                        'description' => 'Banka-kasa virmanı: ' . $bank_account['name'] . ' -> ' . $data['description'],
                        'status' => $_POST['status']
                    ];
                    $this->cashModel->addTransaction($cash_data);
                }
                $this->session->setFlash('success', 'İşlem eklendi.');
                $this->redirect('bank/transactions/' . $bank_account_id);
            } else {
                $this->session->setFlash('error', 'İşlem ekleme başarısız.');
            }
        }
        $customers = $this->customerModel->getAllCustomers();
        $invoices = $this->db->query("SELECT * FROM invoices WHERE status != 'canceled'");
        $orders = $this->db->query("SELECT * FROM orders WHERE status != 'canceled'");
        $bank_accounts = $this->bankModel->getAllBankAccounts(['status' => 'active']);
        $cash_registers = $this->cashModel->getAllCashRegisters(['status' => 'active']);
        $this->view('bank/transaction_add', [
            'title' => 'Banka İşlemi Ekle',
            'bank_account' => $bank_account,
            'customers' => $customers,
            'invoices' => $invoices,
            'orders' => $orders,
            'bank_accounts' => $bank_accounts,
            'cash_registers' => $cash_registers
        ]);
    }

    public function editTransaction($id) {
        if (!$this->auth->hasPermission('bank.edit')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('bank/list');
        }
        $transaction = $this->bankModel->getTransactionById($id);
        if (!$transaction) {
            $this->session->setFlash('error', 'İşlem bulunamadı.');
            $this->redirect('bank/list');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'bank_account_id' => $_POST['bank_account_id'],
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
            if ($this->bankModel->updateTransaction($id, $data)) {
                if ($data['customer_id'] && in_array($data['type'], ['tahsilat', 'odeme'])) {
                    $this->bankModel->updateCustomerBalance($data['customer_id'], $data['amount'], $data['type'], $data['currency']);
                }
                $this->session->setFlash('success', 'İşlem güncellendi.');
                $this->redirect('bank/transactions/' . $data['bank_account_id']);
            } else {
                $this->session->setFlash('error', 'İşlem güncelleme başarısız.');
            }
        }
        $bank_accounts = $this->bankModel->getAllBankAccounts();
        $customers = $this->customerModel->getAllCustomers();
        $invoices = $this->db->query("SELECT * FROM invoices WHERE status != 'canceled'");
        $orders = $this->db->query("SELECT * FROM orders WHERE status != 'canceled'");
        $cash_registers = $this->cashModel->getAllCashRegisters(['status' => 'active']);
        $this->view('bank/transaction_edit', [
            'title' => 'Banka İşlemi Düzenle',
            'transaction' => $transaction,
            'bank_accounts' => $bank_accounts,
            'customers' => $customers,
            'invoices' => $invoices,
            'orders' => $orders,
            'cash_registers' => $cash_registers
        ]);
    }

    public function controlTransaction($id, $status) {
        if (!$this->auth->hasPermission('bank.control')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('bank/list');
        }
        $transaction = $this->bankModel->getTransactionById($id);
        if (!$transaction) {
            $this->session->setFlash('error', 'İşlem bulunamadı.');
            $this->redirect('bank/list');
        }
        if ($this->bankModel->controlTransaction($id, $status)) {
            $this->session->setFlash('success', 'İşlem ' . ($status == 'controlled' ? 'kontrol edildi' : 'reddedildi') . '.');
            $this->redirect('bank/transactions/' . $transaction['bank_account_id']);
        } else {
            $this->session->setFlash('error', 'İşlem kontrol başarısız.');
            $this->redirect('bank/transactions/' . $transaction['bank_account_id']);
        }
    }
}
?>