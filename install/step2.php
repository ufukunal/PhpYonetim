<?php
session_start();

if (!isset($_SESSION['db_config'])) {
    header('Location: index.php?step=1');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $base_url = rtrim($_POST['base_url'], '/');
    $timezone = $_POST['timezone'] ?? 'Europe/Istanbul';

    // config.php dosyasını oluştur
    $config_content = "<?php\nreturn [\n";
    $config_content .= "    'base_url' => '$base_url',\n";
    $config_content .= "    'session' => [\n";
    $config_content .= "        'name' => 'app_session',\n";
    $config_content .= "        'lifetime' => 7200,\n";
    $config_content .= "        'secure' => true,\n";
    $config_content .= "        'httponly' => true\n";
    $config_content .= "    ],\n";
    $config_content .= "    'timezone' => '$timezone',\n";
    $config_content .= "    'log_path' => __DIR__ . '/../logs/error.log',\n";
    $config_content .= "    'debug' => false\n";
    $config_content .= "];\n?>";

    if (file_put_contents('../config/config.php', $config_content)) {
        chmod('../config/config.php', 0644);
        $_SESSION['sys_config'] = [
            'base_url' => $base_url,
            'timezone' => $timezone
        ];
        header('Location: index.php?step=3');
        exit;
    } else {
        $error = 'config.php dosyası oluşturulamadı. Yazma izinlerini kontrol edin.';
    }
}
?>

<form method="POST" action="">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <div class="form-group">
        <label for="base_url">Base URL</label>
        <input type="text" class="form-control" id="base_url" name="base_url" value="https://<?php echo htmlspecialchars($_SERVER['HTTP_HOST']); ?>" required>
        <small class="form-text text-muted">Sitenizin tam adresi (örn: https://alanadiniz.com)</small>
    </div>
    <div class="form-group">
        <label for="timezone">Saat Dilimi</label>
        <select class="form-control" id="timezone" name="timezone" required>
            <option value="Europe/Istanbul" selected>Europe/Istanbul</option>
            <option value="UTC">UTC</option>
            <option value="America/New_York">America/New_York</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-arrow-right mr-2"></i>İleri</button>
</form>