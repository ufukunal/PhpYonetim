<?php
class CustomerContactModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function addContact($data) {
        $query = "INSERT INTO customer_contacts (customer_id, name, title, phone, email, note)
                  VALUES (:customer_id, :name, :title, :phone, :email, :note)";
        $params = [
            'customer_id' => $data['customer_id'],
            'name' => $data['name'],
            'title' => $data['title'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'note' => $data['note'] ?? null
        ];
        return $this->db->execute($query, $params);
    }

    public function updateContact($id, $data) {
        $query = "UPDATE customer_contacts SET
                  name = :name, title = :title, phone = :phone, email = :email, note = :note
                  WHERE id = :id";
        $params = [
            'id' => $id,
            'name' => $data['name'],
            'title' => $data['title'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'note' => $data['note'] ?? null
        ];
        return $this->db->execute($query, $params);
    }

    public function deleteContact($id) {
        $query = "DELETE FROM customer_contacts WHERE id = :id";
        return $this->db->execute($query, ['id' => $id]);
    }

    public function getContactsByCustomer($customer_id) {
        $query = "SELECT * FROM customer_contacts WHERE customer_id = :customer_id";
        return $this->db->query($query, ['customer_id' => $customer_id]);
    }
}
?>