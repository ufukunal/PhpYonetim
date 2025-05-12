<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-key mr-2"></i>İzinler</h3>
        <div class="card-tools">
            <a href="<?php echo url('auth/permissionEdit'); ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-2"></i>Yeni İzin
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
                <?php foreach ($permissions as $permission): ?>
                    <tr>
                        <td><?php echo sanitize($permission['id']); ?></td>
                        <td><?php echo sanitize($permission['name']); ?></td>
                        <td><?php echo sanitize($permission['description']); ?></td>
                        <td>
                            <a href="<?php echo url('auth/permissionEdit/' . $permission['id']); ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Düzenle
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>