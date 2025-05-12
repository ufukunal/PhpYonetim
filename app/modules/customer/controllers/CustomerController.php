<?php
class CustomerController extends BaseController {
    private $customerModel;
    private $groupModel;
    private $addressModel;
    private $contactModel;
    private $auth;

    public function __construct() {
        parent::__construct();
        $this->customerModel = new CustomerModel();
        $this->groupModel = new CustomerGroupModel();
        $this->addressModel = new CustomerAddressModel();
        $this->contactModel = new CustomerContactModel();
        $this->auth = new Auth();
    }

    public function list() {
        if (!$this->auth->hasPermission('customer.view')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('');
        }
        $filters = $_GET ?? [];
        $customers = $this->customerModel->getAllCustomers($filters);
        $groups = $this->groupModel->getAllGroups();
        $this->view('customer/customer_list', [
            'title' => 'Müşteriler',
            'customers' => $customers,
            'groups' => $groups
        ]);
    }

    public function add() {
        if (!$this->auth->hasPermission('customer.edit')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('customer/list');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'code' => $_POST['code'],
                'type' => $_POST['type'],
                'title' => $_POST['title'],
                'tax_number' => $_POST['tax_number'],
                'tax_office' => $_POST['tax_office'],
                'group_id' => $_POST['group_id']
            ];
            if ($this->customerModel->addCustomer($data)) {
                $customer_id = $this->db->getConnection()->lastInsertId();
                // Adresleri kaydet
                if (!empty($_POST['addresses'])) {
                    foreach ($_POST['addresses'] as $addr) {
                        $addr['customer_id'] = $customer_id;
                        $this->addressModel->addAddress($addr);
                    }
                }
                // Yetkili kişileri kaydet
                if (!empty($_POST['contacts'])) {
                    foreach ($_POST['contacts'] as $contact) {
                        $contact['customer_id'] = $customer_id;
                        $this->contactModel->addContact($contact);
                    }
                }
                $this->session->setFlash('success', 'Müşteri eklendi.');
                $this->redirect('customer/list');
            } else {
                $this->session->setFlash('error', 'Müşteri ekleme başarısız.');
            }
        }
        $groups = $this->groupModel->getAllGroups();
        $this->view('customer/customer_add', [
            'title' => 'Müşteri Ekle',
            'groups' => $groups
        ]);
    }

    public function edit($id) {
        if (!$this->auth->hasPermission('customer.edit')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('customer/list');
        }
        $customer = $this->customerModel->getCustomerById($id);
        if (!$customer) {
            $this->session->setFlash('error', 'Müşteri bulunamadı.');
            $this->redirect('customer/list');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'code' => $_POST['code'],
                'type' => $_POST['type'],
                'title' => $_POST['title'],
                'tax_number' => $_POST['tax_number'],
                'tax_office' => $_POST['tax_office'],
                'group_id' => $_POST['group_id']
            ];
            if ($this->customerModel->updateCustomer($id, $data)) {
                // Adresleri güncelle/sil
                if (!empty($_POST['addresses'])) {
                    foreach ($_POST['addresses'] as $addr) {
                        if (isset($addr['id']) && $addr['id']) {
                            $this->addressModel->updateAddress($addr['id'], $addr);
                        } else {
                            $addr['customer_id'] = $id;
                            $this->addressModel->addAddress($addr);
                        }
                    }
                }
                if (!empty($_POST['delete_addresses'])) {
                    foreach ($_POST['delete_addresses'] as $addr_id) {
                        $this->addressModel->deleteAddress($addr_id);
                    }
                }
                // Yetkili kişileri güncelle/sil
                if (!empty($_POST['contacts'])) {
                    foreach ($_POST['contacts'] as $contact) {
                        if (isset($contact['id']) && $contact['id']) {
                            $this->contactModel->updateContact($contact['id'], $contact);
                        } else {
                            $contact['customer_id'] = $id;
                            $this->contactModel->addContact($contact);
                        }
                    }
                }
                if (!empty($_POST['delete_contacts'])) {
                    foreach ($_POST['delete_contacts'] as $contact_id) {
                        $this->contactModel->deleteContact($contact_id);
                    }
                }
                $this->session->setFlash('success', 'Müşteri güncellendi.');
                $this->redirect('customer/list');
            } else {
                $this->session->setFlash('error', 'Müşteri güncelleme başarısız.');
            }
        }
        $groups = $this->groupModel->getAllGroups();
        $addresses = $this->addressModel->getAddressesByCustomer($id);
        $contacts = $this->contactModel->getContactsByCustomer($id);
        $this->view('customer/customer_edit', [
            'title' => 'Müşteri Düzenle',
            'customer' => $customer,
            'groups' => $groups,
            'addresses' => $addresses,
            'contacts' => $contacts
        ]);
    }

    public function detail($id) {
        if (!$this->auth->hasPermission('customer.view')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('customer/list');
        }
        $customer = $this->customerModel->getCustomerById($id);
        if (!$customer) {
            $this->session->setFlash('error', 'Müşteri bulunamadı.');
            $this->redirect('customer/list');
        }
        $addresses = $this->addressModel->getAddressesByCustomer($id);
        $contacts = $this->contactModel->getContactsByCustomer($id);
        $transactions = $this->customerModel->getTransactions(['customer_id' => $id]);
        $balance = $this->customerModel->getBalanceSummary(['customer_id' => $id])[0]['balance'] ?? 0;
        $this->view('customer/customer_detail', [
            'title' => 'Müşteri Detayı',
            'customer' => $customer,
            'addresses' => $addresses,
            'contacts' => $contacts,
            'transactions' => $transactions,
            'balance' => $balance
        ]);
    }

    public function transactionList() {
        if (!$this->auth->hasPermission('customer.view')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('customer/list');
        }
        $filters = $_GET ?? [];
        $transactions = $this->customerModel->getTransactions($filters);
        $customers = $this->customerModel->getAllCustomers();
        $this->view('customer/transaction_list', [
            'title' => 'Müşteri İşlemleri',
            'transactions' => $transactions,
            'customers' => $customers
        ]);
    }

    public function transactionAdd() {
        if (!$this->auth->hasPermission('customer.transaction.add')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('customer/transactionList');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'customer_id' => $_POST['customer_id'],
                'type' => $_POST['type'],
                'amount' => $_POST['amount'],
                'currency' => $_POST['currency'],
                'transaction_date' => $_POST['transaction_date'],
                'invoice_no' => $_POST['invoice_no'],
                'invoice_address_id' => $_POST['invoice_address_id'],
                'delivery_address_id' => $_POST['delivery_address_id'],
                'description' => $_POST['description'],
                'stock_entry_id' => $_POST['stock_entry_id'],
                'stock_exit_id' => $_POST['stock_exit_id']
            ];
            if ($this->customerModel->addTransaction($data)) {
                $this->session->setFlash('success', 'İşlem eklendi.');
                $this->redirect('customer/transactionList');
            } else {
                $this->session->setFlash('error', 'İşlem ekleme başarısız.');
            }
        }
        $customers = $this->customerModel->getAllCustomers();
        $this->view('customer/transaction_add', [
            'title' => 'İşlem Ekle',
            'customers' => $customers
        ]);
    }

    public function transactionEdit($id) {
        if (!$this->auth->hasPermission('customer.transaction.add')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('customer/transactionList');
        }
        $transaction = $this->customerModel->getTransactionById($id);
        if (!$transaction) {
            $this->session->setFlash('error', 'İşlem bulunamadı.');
            $this->redirect('customer/transactionList');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'customer_id' => $_POST['customer_id'],
                'type' => $_POST['type'],
                'amount' => $_POST['amount'],
                'currency' => $_POST['currency'],
                'transaction_date' => $_POST['transaction_date'],
                'invoice_no' => $_POST['invoice_no'],
                'invoice_address_id' => $_POST['invoice_address_id'],
                'delivery_address_id' => $_POST['delivery_address_id'],
                'description' => $_POST['description'],
                'stock_entry_id' => $_POST['stock_entry_id'],
                'stock_exit_id' => $_POST['stock_exit_id']
            ];
            if ($this->customerModel->updateTransaction($id, $data)) {
                $this->session->setFlash('success', 'İşlem güncellendi.');
                $this->redirect('customer/transactionList');
            } else {
                $this->session->setFlash('error', 'İşlem güncelleme başarısız.');
            }
        }
        $customers = $this->customerModel->getAllCustomers();
        $addresses = $this->addressModel->getAddressesByCustomer($transaction['customer_id']);
        $this->view('customer/transaction_edit', [
            'title' => 'İşlem Düzenle',
            'transaction' => $transaction,
            'customers' => $customers,
            'addresses' => $addresses
        ]);
    }

    public function balanceSummary() {
        if (!$this->auth->hasPermission('customer.view')) {
            $this->session->setFlash('error', 'Bu sayfaya erişim yetkiniz yok.');
            $this->redirect('customer/list');
        }
        $filters = $_GET ?? [];
        $summary = $this->customerModel->getBalanceSummary($filters);
        $groups = $this->groupModel->getAllGroups();
        $this->view('customer/balance_summary', [
            'title' => 'Bakiye Özeti',
            'summary' => $summary,
            'groups' => $groups
        ]);
    }
}
?>