<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Sol navbar bağlantıları -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    <!-- Logo -->
    <a href="<?php echo url(''); ?>" class="navbar-brand">
        <img src="<?php echo asset('images/logo.png'); ?>" alt="<?php echo APP_NAME; ?> Logo" class="brand-image">
        <span class="brand-text"><?php echo APP_NAME; ?></span>
    </a>
    <!-- Sağ navbar bağlantıları -->
    <ul class="navbar-nav ml-auto">
        <!-- Kullanıcı menüsü -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-user"></i>
                <?php echo $session->get('user_email', 'Misafir'); ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="<?php echo url('user/profile'); ?>" class="dropdown-item">
                    <i class="fas fa-user-circle mr-2"></i> Profil
                </a>
                <div class="dropdown-divider"></div>
                <a href="<?php echo url('auth/logout'); ?>" class="dropdown-item">
                    <i class="fas fa-sign-out-alt mr-2"></i> Çıkış Yap
                </a>
            </div>
        </li>
    </ul>
</nav>