<?php
class Database {
    private $pdo;
    private $host;
    private $dbname;
    private $username;
    private $password;

    public function __construct() {
        // Config dosyasından ayarları al
        $this->host = DB_HOST;
        $this->dbname = DB_NAME;
        $this->username = DB_USER;
        $this->password = DB_PASS;

        try {
            $this->pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage(), 3, BASE_PATH . '/logs/error.log');
            die("Veritabanı bağlantı hatası. Lütfen sistem yöneticinize başvurun.");
        }
    }

    public function getConnection() {
        return $this->pdo;
    }

    public function query($query, $params = []) {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Query failed: " . $e->getMessage(), 3, BASE_PATH . '/logs/error.log');
            return [];
        }
    }

    public function queryOne($query, $params = []) {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Query failed: " . $e->getMessage(), 3, BASE_PATH . '/logs/error.log');
            return null;
        }
    }

    public function execute($query, $params = []) {
        try {
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Execute failed: " . $e->getMessage(), 3, BASE_PATH . '/logs/error.log');
            return false;
        }
    }
}
?>