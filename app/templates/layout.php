<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - <?php echo isset($title) ? sanitize($title) : 'Dashboard'; ?></title>
    <!-- AdminLTE ve Font Awesome CSS -->
    <link rel="stylesheet" href="<?php echo asset('css/fontawesome.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/adminlte.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/datatables.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/buttons.dataTables.min.css'); ?>"> <!-- Yeni -->
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Üst Menü -->
    <?php require_once APP_PATH . '/templates/header.php'; ?>
    <!-- Yan Menü -->
    <?php require_once APP_PATH . '/templates/sidebar.php'; ?>
    <!-- İçerik Alanı -->
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1><?php echo isset($title) ? sanitize($title) : 'Dashboard'; ?></h1>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <!-- Flash mesajlar -->
                <?php if ($session->getFlash('success')): ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <i class="fas fa-check-circle"></i> <?php echo sanitize($session->getFlash('success')); ?>
                    </div>
                <?php endif; ?>
                <?php if ($session->getFlash('error')): ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <i class="fas fa-exclamation-circle"></i> <?php echo sanitize($session->getFlash('error')); ?>
                    </div>
                <?php endif; ?>
                <!-- Dinamik içerik -->
                <?php require_once $viewFile; ?>
            </div>
        </section>
    </div>
    <!-- Alt Kısım -->
    <?php require_once APP_PATH . '/templates/footer.php'; ?>
</div>
<!-- JS Dosyaları -->
<script src="<?php echo asset('js/jquery.min.js'); ?>"></script>
<script src="<?php echo asset('js/bootstrap.min.js'); ?>"></script>
<script src="<?php echo asset('js/datatables.min.js'); ?>"></script>
<script src="<?php echo asset('js/jszip.min.js'); ?>"></script> <!-- Yeni -->
<script src="<?php echo asset('js/pdfmake.min.js'); ?>"></script> <!-- Yeni -->
<script src="<?php echo asset('js/vfs_fonts.js'); ?>"></script> <!-- Yeni -->
<script src="<?php echo asset('js/datatables.buttons.min.js'); ?>"></script> <!-- Yeni -->
<script src="<?php echo asset('js/buttons.html5.min.js'); ?>"></script> <!-- Yeni -->
<script src="<?php echo asset('js/chart.js'); ?>"></script>
<script src="<?php echo asset('js/adminlte.js'); ?>"></script>
<script src="<?php echo asset('js/scripts.js'); ?>"></script>
</body>
</html>