<?php
class Role {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Tüm rolleri getirir
     * @return array
     */
    public function getAllRoles() {
        $query = "SELECT * FROM roles";
        return $this->db->query($query);
    }

    /**
     * Rol ekler
     * @param string $name
     * @param string $description
     * @return bool
     */
    public function addRole($name, $description) {
        $query = "INSERT INTO roles (name, description) VALUES (:name, :description)";
        return $this->db->execute($query, [
            'name' => $name,
            'description' => $description
        ]);
    }

    /**
     * Rolü siler
     * @param int $id
     * @return bool
     */
    public function deleteRole($id) {
        $query = "DELETE FROM roles WHERE id = :id";
        return $this->db->execute($query, ['id' => $id]);
    }
}
?>