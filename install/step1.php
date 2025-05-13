<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = $_POST['host'] ?? '';
    $database = $_POST['database'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Veritabanı bağlantısını test et
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $_SESSION['db_config'] = [
            'host' => $host,
            'database' => $database,
            'username' => $username,
            'password' => $password,
            'charset' => 'utf8mb4'
        ];
        header('Location: index.php?step=2');
        exit;
    } catch (PDOException $e) {
        $error = 'Veritabanı bağlantısı başarısız: ' . $e->getMessage();
    }
}
?>

<form method="POST" action="">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <div class="form-group">
        <label for="host">Veritabanı Sunucusu</label>
        <input type="text" class="form-control" id="host" name="host" value="localhost" required>
    </div>
    <div class="form-group">
        <label for="database">Veritabanı Adı</label>
        <input type="text" class="form-control" id="database" name="database" required>
    </div>
    <div class="form-group">
        <label for="username">Kullanıcı Adı</label>
        <input type="text" class="form-control" id="username" name="username" required>
    </div>
    <div class="form-group">
        <label for="password">Parola</label>
        <input type="password" class="form-control" id="password" name="password">
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-arrow-right mr-2"></i>İleri</button>
</form>