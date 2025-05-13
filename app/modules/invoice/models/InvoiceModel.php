<?php
class InvoiceModel {
    private $db;
    private $session;

    public function __construct() {
        $this->db = new Database();
        $this->session = new Session();
    }

    public function addInvoice($data) {
        $query = "INSERT INTO invoices (invoice_no, type, customer_id, invoice_date, due_date, currency, subtotal, tax_total, discount_total, net_total, invoice_address_id, delivery_address_id, description, status, created_by)
                  VALUES (:invoice_no, :type, :customer_id, :invoice_date, :due_date, :currency, :subtotal, :tax_total, :discount_total, :net_total, :invoice_address_id, :delivery_address_id, :description, :status, :created_by)";
        $params = [
            'invoice_no' => $data['invoice_no'],
            'type' => $data['type'],
            'customer_id' => $data['customer_id'],
            'invoice_date' => $data['invoice_date'],
            'due_date' => $data['due_date'] ?? null,
            'currency' => $data['currency'] ?? 'TL',
            'subtotal' => $data['subtotal'],
            'tax_total' => $data['tax_total'],
            'discount_total' => $data['discount_total'],
            'net_total' => $data['net_total'],
            'invoice_address_id' => $data['invoice_address_id'] ?? null,
            'delivery_address_id' => $data['delivery_address_id'] ?? null,
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'created_by' => $this->session->get('user_id')
        ];
        return $this->db->execute($query, $params);
    }

    public function updateInvoice($id, $data) {
        $query = "UPDATE invoices SET
                  invoice_no = :invoice_no, type = :type, customer_id = :customer_id, invoice_date = :invoice_date,
                  due_date = :due_date, currency = :currency, subtotal = :subtotal, tax_total = :tax_total,
                  discount_total = :discount_total, net_total = :net_total, invoice_address_id = :invoice_address_id,
                  delivery_address_id = :delivery_address_id, description = :description, status = :status,
                  updated_by = :updated_by, updated_at = NOW()
                  WHERE id = :id";
        $params = [
            'id' => $id,
            'invoice_no' => $data['invoice_no'],
            'type' => $data['type'],
            'customer_id' => $data['customer_id'],
            'invoice_date' => $data['invoice_date'],
            'due_date' => $data['due_date'] ?? null,
            'currency' => $data['currency'] ?? 'TL',
            'subtotal' => $data['subtotal'],
            'tax_total' => $data['tax_total'],
            'discount_total' => $data['discount_total'],
            'net_total' => $data['net_total'],
            'invoice_address_id' => $data['invoice_address_id'] ?? null,
            'delivery_address_id' => $data['delivery_address_id'] ?? null,
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'updated_by' => $this->session->get('user_id')
        ];
        return $this->db->execute($query, $params);
    }

    public function deleteInvoice($id) {
        $query = "DELETE FROM invoices WHERE id = :id";
        return $this->db->execute($query, ['id' => $id]);
    }

    public function getInvoiceById($id) {
        $query = "SELECT i.*, c.title AS customer_title, ca_in.title AS invoice_address_title, ca_del.title AS delivery_address_title
                  FROM invoices i
                  JOIN customer_accounts c ON i.customer_id = c.id
                  LEFT JOIN customer_addresses ca_in ON i.invoice_address_id = ca_in.id
                  LEFT JOIN customer_addresses ca_del ON i.delivery_address_id = ca_del.id
                  WHERE i.id = :id";
        return $this->db->queryOne($query, ['id' => $id]);
    }

    public function getAllInvoices($filters = []) {
        $query = "SELECT i.*, c.title AS customer_title
                  FROM invoices i
                  JOIN customer_accounts c ON i.customer_id = c.id
                  WHERE 1=1";
        $params = [];

        if (!empty($filters['invoice_no'])) {
            $query .= " AND i.invoice_no LIKE :invoice_no";
            $params['invoice_no'] = '%' . $filters['invoice_no'] . '%';
        }
        if (!empty($filters['customer_id'])) {
            $query .= " AND i.customer_id = :customer_id";
            $params['customer_id'] = $filters['customer_id'];
        }
        if (!empty($filters['type'])) {
            $query .= " AND i.type = :type";
            $params['type'] = $filters['type'];
        }
        if (!empty($filters['status'])) {
            $query .= " AND i.status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['start_date'])) {
            $query .= " AND i.invoice_date >= :start_date";
            $params['start_date'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $query .= " AND i.invoice_date <= :end_date";
            $params['end_date'] = $filters['end_date'];
        }

        return $this->db->query($query, $params);
    }

    public function updateCustomerBalance($customer_id, $amount, $type, $currency = 'TL') {
        $query = "INSERT INTO customer_transactions (customer_id, type, amount, currency, transaction_date, created_by)
                  VALUES (:customer_id, :type, :amount, :currency, NOW(), :created_by)";
        $params = [
            'customer_id' => $customer_id,
            'type' => $type,
            'amount' => $amount,
            'currency' => $currency,
            'created_by' => $this->session->get('user_id')
        ];
        return $this->db->execute($query, $params);
    }
}
?>