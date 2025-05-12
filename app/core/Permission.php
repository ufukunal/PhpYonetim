<?php
class Permission {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Tüm izinleri getirir
     * @return array
     */
    public function getAllPermissions() {
        $query = "SELECT * FROM permissions";
        return $this->db->query($query);
    }

    /**
     * İzin ekler
     * @param string $name
     * @param string $description
     * @return bool
     */
    public function addPermission($name, $description) {
        $query = "INSERT INTO permissions (name, description) VALUES (:name, :description)";
        return $this->db->execute($query, [
            'name' => $name,
            'description' => $description
        ]);
    }

    /**
     * İzni siler
     * @param int $id
     * @return bool
     */
    public function deletePermission($id) {
        $query = "DELETE FROM permissions WHERE id = :id";
        return $this->db->execute($query, ['id' => $id]);
    }
}
?>