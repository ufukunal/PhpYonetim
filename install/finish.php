<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kurulum Tamamlandı</title>
    <link rel="stylesheet" href="install.css">
    <link rel="stylesheet" href="../public/assets/css/adminlte.min.css">
    <link rel="stylesheet" href="../public/assets/css/fontawesome.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <div class="card card-success mt-4">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-check-circle mr-2"></i>Kurulum Tamamlandı</h3>
                    </div>
                    <div class="card-body">
                        <p>Sistem başarıyla kuruldu! Artık yönetici paneline veya API’ye erişebilirsiniz.</p>
                        <a href="../" class="btn btn-primary"><i class="fas fa-home mr-2"></i>Ana Sayfaya Git</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<script src="../public/assets/js/jquery.min.js"></script>
<script src="../public/assets/js/bootstrap.bundle.min.js"></script>
<script src="../public/assets/js/adminlte.min.js"></script>
</body>
</html>