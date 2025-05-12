<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user-circle mr-2"></i>Profil</h3>
    </div>
    <div class="card-body">
        <p><strong>E-posta:</strong> <?php echo sanitize($user['email']); ?></p>
        <p><strong>Ad:</strong> <?php echo sanitize($user['first_name']); ?></p>
        <p><strong>Soyad:</strong> <?php echo sanitize($user['last_name']); ?></p>
        <a href="<?php echo url('user/edit/' . $user['id']); ?>" class="btn btn-primary">
            <i class="fas fa-edit mr-2"></i>Profili Düzenle
        </a>
    </div>
</div>