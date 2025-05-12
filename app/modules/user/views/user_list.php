<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-users mr-2"></i>Kullanıcılar</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>E-posta</th>
                    <th>Ad</th>
                    <th>Soyad</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo sanitize($user['id']); ?></td>
                        <td><?php echo sanitize($user['email']); ?></td>
                        <td><?php echo sanitize($user['first_name']); ?></td>
                        <td><?php echo sanitize($user['last_name']); ?></td>
                        <td>
                            <a href="<?php echo url('user/edit/' . $user['id']); ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Düzenle
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>