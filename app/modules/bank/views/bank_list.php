<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-university mr-2"></i>Banka Hesapları</h3>
        <div class="card-tools">
            <a href="<?php echo url('bank/add'); ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-2"></i>Yeni Hesap
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo url('bank/list'); ?>" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <label>Hesap Kodu</label>
                    <input type="text" name="code" class="form-control" value="<?php echo isset($_GET['code']) ? sanitize($_GET['code']) : ''; ?>">
                </div>
                <div class="col-md-3">
                    <label>Hesap Adı</label>
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
                    <th>Hesap Kodu</th>
                    <th>Hesap Adı</th>
                    <th>Banka</th>
                    <th>IBAN</th>
                    <th>Para Birimi</th>
                    <th>Bakiye</th>
                    <th>Durum</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bank_accounts as $bank_account): ?>
                    <tr>
                        <td><?php echo sanitize($bank_account['code']); ?></td>
                        <td><?php echo sanitize($bank_account['name']); ?></td>
                        <td><?php echo sanitize($bank_account['bank_name']); ?></td>
                        <td><?php echo sanitize($bank_account['iban']); ?></td>
                        <td><?php echo sanitize($bank_account['currency']); ?></td>
                        <td><?php echo number_format($bank_account['balance'], 2); ?> <?php echo $bank_account['currency']; ?></td>
                        <td><?php echo $bank_account['status'] == 'active' ? 'Aktif' : 'Pasif'; ?></td>
                        <td>
                            <a href="<?php echo url('bank/edit/' . $bank_account['id']); ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Düzenle
                            </a>
                            <button class="btn btn-sm btn-danger" onclick="deleteBankAccount(<?php echo $bank_account['id']); ?>)">
                                <i class="fas fa-trash"></i> Sil
                            </button>
                            <a href="<?php echo url('bank/transactions/' . $bank_account['id']); ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-money-check-alt"></i> İşlemler
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
function deleteBankAccount(id) {
    if (confirm('Banka hesabını silmek istediğinizden emin misiniz?')) {
        $.post('<?php echo url('bank/delete'); ?>', {id: id}, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Banka hesabı silme başarısız.');
            }
        });
    }
}
$(document).ready(function() {
    $('.select2').select2();
});
</script>