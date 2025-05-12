<?php
class CustomerAddressModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function addAddress($data) {
        $query = "INSERT INTO customer_addresses (customer_id, type, title, address, city, country, postal_code)
                  VALUES (:customer_id, :type, :title, :address, :city, :country, :postal_code)";
        $params = [
            'customer_id' => $data['customer_id'],
            'type' => $data['type'],
            'title' => $data['title'],
            'address' => $data['address'],
            'city' => $data['city'] ?? null,
            'country' => $data['country'] ?? null,
            'postal_code' => $data['postal_code'] ?? null
        ];
        return $this->db->execute($query, $params);
    }

    public function updateAddress($id, $data) {
        $query = "UPDATE customer_addresses SET
                  type = :type, title = :title, address = :address, city = :city, country = :country, postal_code = :postal_code
                  WHERE id = :id";
        $params = [
            'id' => $id,
            'type' => $data['type'],
            'title' => $data['title'],
            'address' => $data['address'],
            'city' => $data['city'] ?? null,
            'country' => $data['country'] ?? null,
            'postal_code' => $data['postal_code'] ?? null
        ];
        return $this->db->execute($query, $params);
    }

    public function deleteAddress($id) {
        $query = "DELETE FROM customer_addresses WHERE id = :id";
        return $this->db->execute($query, ['id' => $id]);
    }

    public function getAddressesByCustomer($customer_id) {
        $query = "SELECT * FROM customer_addresses WHERE customer_id = :customer_id";
        return $this->db->query($query, ['customer_id' => $customer_id]);
    }
}
?>