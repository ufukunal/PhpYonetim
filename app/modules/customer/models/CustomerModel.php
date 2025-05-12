<?php
class CustomerModel {
    private $db;
    private $session;

    public function __construct() {
        $this->db = new Database();
        $this->session = new Session();
    }

    public function addCustomer($data) {
        $query = "INSERT INTO customer_accounts (code, type, title, tax_number, tax_office, group_id, created_by)
                  VALUES (:code, :type, :title, :tax_number, :tax_office, :group_id, :created_by)";
        $params = [
            'code' => $data['code'],
            'type' => $data['type'],
            'title' => $data['title'],
            'tax_number' => $data['tax_number'] ?? null,
            'tax_office' => $data['tax_office'] ?? null,
            'group_id' => $data['group_id'] ?? null,
            'created_by' => $this->session->get('user_id')
        ];
        return $this->db->execute($query, $params);
    }

    public function updateCustomer($id, $data) {
        $query = "UPDATE customer_accounts SET
                  code = :code, type = :type, title = :title, tax_number = :tax_number, tax_office = :tax_office,
                  group_id = :group_id, updated_by = :updated_by, updated_at = NOW()
                  WHERE id = :id";
        $params = [
            'id' => $id,
            'code' => $data['code'],
            'type' => $data['type'],
            'title' => $data['title'],
            'tax_number' => $data['tax_number'] ?? null,
            'tax_office' => $data['tax_office'] ?? null,
            'group_id' => $data['group_id'] ?? null,
            'updated_by' => $this->session->get('user_id')
        ];
        return $this->db->execute($query, $params);
    }

    public function deleteCustomer($id) {
        $query = "DELETE FROM customer_accounts WHERE id = :id";
        return $this->db->execute($query, ['id' => $id]);
    }

    public function getCustomerById($id) {
        $query = "SELECT * FROM customer_accounts WHERE id = :id";
        return $this->db->queryOne($query, ['id' => $id]);
    }

    public function getAllCustomers($filters = []) {
        $query = "SELECT ca.*, cg.name AS group_name,
                         (SELECT COUNT(*) FROM customer_addresses WHERE customer_id = ca.id) AS address_count,
                         (SELECT COUNT(*) FROM customer_contacts WHERE customer_id = ca.id) AS contact_count,
                         (SELECT SUM(CASE WHEN type IN ('sale', 'payment') THEN amount ELSE -amount END)
                          FROM customer_transactions WHERE customer_id = ca.id) AS balance
                  FROM customer_accounts ca
                  LEFT JOIN customer_groups cg ON ca.group_id = cg.id
                  WHERE 1=1";
        $params = [];

        if (!empty($filters['code'])) {
            $query .= " AND ca.code LIKE :code";
            $params['code'] = '%' . $filters['code'] . '%';
        }
        if (!empty($filters['title'])) {
            $query .= " AND ca.title LIKE :title";
            $params['title'] = '%' . $filters['title'] . '%';
        }
        if (!empty($filters['type'])) {
            $query .= " AND ca.type = :type";
            $params['type'] = $filters['type'];
        }
        if (!empty($filters['group_id'])) {
            $query .= " AND ca.group_id = :group_id";
            $params['group_id'] = $filters['group_id'];
        }

        return $this->db->query($query, $params);
    }

    public function addTransaction($data) {
        $query = "INSERT INTO customer_transactions (customer_id, type, amount, currency, transaction_date, invoice_no, invoice_address_id, delivery_address_id, description, stock_entry_id, stock_exit_id, created_by)
                  VALUES (:customer_id, :type, :amount, :currency, :transaction_date, :invoice_no, :invoice_address_id, :delivery_address_id, :description, :stock_entry_id, :stock_exit_id, :created_by)";
        $params = [
            'customer_id' => $data['customer_id'],
            'type' => $data['type'],
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'TL',
            'transaction_date' => $data['transaction_date'],
            'invoice_no' => $data['invoice_no'] ?? null,
            'invoice_address_id' => $data['invoice_address_id'] ?? null,
            'delivery_address_id' => $data['delivery_address_id'] ?? null,
            'description' => $data['description'] ?? null,
            'stock_entry_id' => $data['stock_entry_id'] ?? null,
            'stock_exit_id' => $data['stock_exit_id'] ?? null,
            'created_by' => $this->session->get('user_id')
        ];
        return $this->db->execute($query, $params);
    }

    public function updateTransaction($id, $data) {
        $query = "UPDATE customer_transactions SET
                  customer_id = :customer_id, type = :type, amount = :amount, currency = :currency,
                  transaction_date = :transaction_date, invoice_no = :invoice_no, invoice_address_id = :invoice_address_id,
                  delivery_address_id = :delivery_address_id, description = :description,
                  stock_entry_id = :stock_entry_id, stock_exit_id = :stock_exit_id
                  WHERE id = :id";
        $params = [
            'id' => $id,
            'customer_id' => $data['customer_id'],
            'type' => $data['type'],
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'TL',
            'transaction_date' => $data['transaction_date'],
            'invoice_no' => $data['invoice_no'] ?? null,
            'invoice_address_id' => $data['invoice_address_id'] ?? null,
            'delivery_address_id' => $data['delivery_address_id'] ?? null,
            'description' => $data['description'] ?? null,
            'stock_entry_id' => $data['stock_entry_id'] ?? null,
            'stock_exit_id' => $data['stock_exit_id'] ?? null
        ];
        return $this->db->execute($query, $params);
    }

    public function getTransactionById($id) {
        $query = "SELECT * FROM customer_transactions WHERE id = :id";
        return $this->db->queryOne($query, ['id' => $id]);
    }

    public function getTransactions($filters = []) {
        $query = "SELECT ct.*, ca.title AS customer_title, ca_in.title AS invoice_address_title, ca_del.title AS delivery_address_title
                  FROM customer_transactions ct
                  JOIN customer_accounts ca ON ct.customer_id = ca.id
                  LEFT JOIN customer_addresses ca_in ON ct.invoice_address_id = ca_in.id
                  LEFT JOIN customer_addresses ca_del ON ct.delivery_address_id = ca_del.id
                  WHERE 1=1";
        $params = [];

        if (!empty($filters['customer_id'])) {
            $query .= " AND ct.customer_id = :customer_id";
            $params['customer_id'] = $filters['customer_id'];
        }
        if (!empty($filters['type'])) {
            $query .= " AND ct.type = :type";
            $params['type'] = $filters['type'];
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

    public function getBalanceSummary($filters = []) {
        $query = "SELECT ca.id, ca.code, ca.title, cg.name AS group_name,
                         (SELECT SUM(CASE WHEN type IN ('sale', 'payment') THEN amount ELSE -amount END)
                          FROM customer_transactions WHERE customer_id = ca.id) AS balance
                  FROM customer_accounts ca
                  LEFT JOIN customer_groups cg ON ca.group_id = cg.id
                  WHERE 1=1";
        $params = [];

        if (!empty($filters['group_id'])) {
            $query .= " AND ca.group_id = :group_id";
            $params['group_id'] = $filters['group_id'];
        }
        if (!empty($filters['balance_type'])) {
            if ($filters['balance_type'] === 'positive') {
                $query .= " AND (SELECT SUM(CASE WHEN type IN ('sale', 'payment') THEN amount ELSE -amount END)
                           FROM customer_transactions WHERE customer_id = ca.id) > 0";
            } elseif ($filters['balance_type'] === 'negative') {
                $query .= " AND (SELECT SUM(CASE WHEN type IN ('sale', 'payment') THEN amount ELSE -amount END)
                           FROM customer_transactions WHERE customer_id = ca.id) < 0";
            }
        }

        return $this->db->query($query, $params);
    }
}
?>