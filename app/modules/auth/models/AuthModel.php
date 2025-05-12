<?php
class AuthModel {
    private $role;
    private $permission;

    public function __construct() {
        $this->role = new Role();
        $this->permission = new Permission();
    }

    public function getAllRoles() {
        return $this->role->getAllRoles();
    }

    public function addRole($name, $description) {
        return $this->role->addRole($name, $description);
    }

    public function deleteRole($id) {
        return $this->role->deleteRole($id);
    }

    public function getAllPermissions() {
        return $this->permission->getAllPermissions();
    }

    public function addPermission($name, $description) {
        return $this->permission->addPermission($name, $description);
    }

    public function deletePermission($id) {
        return $this->permission->deletePermission($id);
    }

    public function assignRoleToUser($user_id, $role_id) {
        $query = "INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)";
        $db = new Database();
        return $db->execute($query, ['user_id' => $user_id, 'role_id' => $role_id]);
    }

    public function assignPermissionToRole($role_id, $permission_id) {
        $query = "INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)";
        $db = new Database();
        return $db->execute($query, ['role_id' => $role_id, 'permission_id' => $permission_id]);
    }
}
?>