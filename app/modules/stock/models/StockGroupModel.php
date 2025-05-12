<?php
class StockGroupModel {
    private $db;
    private $session;

    public function __construct() {
        $this->db = new Database();
        $this->session = new Session();
    }

    public function addGroup($data) {
        $query = "INSERT INTO stock_groups (code, name, description, created_by)
                  VALUES (:code, :name, :description, :created_by)";
        $params = [
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'],
            'created_by' => $this->session->get('user_id')
        ];
        return $this->db->execute($query, $params);
    }

    public function addSubGroup($data) {
        $query = "INSERT INTO stock_sub_groups (code, name, description, group_id, created_by)
                  VALUES (:code, :name, :description, :group_id, :created_by)";
        $params = [
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'],
            'group_id' => $data['group_id'],
            'created_by' => $this->session->get('user_id')
        ];
        return $this->db->execute($query, $params);
    }

    public function addSubSubGroup($data) {
        $query = "INSERT INTO stock_sub_sub_groups (code, name, description, sub_group_id, created_by)
                  VALUES (:code, :name, :description, :sub_group_id, :created_by)";
        $params = [
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'],
            'sub_group_id' => $data['sub_group_id'],
            'created_by' => $this->session->get('user_id')
        ];
        return $this->db->execute($query, $params);
    }

    public function getAllGroups() {
        $query = "SELECT * FROM stock_groups";
        return $this->db->query($query);
    }

    public function getAllSubGroups($group_id = null) {
        $query = "SELECT * FROM stock_sub_groups";
        $params = [];
        if ($group_id) {
            $query .= " WHERE group_id = :group_id";
            $params['group_id'] = $group_id;
        }
        return $this->db->query($query, $params);
    }

    public function getAllSubSubGroups($sub_group_id = null) {
        $query = "SELECT * FROM stock_sub_sub_groups";
        $params = [];
        if ($sub_group_id) {
            $query .= " WHERE sub_group_id = :sub_group_id";
            $params['sub_group_id'] = $sub_group_id;
        }
        return $this->db->query($query, $params);
    }

    public function updateGroup($id, $data) {
        $query = "UPDATE stock_groups SET code = :code, name = :name, description = :description WHERE id = :id";
        $params = [
            'id' => $id,
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description']
        ];
        return $this->db->execute($query, $params);
    }

    public function updateSubGroup($id, $data) {
        $query = "UPDATE stock_sub_groups SET code = :code, name = :name, description = :description, group_id = :group_id WHERE id = :id";
        $params = [
            'id' => $id,
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'],
            'group_id' => $data['group_id']
        ];
        return $this->db->execute($query, $params);
    }

    public function updateSubSubGroup($id, $data) {
        $query = "UPDATE stock_sub_sub_groups SET code = :code, name = :name, description = :description, sub_group_id = :sub_group_id WHERE id = :id";
        $params = [
            'id' => $id,
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'],
            'sub_group_id' => $data['sub_group_id']
        ];
        return $this->db->execute($query, $params);
    }

    public function deleteGroup($id) {
        $query = "DELETE FROM stock_groups WHERE id = :id";
        return $this->db->execute($query, ['id' => $id]);
    }

    public function deleteSubGroup($id) {
        $query = "DELETE FROM stock_sub_groups WHERE id = :id";
        return $this->db->execute($query, ['id' => $id]);
    }

    public function deleteSubSubGroup($id) {
        $query = "DELETE FROM stock_sub_sub_groups WHERE id = :id";
        return $this->db->execute($query, ['id' => $id]);
    }
}
?>