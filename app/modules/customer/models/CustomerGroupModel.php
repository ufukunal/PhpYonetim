<?php
class CustomerGroupModel {
    private $db;
    private $session;

    public function __construct() {
        $this->db = new Database();
        $this->session = new Session();
    }

    public function addGroup($data) {
        $query = "INSERT INTO customer_groups (code, name, description, created_by)
                  VALUES (:code, :name, :description, :created_by)";
        $params = [
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'created_by' => $this->session->get('user_id')
        ];
        return $this->db->execute($query, $params);
    }

    public function updateGroup($id, $data) {
        $query = "UPDATE customer_groups SET
                  code = :code, name = :name, description = :description
                  WHERE id = :id";
        $params = [
            'id' => $id,
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null
        ];
        return $this->db->execute($query, $params);
    }

    public function deleteGroup($id) {
        $query = "DELETE FROM customer_groups WHERE id = :id";
        return $this->db->execute($query, ['id' => $id]);
    }

    public function getAllGroups() {
        $query = "SELECT * FROM customer_groups";
        return $this->db->query($query);
    }

    public function getGroupById($id) {
        $query = "SELECT * FROM customer_groups WHERE id = :id";
        return $this->db->queryOne($query, ['id' => $id]);
    }
}
?>