<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-cash-register mr-2"></i>Kasalar</h3>
        <div class="card-tools">
            <a href="<?php echo url('cash/add'); ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-2"></i>Yeni Kasa
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo url('cash/list'); ?>" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <label>Kasa Kodu</label>
                    <input type="text" name="code" class="form-control" value="<?php echo isset($_GET['code']) ? sanitize($_GET['code']) : ''; ?>">
                </div>
                <div class="col-md-3">
                    <label>Kasa Adı</label>
                    <input type="text" name="name" class="form-control" value="<?php echo isset($_GET['name']) ? sanitize($_GET['name']) : ''; ?>">
                </div>
                <div class="col-md-3">
                    <label>Durum</label>
                    <select name="status" class="form-control">
                        <option value="">Tümü</option>
                        <option value="active" <?php echo isset($_GET['status']) && $_GET['status'] == 'active' ? 'selected' : ''; ?>>Aktif</option>
                        <option value="passive" <?php echo isset($_GET['status']) && $_GET['status'] == 'passive' ? 'selected' : ''; ?>>Pasif</option>
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
                    <th>Kasa Kodu</th>
                    <th>Kasa Adı</th>
                    <th>Para Birimi</th>
                    <th>Bakiye</th>
                    <th>Durum</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cash_registers as $cash_register): ?>
                    <tr>
                        <td><?php echo sanitize($cash_register['code']); ?></td>
                        <td><?php echo sanitize($cash_register['name']); ?></td>
                        <td><?php echo sanitize($cash_register['currency']); ?></td>
                        <td><?php echo number_format($cash_register['balance'], 2); ?> <?php echo $cash_register['currency']; ?></td>
                        <td><?php echo $cash_register['status'] == 'active' ? 'Aktif' : 'Pasif'; ?></td>
                        <td>
                            <a href="<?php echo url('cash/edit/' . $cash_register['id']); ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Düzenle
                            </a>
                            <button class="btn btn-sm btn-danger" onclick="deleteCashRegister(<?php echo $cash_register['id']); ?>)">
                                <i class="fas fa-trash"></i> Sil
                            </button>
                            <a href="<?php echo url('cash/transactions/' . $cash_register['id']); ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-money-bill"></i> İşlemler
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
function deleteCashRegister(id) {
    if (confirm('Kasayı silmek istediğinizden emin misiniz?')) {
        $.post('<?php echo url('cash/delete'); ?>', {id: id}, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Kasa silme başarısız.');
            }
        });
    }
}
$(document).ready(function() {
    $('.select2').select2();
});
</script>