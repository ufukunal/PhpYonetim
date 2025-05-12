<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-users mr-2"></i>Müşteri Grupları</h3>
        <div class="card-tools">
            <a href="<?php echo url('customer/groupAdd'); ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-2"></i>Yeni Grup
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>Kod</th>
                    <th>Ad</th>
                    <th>Açıklama</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($groups as $group): ?>
                    <tr>
                        <td><?php echo sanitize($group['code']); ?></td>
                        <td><?php echo sanitize($group['name']); ?></td>
                        <td><?php echo sanitize($group['description'] ?? '-'); ?></td>
                        <td>
                            <a href="<?php echo url('customer/groupEdit/' . $group['id']); ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Düzenle
                            </a>
                            <button class="btn btn-sm btn-danger" onclick="deleteGroup(<?php echo $group['id']); ?>">
                                <i class="fas fa-trash"></i> Sil
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
function deleteGroup(id) {
    if (confirm('Grubu silmek istediğinizden emin misiniz?')) {
        $.post('<?php echo url('customer/groupDelete'); ?>', {id: id}, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Grup silme başarısız.');
            }
        });
    }
}
</script>