<?php
class BankModel {
    private $db;
    private $session;

    public function __construct() {
        $this->db = new Database();
        $this->session = new Session();
    }

    public function addBankAccount($data) {
        $query = "INSERT INTO bank_accounts (code, name, bank_name, branch_code, branch_name, account_number, iban, currency, branch_id, balance, status, created_by)
                  VALUES (:code, :name, :bank_name, :branch_code, :branch_name, :account_number, :iban, :currency, :branch_id, :balance, :status, :created_by)";
        $params = [
            'code' => $data['code'],
            'name' => $data['name'],
            'bank_name' => $data['bank_name'],
            'branch_code' => $data['branch_code'] ?? null,
            'branch_name' => $data['branch_name'] ?? null,
            'account_number' => $data['account_number'] ?? null,
            'iban' => $data['iban'],
            'currency' => $data['currency'],
            'branch_id' => $data['branch_id'] ?? null,
            'balance' => $data['balance'] ?? 0.00,
            'status' => $data['status'] ?? 'active',
            'created_by' => $this->session->get('user_id')
        ];
        return $this->db->execute($query, $params);
    }

    public function updateBankAccount($id, $data) {
        $query = "UPDATE bank_accounts SET
                  code = :code, name = :name, bank_name = :bank_name, branch_code = :branch_code,
                  branch_name = :branch_name, account_number = :account_number, iban = :iban,
                  currency = :currency, branch_id = :branch_id, status = :status,
                  updated_by = :updated_by, updated_at = NOW()
                  WHERE id = :id";
        $params = [
            'id' => $id,
            'code' => $data['code'],
            'name' => $data['name'],
            'bank_name' => $data['bank_name'],
            'branch_code' => $data['branch_code'] ?? null,
            'branch_name' => $data['branch_name'] ?? null,
            'account_number' => $data['account_number'] ?? null,
            'iban' => $data['iban'],
            'currency' => $data['currency'],
            'branch_id' => $data['branch_id'] ?? null,
            'status' => $data['status'] ?? 'active',
            'updated_by' => $this->session->get('user_id')
        ];
        return $this->db->execute($query, $params);
    }

    public function deleteBankAccount($id) {
        $query = "DELETE FROM bank_accounts WHERE id = :id AND NOT EXISTS (SELECT 1 FROM bank_transactions WHERE bank_account_id = :id)";
        return $this->db->execute($query, ['id' => $id]);
    }

    public function getBankAccountById($id) {
        $query = "SELECT * FROM bank_accounts WHERE id = :id";
        return $this->db->queryOne($query, ['id' => $id]);
    }

    public function getAllBankAccounts($filters = []) {
        $query = "SELECT * FROM bank_accounts WHERE 1=1";
        $params = [];

        if (!empty($filters['code'])) {
            $query .= " AND code LIKE :code";
            $params['code'] = '%' . $filters['code'] . '%';
        }
        if (!empty($filters['name'])) {
            $query .= " AND name LIKE :name";
            $params['name'] = '%' . $filters['name'] . '%';
        }
        if (!empty($filters['status'])) {
            $query .= " AND status = :status";
            $params['status'] = $filters['status'];
        }

        return $this->db->query($query, $params);
    }

    public function addTransaction($data) {
        $query = "INSERT INTO bank_transactions (bank_account_id, type, amount, currency, transaction_date, customer_id, invoice_id, order_id, description, status, created_by)
                  VALUES (:bank_account_id, :type, :amount, :currency, :transaction_date, :customer_id, :invoice_id, :order_id, :description, :status, :created_by)";
        $params = [
            'bank_account_id' => $data['bank_account_id'],
            'type' => $data['type'],
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'transaction_date' => $data['transaction_date'],
            'customer_id' => $data['customer_id'] ?? null,
            'invoice_id' => $data['invoice_id'] ?? null,
            'order_id' => $data['order_id'] ?? null,
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'created_by' => $this->session->get('user_id')
        ];
        if ($this->db->execute($query, $params)) {
            // Banka bakiyesini güncelle
            $multiplier = ($data['type'] == 'out' || $data['type'] == 'havale' || $data['type'] == 'eft' || $data['type'] == 'odeme') ? -1 : 1;
            $query = "UPDATE bank_accounts SET balance = balance + (:amount * :multiplier) WHERE id = :bank_account_id";
            $params = [
                'amount' => $data['amount'],
                'multiplier' => $multiplier,
                'bank_account_id' => $data['bank_account_id']
            ];
            return $this->db->execute($query, $params);
        }
        return false;
    }

    public function updateTransaction($id, $data) {
        // Mevcut işlemi al
        $old_transaction = $this->getTransactionById($id);
        // Eski işlemi tersine çevir
        $multiplier = ($old_transaction['type'] == 'out' || $old_transaction['type'] == 'havale' || $old_transaction['type'] == 'eft' || $old_transaction['type'] == 'odeme') ? 1 : -1;
        $query = "UPDATE bank_accounts SET balance = balance + (:amount * :multiplier) WHERE id = :bank_account_id";
        $this->db->execute($query, [
            'amount' => $old_transaction['amount'],
            'multiplier' => $multiplier,
            'bank_account_id' => $old_transaction['bank_account_id']
        ]);

        // Yeni işlemi kaydet
        $query = "UPDATE bank_transactions SET
                  bank_account_id = :bank_account_id, type = :type, amount = :amount, currency = :currency,
                  transaction_date = :transaction_date, customer_id = :customer_id, invoice_id = :invoice_id,
                  order_id = :order_id, description = :description, status = :status
                  WHERE id = :id";
        $params = [
            'id' => $id,
            'bank_account_id' => $data['bank_account_id'],
            'type' => $data['type'],
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'transaction_date' => $data['transaction_date'],
            'customer_id' => $data['customer_id'] ?? null,
            'invoice_id' => $data['invoice_id'] ?? null,
            'order_id' => $data['order_id'] ?? null,
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'pending'
        ];
        if ($this->db->execute($query, $params)) {
            // Yeni banka bakiyesini güncelle
            $multiplier = ($data['type'] == 'out' || $data['type'] == 'havale' || $data['type'] == 'eft' || $data['type'] == 'odeme') ? -1 : 1;
            $query = "UPDATE bank_accounts SET balance = balance + (:amount * :multiplier) WHERE id = :bank_account_id";
            $params = [
                'amount' => $data['amount'],
                'multiplier' => $multiplier,
                'bank_account_id' => $data['bank_account_id']
            ];
            return $this->db->execute($query, $params);
        }
        return false;
    }

    public function deleteTransaction($id) {
        $transaction = $this->getTransactionById($id);
        $query = "DELETE FROM bank_transactions WHERE id = :id";
        if ($this->db->execute($query, ['id' => $id])) {
            // Banka bakiyesini tersine çevir
            $multiplier = ($transaction['type'] == 'out' || $transaction['type'] == 'havale' || $transaction['type'] == 'eft' || $transaction['type'] == 'odeme') ? 1 : -1;
            $query = "UPDATE bank_accounts SET balance = balance + (:amount * :multiplier) WHERE id = :bank_account_id";
            $params = [
                'amount' => $transaction['amount'],
                'multiplier' => $multiplier,
                'bank_account_id' => $transaction['bank_account_id']
            ];
            return $this->db->execute($query, $params);
        }
        return false;
    }

    public function getTransactionById($id) {
        $query = "SELECT bt.*, ba.name AS bank_account_name, ca.title AS customer_title
                  FROM bank_transactions bt
                  JOIN bank_accounts ba ON bt.bank_account_id = ba.id
                  LEFT JOIN customer_accounts ca ON bt.customer_id = ca.id
                  WHERE bt.id = :id";
        return $this->db->queryOne($query, ['id' => $id]);
    }

    public function getTransactionsByBankAccount($bank_account_id, $filters = []) {
        $query = "SELECT bt.*, ca.title AS customer_title, i.invoice_no, o.order_no
                  FROM bank_transactions bt
                  LEFT JOIN customer_accounts ca ON bt.customer_id = ca.id
                  LEFT JOIN invoices i ON bt.invoice_id = i.id
                  LEFT JOIN orders o ON bt.order_id = o.id
                  WHERE bt.bank_account_id = :bank_account_id";
        $params = ['bank_account_id' => $bank_account_id];

        if (!empty($filters['type'])) {
            $query .= " AND bt.type = :type";
            $params['type'] = $filters['type'];
        }
        if (!empty($filters['customer_id'])) {
            $query .= " AND bt.customer_id = :customer_id";
            $params['customer_id'] = $filters['customer_id'];
        }
        if (!empty($filters['status'])) {
            $query .= " AND bt.status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['start_date'])) {
            $query .= " AND bt.transaction_date >= :start_date";
            $params['start_date'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $query .= " AND bt.transaction_date <= :end_date";
            $params['end_date'] = $filters['end_date'];
        }

        return $this->db->query($query, $params);
    }

    public function controlTransaction($id, $status) {
        $query = "UPDATE bank_transactions SET status = :status WHERE id = :id";
        return $this->db->execute($query, ['id' => $id, 'status' => $status]);
    }

    public function updateCustomerBalance($customer_id, $amount, $type, $currency = 'TL') {
        $query = "INSERT INTO customer_transactions (customer_id, type, amount, currency, transaction_date, created_by)
                  VALUES (:customer_id, :type, :amount, :currency, NOW(), :created_by)";
        $params = [
            'customer_id' => $customer_id,
            'type' => $type == 'tahsilat' ? 'collection' : 'payment',
            'amount' => $amount,
            'currency' => $currency,
            'created_by' => $this->session->get('user_id')
        ];
        return $this->db->execute($query, $params);
    }
}
?>