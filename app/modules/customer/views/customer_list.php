<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user-tie mr-2"></i>Müşteriler</h3>
        <div class="card-tools">
            <a href="<?php echo url('customer/add'); ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-2"></i>Yeni Müşteri
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo url('customer/list'); ?>" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <label>Müşteri Kodu</label>
                    <input type="text" name="code" class="form-control" value="<?php echo isset($_GET['code']) ? sanitize($_GET['code']) : ''; ?>">
                </div>
                <div class="col-md-3">
                    <label>Unvan</label>
                    <input type="text" name="title" class="form-control" value="<?php echo isset($_GET['title']) ? sanitize($_GET['title']) : ''; ?>">
                </div>
                <div class="col-md-3">
                    <label>Müşteri Grubu</label>
                    <select name="group_id" class="form-control select2">
                        <option value="">Tümü</option>
                        <?php foreach ($groups as $group): ?>
                            <option value="<?php echo $group['id']; ?>" <?php echo isset($_GET['group_id']) && $_GET['group_id'] == $group['id'] ? 'selected' : ''; ?>>
                                <?php echo sanitize($group['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary mt-4"><i class="fas fa-filter mr-2"></i>Filtrele</button>
                </div>
            </div>
        </form>
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>Kod</th>
                    <th>Unvan</th>
                    <th>Tür</th>
                    <th>Grup</th>
                    <th>Bakiye</th>
                    <th>Adres Sayısı</th>
                    <th>Yetkili Sayısı</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?php echo sanitize($customer['code']); ?></td>
                        <td><?php echo sanitize($customer['title']); ?></td>
                        <td><?php echo sanitize($customer['type']); ?></td>
                        <td><?php echo sanitize($customer['group_name'] ?? '-'); ?></td>
                        <td><?php echo number_format($customer['balance'] ?? 0, 2); ?> TL</td>
                        <td><?php echo $customer['address_count']; ?></td>
                        <td><?php echo $customer['contact_count']; ?></td>
                        <td>
                            <a href="<?php echo url('customer/edit/' . $customer['id']); ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Düzenle
                            </a>
                            <a href="<?php echo url('customer/detail/' . $customer['id']); ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Detay
                            </a>
                            <button class="btn btn-sm btn-danger" onclick="deleteCustomer(<?php echo $customer['id']); ?>)">
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
function deleteCustomer(id) {
    if (confirm('Müşteriyi silmek istediğinizden emin misiniz?')) {
        $.post('<?php echo url('customer/delete'); ?>', {id: id}, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Müşteri silme başarısız.');
            }
        });
    }
}
$(document).ready(function() {
    $('.select2').select2();
});
</script>