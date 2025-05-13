<?php
class CashModel {
    private $db;
    private $session;

    public function __construct() {
        $this->db = new Database();
        $this->session = new Session();
    }

    public function addCashRegister($data) {
        $query = "INSERT INTO cash_registers (code, name, currency, branch_id, balance, status, created_by)
                  VALUES (:code, :name, :currency, :branch_id, :balance, :status, :created_by)";
        $params = [
            'code' => $data['code'],
            'name' => $data['name'],
            'currency' => $data['currency'],
            'branch_id' => $data['branch_id'] ?? null,
            'balance' => $data['balance'] ?? 0.00,
            'status' => $data['status'] ?? 'active',
            'created_by' => $this->session->get('user_id')
        ];
        return $this->db->execute($query, $params);
    }

    public function updateCashRegister($id, $data) {
        $query = "UPDATE cash_registers SET
                  code = :code, name = :name, currency = :currency, branch_id = :branch_id,
                  status = :status, updated_by = :updated_by, updated_at = NOW()
                  WHERE id = :id";
        $params = [
            'id' => $id,
            'code' => $data['code'],
            'name' => $data['name'],
            'currency' => $data['currency'],
            'branch_id' => $data['branch_id'] ?? null,
            'status' => $data['status'] ?? 'active',
            'updated_by' => $this->session->get('user_id')
        ];
        return $this->db->execute($query, $params);
    }

    public function deleteCashRegister($id) {
        $query = "DELETE FROM cash_registers WHERE id = :id AND NOT EXISTS (SELECT 1 FROM cash_transactions WHERE cash_register_id = :id)";
        return $this->db->execute($query, ['id' => $id]);
    }

    public function getCashRegisterById($id) {
        $query = "SELECT * FROM cash_registers WHERE id = :id";
        return $this->db->queryOne($query, ['id' => $id]);
    }

    public function getAllCashRegisters($filters = []) {
        $query = "SELECT * FROM cash_registers WHERE 1=1";
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
        $query = "INSERT INTO cash_transactions (cash_register_id, type, amount, currency, transaction_date, customer_id, invoice_id, order_id, description, status, created_by)
                  VALUES (:cash_register_id, :type, :amount, :currency, :transaction_date, :customer_id, :invoice_id, :order_id, :description, :status, :created_by)";
        $params = [
            'cash_register_id' => $data['cash_register_id'],
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
            $multiplier = ($data['type'] == 'out' || $data['type'] == 'odeme') ? -1 : 1;
            $query = "UPDATE cash_registers SET balance = balance + (:amount * :multiplier) WHERE id = :cash_register_id";
            $params = [
                'amount' => $data['amount'],
                'multiplier' => $multiplier,
                'cash_register_id' => $data['cash_register_id']
            ];
            return $this->db->execute($query, $params);
        }
        return false;
    }

    public function updateTransaction($id, $data) {
        $old_transaction = $this->getTransactionById($id);
        $multiplier = ($old_transaction['type'] == 'out' || $old_transaction['type'] == 'odeme') ? 1 : -1;
        $query = "UPDATE cash_registers SET balance = balance + (:amount * :multiplier) WHERE id = :cash_register_id";
        $this->db->execute($query, [
            'amount' => $old_transaction['amount'],
            'multiplier' => $multiplier,
            'cash_register_id' => $old_transaction['cash_register_id']
        ]);

        $query = "UPDATE cash_transactions SET
                  cash_register_id = :cash_register_id, type = :type, amount = :amount, currency = :currency,
                  transaction_date = :transaction_date, customer_id = :customer_id, invoice_id = :invoice_id,
                  order_id = :order_id, description = :description, status = :status
                  WHERE id = :id";
        $params = [
            'id' => $id,
            'cash_register_id' => $data['cash_register_id'],
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
            $multiplier = ($data['type'] == 'out' || $data['type'] == 'odeme') ? -1 : 1;
            $query = "UPDATE cash_registers SET balance = balance + (:amount * :multiplier) WHERE id = :cash_register_id";
            $params = [
                'amount' => $data['amount'],
                'multiplier' => $multiplier,
                'cash_register_id' => $data['cash_register_id']
            ];
            return $this->db->execute($query, $params);
        }
        return false;
    }

    public function deleteTransaction($id) {
        $transaction = $this->getTransactionById($id);
        $query = "DELETE FROM cash_transactions WHERE id = :id";
        if ($this->db->execute($query, ['id' => $id])) {
            $multiplier = ($transaction['type'] == 'out' || $transaction['type'] == 'odeme') ? 1 : -1;
            $query = "UPDATE cash_registers SET balance = balance + (:amount * :multiplier) WHERE id = :cash_register_id";
            $params = [
                'amount' => $transaction['amount'],
                'multiplier' => $multiplier,
                'cash_register_id' => $transaction['cash_register_id']
            ];
            return $this->db->execute($query, $params);
        }
        return false;
    }

    public function getTransactionById($id) {
        $query = "SELECT ct.*, cr.name AS cash_register_name, ca.title AS customer_title
                  FROM cash_transactions ct
                  JOIN cash_registers cr ON ct.cash_register_id = cr.id
                  LEFT JOIN customer_accounts ca ON ct.customer_id = ca.id
                  WHERE ct.id = :id";
        return $this->db->queryOne($query, ['id' => $id]);
    }

    public function getTransactionsByCashRegister($cash_register_id, $filters = []) {
        $query = "SELECT ct.*, ca.title AS customer_title, i.invoice_no
                  FROM cash_transactions ct
                  LEFT JOIN customer_accounts ca ON ct.customer_id = ca.id
                  LEFT JOIN invoices i ON ct.invoice_id = i.id
                  WHERE ct.cash_register_id = :cash_register_id";
        $params = ['cash_register_id' => $cash_register_id];

        if (!empty($filters['type'])) {
            $query .= " AND ct.type = :type";
            $params['type'] = $filters['type'];
        }
        if (!empty($filters['customer_id'])) {
            $query .= " AND ct.customer_id = :customer_id";
            $params['customer_id'] = $filters['customer_id'];
        }
        if (!empty($filters['status'])) {
            $query .= " AND ct.status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['start_date'])) {
            $query .= " AND ct.transaction_date >= :start_date";
            $params['start_date'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $query .= " AND ct.transaction_date <= :end_date";
            $params['end_date'] = $filters['end_date'];
        }

        return $this->db->query($query, $params);
    }

    public function controlTransaction($id, $status) {
        $query = "UPDATE cash_transactions SET status = :status WHERE id = :id";
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