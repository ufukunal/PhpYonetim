<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="<?php echo url(''); ?>" class="brand-link">
        <img src="<?php echo asset('images/logo.png'); ?>" alt="<?php echo APP_NAME; ?> Logo" class="brand-image elevation-3">
        <span class="brand-text font-weight-light"><?php echo APP_NAME; ?></span>
    </a>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="<?php echo url(''); ?>" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <!-- Dinamik modül bağlantıları (modüller yazıldığında eklenecek) -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cubes"></i>
                        <p>Stok <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo url('stock/list'); ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Stok Listesi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo url('stock/entry'); ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Stok Girişi</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</aside>