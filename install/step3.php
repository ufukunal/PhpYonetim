<?php
session_start();

if (!isset($_SESSION['db_config']) || !isset($_SESSION['sys_config'])) {
    header('Location: index.php?step=1');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jwt_secret = bin2hex(random_bytes(32)); // Rastgele JWT secret oluştur
    $rate_limit = (int)($_POST['rate_limit'] ?? 100);
    $allowed_origins = $_POST['allowed_origins'] ?? '*';

    // api.php dosyasını oluştur
    $api_content = "<?php\nreturn [\n";
    $api_content .= "    'jwt_secret' => '$jwt_secret',\n";
    $api_content .= "    'rate_limit' => $rate_limit,\n";
    $api_content .= "    'rate_limit_window' => 60,\n";
    $api_content .= "    'api_version' => 'v1',\n";
    $api_content .= "    'log_path' => __DIR__ . '/../logs/api.log',\n";
    $api_content .= "    'cors' => [\n";
    $api_content .= "        'allowed_origins' => ['$allowed_origins'],\n";
    $api_content .= "        'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],\n";
    $api_content .= "        'allowed_headers' => ['X-API-Key', 'Authorization', 'Content-Type']\n";
    $api_content .= "    ]\n";
    $api_content .= "];\n?>";

    // database.php dosyasını oluştur
    $db_config = $_SESSION['db_config'];
    $db_content = "<?php\nreturn [\n";
    $db_content .= "    'host' => '{$db_config['host']}',\n";
    $db_content .= "    'database' => '{$db_config['database']}',\n";
    $db_content .= "    'username' => '{$db_config['username']}',\n";
    $db_content .= "    'password' => '{$db_config['password']}',\n";
    $db_content .= "    'charset' => '{$db_config['charset']}'\n";
    $db_content .= "];\n?>";

    if (file_put_contents('../config/api.php', $api_content) && file_put_contents('../config/database.php', $db_content)) {
        chmod('../config/api.php', 0600);
        chmod('../config/database.php', 0600);
        // Kurulum dizinini sil
        array_map('unlink', glob(__DIR__ . '/*.*'));
        rmdir(__DIR__);
        header('Location: finish.php');
        exit;
    } else {
        $error = 'api.php veya database.php dosyası oluşturulamadı. Yazma izinlerini kontrol edin.';
    }
}
?>

<form method="POST" action="">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <div class="form-group">
        <label for="rate_limit">Dakikada Maksimum İstek</label>
        <input type="number" class="form-control" id="rate_limit" name="rate_limit" value="100" required>
        <small class="form-text text-muted">API için dakikada izin verilen istek sayısı</small>
    </div>
    <div class="form-group">
        <label for="allowed_origins">İzin Verilen Domain’ler</label>
        <input type="text" class="form-control" id="allowed_origins" name="allowed_origins" value="*" required>
        <small class="form-text text-muted">API’ye erişmesine izin verilen domain’ler (örn: https://istemci.alanadiniz.com veya *)</small>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-check mr-2"></i>Kurulum Tamamla</button>
</form>