<?php
class CheckModel {
    private $db;
    private $session;

    public function __construct() {
        $this->db = new Database();
        $this->session = new Session();
    }

    public function addCheckNote($data) {
        $query = "INSERT INTO checks_notes (type, document_number, customer_id, issue_date, due_date, amount, currency, bank_id, check_number, serial_number, invoice_id, order_id, description, status, created_by)
                  VALUES (:type, :document_number, :customer_id, :issue_date, :due_date, :amount, :currency, :bank_id, :check_number, :serial_number, :invoice_id, :order_id, :description, :status, :created_by)";
        $params = [
            'type' => $data['type'],
            'document_number' => $data['document_number'],
            'customer_id' => $data['customer_id'],
            'issue_date' => $data['issue_date'],
            'due_date' => $data['due_date'],
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'bank_id' => $data['bank_id'] ?? null,
            'check_number' => $data['check_number'] ?? null,
            'serial_number' => $data['serial_number'] ?? null,
            'invoice_id' => $data['invoice_id'] ?? null,
            'order_id' => $data['order_id'] ?? null,
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'created_by' => $this->session->get('user_id')
        ];
        if ($this->db->execute($query, $params)) {
            // Cari bakiyesini güncelle
            $this->updateCustomerBalance($data['customer_id'], $data['amount'], $data['type'] == 'check' ? 'collection' : 'payment', $data['currency']);
            return true;
        }
        return false;
    }

    public function updateCheckNote($id, $data) {
        $old_check_note = $this->getCheckNoteById($id);
        // Eski bakiye etkisini tersine çevir
        $this->updateCustomerBalance($old_check_note['customer_id'], -$old_check_note['amount'], $old_check_note['type'] == 'check' ? 'collection' : 'payment', $old_check_note['currency']);

        $query = "UPDATE checks_notes SET
                  type = :type, document_number = :document_number, customer_id = :customer_id,
                  issue_date = :issue_date, due_date = :due_date, amount = :amount, currency = :currency,
                  bank_id = :bank_id, check_number = :check_number, serial_number = :serial_number,
                  invoice_id = :invoice_id, order_id = :order_id, description = :description, status = :status,
                  updated_by = :updated_by, updated_at = NOW()
                  WHERE id = :id";
        $params = [
            'id' => $id,
            'type' => $data['type'],
            'document_number' => $data['document_number'],
            'customer_id' => $data['customer_id'],
            'issue_date' => $data['issue_date'],
            'due_date' => $data['due_date'],
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'bank_id' => $data['bank_id'] ?? null,
            'check_number' => $data['check_number'] ?? null,
            'serial_number' => $data['serial_number'] ?? null,
            'invoice_id' => $data['invoice_id'] ?? null,
            'order_id' => $data['order_id'] ?? null,
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'updated_by' => $this->session->get('user_id')
        ];
        if ($this->db->execute($query, $params)) {
            // Yeni bakiye etkisini uygula
            $this->updateCustomerBalance($data['customer_id'], $data['amount'], $data['type'] == 'check' ? 'collection' : 'payment', $data['currency']);
            return true;
        }
        return false;
    }

    public function deleteCheckNote($id) {
        $check_note = $this->getCheckNoteById($id);
        $query = "DELETE FROM checks_notes WHERE id = :id AND NOT EXISTS (SELECT 1 FROM check_note_transactions WHERE check_note_id = :id)";
        if ($this->db->execute($query, ['id' => $id])) {
            // Cari bakiyesini tersine çevir
            $this->updateCustomerBalance($check_note['customer_id'], -$check_note['amount'], $check_note['type'] == 'check' ? 'collection' : 'payment', $check_note['currency']);
            return true;
        }
        return false;
    }

    public function getCheckNoteById($id) {
        $query = "SELECT cn.*, ca.title AS customer_title, ba.name AS bank_name
                  FROM checks_notes cn
                  JOIN customer_accounts ca ON cn.customer_id = ca.id
                  LEFT JOIN bank_accounts ba ON cn.bank_id = ba.id
                  WHERE cn.id = :id";
        return $this->db->queryOne($query, ['id' => $id]);
    }

    public function getAllCheckNotes($filters = []) {
        $query = "SELECT cn.*, ca.title AS customer_title, i.invoice_no, o.order_no
                  FROM checks_notes cn
                  JOIN customer_accounts ca ON cn.customer_id = ca.id
                  LEFT JOIN invoices i ON cn.invoice_id = i.id
                  LEFT JOIN orders o ON cn.order_id = o.id
                  WHERE 1=1";
        $params = [];

        if (!empty($filters['type'])) {
            $query .= " AND cn.type = :type";
            $params['type'] = $filters['type'];
        }
        if (!empty($filters['document_number'])) {
            $query .= " AND cn.document_number LIKE :document_number";
            $params['document_number'] = '%' . $filters['document_number'] . '%';
        }
        if (!empty($filters['customer_id'])) {
            $query .= " AND cn.customer_id = :customer_id";
            $params['customer_id'] = $filters['customer_id'];
        }
        if (!empty($filters['status'])) {
            $query .= " AND cn.status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['start_date'])) {
            $query .= " AND cn.due_date >= :start_date";
            $params['start_date'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $query .= " AND cn.due_date <= :end_date";
            $params['end_date'] = $filters['end_date'];
        }

        return $this->db->query($query, $params);
    }

    public function addTransaction($data) {
        $query = "INSERT INTO check_note_transactions (check_note_id, type, amount, currency, transaction_date, method, bank_account_id, cash_register_id, description, status, created_by)
                  VALUES (:check_note_id, :type, :amount, :currency, :transaction_date, :method, :bank_account_id, :cash_register_id, :description, :status, :created_by)";
        $params = [
            'check_note_id' => $data['check_note_id'],
            'type' => $data['type'],
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'transaction_date' => $data['transaction_date'],
            'method' => $data['method'],
            'bank_account_id' => $data['bank_account_id'] ?? null,
            'cash_register_id' => $data['cash_register_id'] ?? null,
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'created_by' => $this->session->get('user_id')
        ];
        if ($this->db->execute($query, $params)) {
            $check_note = $this->getCheckNoteById($data['check_note_id']);
            // Kasa veya banka bakiyesini güncelle
            if ($data['method'] == 'cash_register') {
                $cash_data = [
                    'cash_register_id' => $data['cash_register_id'],
                    'type' => $data['type'] == 'collection' ? 'in' : 'out',
                    'amount' => $data['amount'],
                    'currency' => $data['currency'],
                    'transaction_date' => $data['transaction_date'],
                    'description' => 'Çek/Senet işlemi: ' . $check_note['document_number'],
                    'status' => $data['status']
                ];
                $this->db->execute("INSERT INTO cash_transactions (cash_register_id, type, amount, currency, transaction_date, description, status, created_by)
                                    VALUES (:cash_register_id, :type, :amount, :currency, :transaction_date, :description, :status, :created_by)", 
                                    array_merge($cash_data, ['created_by' => $this->session->get('user_id')]));
                $this->db->execute("UPDATE cash_registers SET balance = balance + (:amount * :multiplier) WHERE id = :cash_register_id", [
                    'amount' => $data['amount'],
                    'multiplier' => $data['type'] == 'collection' ? 1 : -1,
                    'cash_register_id' => $data['cash_register_id']
                ]);
            } elseif ($data['method'] == 'bank') {
                $bank_data = [
                    'bank_account_id' => $data['bank_account_id'],
                    'type' => $data['type'] == 'collection' ? 'in' : 'out',
                    'amount' => $data['amount'],
                    'currency' => $data['currency'],
                    'transaction_date' => $data['transaction_date'],
                    'description' => 'Çek/Senet işlemi: ' . $check_note['document_number'],
                    'status' => $data['status']
                ];
                $this->db->execute("INSERT INTO bank_transactions (bank_account_id, type, amount, currency, transaction_date, description, status, created_by)
                                    VALUES (:bank_account_id, :type, :amount, :currency, :transaction_date, :description, :status, :created_by)", 
                                    array_merge($bank_data, ['created_by' => $this->session->get('user_id')]));
                $this->db->execute("UPDATE bank_accounts SET balance = balance + (:amount * :multiplier) WHERE id = :bank_account_id", [
                    'amount' => $data['amount'],
                    'multiplier' => $data['type'] == 'collection' ? 1 : -1,
                    'bank_account_id' => $data['bank_account_id']
                ]);
            }
            // Çek/senet durumunu güncelle
            $total_collected = $this->db->queryOne("SELECT SUM(amount) AS total FROM check_note_transactions WHERE check_note_id = :check_note_id AND type = :type AND status = 'controlled'", [
                'check_note_id' => $data['check_note_id'],
                'type' => $data['type']
            ])['total'] ?? 0;
            $new_status = $total_collected >= $check_note['amount'] ? ($data['type'] == 'collection' ? 'collected' : 'paid') : 'pending';
            $this->db->execute("UPDATE checks_notes SET status = :status WHERE id = :id", [
                'status' => $new_status,
                'id' => $data['check_note_id']
            ]);
            return true;
        }
        return false;
    }

    public function updateTransaction($id, $data) {
        $old_transaction = $this->getTransactionById($id);
        // Eski işlemi tersine çevir
        if ($old_transaction['method'] == 'cash_register') {
            $this->db->execute("UPDATE cash_registers SET balance = balance + (:amount * :multiplier) WHERE id = :cash_register_id", [
                'amount' => $old_transaction['amount'],
                'multiplier' => $old_transaction['type'] == 'collection' ? -1 : 1,
                'cash_register_id' => $old_transaction['cash_register_id']
            ]);
        } elseif ($old_transaction['method'] == 'bank') {
            $this->db->execute("UPDATE bank_accounts SET balance = balance + (:amount * :multiplier) WHERE id = :bank_account_id", [
                'amount' => $old_transaction['amount'],
                'multiplier' => $old_transaction['type'] == 'collection' ? -1 : 1,
                'bank_account_id' => $old_transaction['bank_account_id']
            ]);
        }

        // Yeni işlemi kaydet
        $query = "UPDATE check_note_transactions SET
                  check_note_id = :check_note_id, type = :type, amount = :amount, currency = :currency,
                  transaction_date = :transaction_date, method = :method, bank_account_id = :bank_account_id,
                  cash_register_id = :cash_register_id, description = :description, status = :status
                  WHERE id = :id";
        $params = [
            'id' => $id,
            'check_note_id' => $data['check_note_id'],
            'type' => $data['type'],
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'transaction_date' => $data['transaction_date'],
            'method' => $data['method'],
            'bank_account_id' => $data['bank_account_id'] ?? null,
            'cash_register_id' => $data['cash_register_id'] ?? null,
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'pending'
        ];
        if ($this->db->execute($query, $params)) {
            $check_note = $this->getCheckNoteById($data['check_note_id']);
            // Kasa veya banka bakiyesini güncelle
            if ($data['method'] == 'cash_register') {
                $this->db->execute("UPDATE cash_registers SET balance = balance + (:amount * :multiplier) WHERE id = :cash_register_id", [
                    'amount' => $data['amount'],
                    'multiplier' => $data['type'] == 'collection' ? 1 : -1,
                    'cash_register_id' => $data['cash_register_id']
                ]);
            } elseif ($data['method'] == 'bank') {
                $this->db->execute("UPDATE bank_accounts SET balance = balance + (:amount * :multiplier) WHERE id = :bank_account_id", [
                    'amount' => $data['amount'],
                    'multiplier' => $data['type'] == 'collection' ? 1 : -1,
                    'bank_account_id' => $data['bank_account_id']
                ]);
            }
            // Çek/senet durumunu güncelle
            $total_collected = $this->db->queryOne("SELECT SUM(amount) AS total FROM check_note_transactions WHERE check_note_id = :check_note_id AND type = :type AND status = 'controlled'", [
                'check_note_id' => $data['check_note_id'],
                'type' => $data['type']
            ])['total'] ?? 0;
            $new_status = $total_collected >= $check_note['amount'] ? ($data['type'] == 'collection' ? 'collected' : 'paid') : 'pending';
            $this->db->execute("UPDATE checks_notes SET status = :status WHERE id = :id", [
                'status' => $new_status,
                'id' => $data['check_note_id']
            ]);
            return true;
        }
        return false;
    }

    public function deleteTransaction($id) {
        $transaction = $this->getTransactionById($id);
        $query = "DELETE FROM check_note_transactions WHERE id = :id";
        if ($this->db->execute($query, ['id' => $id])) {
            if ($transaction['method'] == 'cash_register') {
                $this->db->execute("UPDATE cash_registers SET balance = balance + (:amount * :multiplier) WHERE id = :cash_register_id", [
                    'amount' => $transaction['amount'],
                    'multiplier' => $transaction['type'] == 'collection' ? -1 : 1,
                    'cash_register_id' => $transaction['cash_register_id']
                ]);
            } elseif ($transaction['method'] == 'bank') {
                $this->db->execute("UPDATE bank_accounts SET balance = balance + (:amount * :multiplier) WHERE id = :bank_account_id", [
                    'amount' => $transaction['amount'],
                    'multiplier' => $transaction['type'] == 'collection' ? -1 : 1,
                    'bank_account_id' => $transaction['bank_account_id']
                ]);
            }
            // Çek/senet durumunu güncelle
            $check_note = $this->getCheckNoteById($transaction['check_note_id']);
            $total_collected = $this->db->queryOne("SELECT SUM(amount) AS total FROM check_note_transactions WHERE check_note_id = :check_note_id AND type = :type AND status = 'controlled'", [
                'check_note_id' => $transaction['check_note_id'],
                'type' => $transaction['type']
            ])['total'] ?? 0;
            $new_status = $total_collected >= $check_note['amount'] ? ($transaction['type'] == 'collection' ? 'collected' : 'paid') : 'pending';
            $this->db->execute("UPDATE checks_notes SET status = :status WHERE id = :id", [
                'status' => $new_status,
                'id' => $transaction['check_note_id']
            ]);
            return true;
        }
        return false;
    }

    public function getTransactionById($id) {
        $query = "SELECT cnt.*, cn.document_number, cn.type AS check_note_type
                  FROM check_note_transactions cnt
                  JOIN checks_notes cn ON cnt.check_note_id = cn.id
                  WHERE cnt.id = :id";
        return $this->db->queryOne($query, ['id' => $id]);
    }

    public function getTransactionsByCheckNote($check_note_id, $filters = []) {
        $query = "SELECT cnt.*, ba.name AS bank_name, cr.name AS cash_register_name
                  FROM check_note_transactions cnt
                  LEFT JOIN bank_accounts ba ON cnt.bank_account_id = ba.id
                  LEFT JOIN cash_registers cr ON cnt.cash_register_id = cr.id
                  WHERE cnt.check_note_id = :check_note_id";
        $params = ['check_note_id' => $check_note_id];

        if (!empty($filters['type'])) {
            $query .= " AND cnt.type = :type";
            $params['type'] = $filters['type'];
        }
        if (!empty($filters['status'])) {
            $query .= " AND cnt.status = :status";
            $params['status'] = $filters['status'];
        }

        return $this->db->query($query, $params);
    }

    public function controlTransaction($id, $status) {
        $query = "UPDATE check_note_transactions SET status = :status WHERE id = :id";
        return $this->db->execute($query, ['id' => $id, 'status' => $status]);
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