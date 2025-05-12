<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user-tag mr-2"></i>Roller</h3>
        <div class="card-tools">
            <a href="<?php echo url('auth/roleEdit'); ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-2"></i>Yeni Rol
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ad</th>
                    <th>Açıklama</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roles as $role): ?>
                    <tr>
                        <td><?php echo sanitize($role['id']); ?></td>
                        <td><?php echo sanitize($role['name']); ?></td>
                        <td><?php echo sanitize($role['description']); ?></td>
                        <td>
                            <a href="<?php echo url('auth/roleEdit/' . $role['id']); ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Düzenle
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>