<?php
class UserModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function register($email, $password, $first_name, $last_name) {
        $query = "INSERT INTO users (email, password, first_name, last_name) VALUES (:email, :password, :first_name, :last_name)";
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        return $this->db->execute($query, [
            'email' => $email,
            'password' => $hashedPassword,
            'first_name' => $first_name,
            'last_name' => $last_name
        ]);
    }

    public function getUserByEmail($email) {
        $query = "SELECT * FROM users WHERE email = :email";
        return $this->db->queryOne($query, ['email' => $email]);
    }

    public function getAllUsers() {
        $query = "SELECT * FROM users";
        return $this->db->query($query);
    }

    public function updateUser($id, $email, $first_name, $last_name, $password = null) {
        $params = ['id' => $id, 'email' => $email, 'first_name' => $first_name, 'last_name' => $last_name];
        if ($password) {
            $params['password'] = password_hash($password, PASSWORD_DEFAULT);
            $query = "UPDATE users SET email = :email, first_name = :first_name, last_name = :last_name, password = :password WHERE id = :id";
        } else {
            $query = "UPDATE users SET email = :email, first_name = :first_name, last_name = :last_name WHERE id = :id";
        }
        return $this->db->execute($query, $params);
    }

    public function deleteUser($id) {
        $query = "DELETE FROM users WHERE id = :id";
        return $this->db->execute($query, ['id' => $id]);
    }
}
?>