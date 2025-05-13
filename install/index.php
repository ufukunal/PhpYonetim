<?php
session_start();

// Kurulum zaten tamamlandıysa, erişimi engelle
if (file_exists('../config/database.php') && file_exists('../config/config.php') && file_exists('../config/api.php')) {
    header('Location: ../');
    exit;
}

// Adım kontrolü
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
if ($step < 1 || $step > 3) {
    $step = 1;
}

// Gerekli PHP eklentilerini kontrol et
$required_extensions = ['pdo_mysql', 'mbstring', 'json'];
$missing_extensions = [];
foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $missing_extensions[] = $ext;
    }
}
if (!empty($missing_extensions)) {
    die('Eksik PHP eklentileri: ' . implode(', ', $missing_extensions));
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kurulum Sihirbazı</title>
    <link rel="stylesheet" href="install.css">
    <link rel="stylesheet" href="../public/assets/css/adminlte.min.css">
    <link rel="stylesheet" href="../public/assets/css/fontawesome.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary mt-4">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-cog mr-2"></i>Kurulum Sihirbazı</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($step == 1): ?>
                            <h4>Adım 1: Veritabanı Ayarları</h4>
                            <p>Veritabanı bağlantı bilgilerinizi girin.</p>
                            <?php include 'step1.php'; ?>
                        <?php elseif ($step == 2): ?>
                            <h4>Adım 2: Sistem Ayarları</h4>
                            <p>Genel sistem ayarlarını yapılandırın.</p>
                            <?php include 'step2.php'; ?>
                        <?php elseif ($step == 3): ?>
                            <h4>Adım 3: API Ayarları</h4>
                            <p>RestAPI modülü için ayarları yapılandırın.</p>
                            <?php include 'step3.php'; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<script src="../public/assets/js/jquery.min.js"></script>
<script src="../public/assets/js/bootstrap.bundle.min.js"></script>
<script src="../public/assets/js/adminlte.min.js"></script>
<script src="install.js"></script>
</body>
</html>