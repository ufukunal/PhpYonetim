<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-exchange-alt mr-2"></i><?php echo ucfirst($check_note['type'] == 'check' ? 'Çek' : 'Senet'); ?> İşlemleri - <?php echo sanitize($check_note['document_number']); ?></h3>
        <div class="card-tools">
            <a href="<?php echo url('check/addTransaction/' . $check_note['id']); ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-2"></i>Yeni İşlem
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo url('check/transactions/' . $check_note['id']); ?>" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <label>İşlem Tipi</label>
                    <select name="type" class="form-control">
                        <option value="">Tümü</option>
                        <option value="collection" <?php echo isset($_GET['type']) && $_GET['type'] == 'collection' ? 'selected' : ''; ?>>Tahsilat</option>
                        <option value="payment" <?php echo isset($_GET['type']) && $_GET['type'] == 'payment' ? 'selected' : ''; ?>>Ödeme</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Durum</label>
                    <select name="status" class="form-control">
                        <option value="">Tümü</option>
                        <option value="pending" <?php echo isset($_GET['status']) && $_GET['status'] == 'pending' ? 'selected' : ''; ?>>Beklemede</option>
                        <option value="controlled" <?php echo isset($_GET['status']) && $_GET['status'] == 'controlled' ? 'selected' : ''; ?>>Kontrol Edildi</option>
                        <option value="rejected" <?php echo isset($_GET['status']) && $_GET['status'] == 'rejected' ? 'selected' : ''; ?>>Reddedildi</option>
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
                    <th>Tarih</th>
                    <th>Tip</th>
                    <th>Tutar</th>
                    <th>Yöntem</th>
                    <th>Kasa/Banka</th>
                    <th>Açıklama</th>
                    <th>Durum</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?php echo $transaction['transaction_date']; ?></td>
                        <td><?php echo $transaction['type'] == 'collection' ? 'Tahsilat' : 'Ödeme'; ?></td>
                        <td><?php echo number_format($transaction['amount'], 2); ?> <?php echo $transaction['currency']; ?></td>
                        <td><?php echo $transaction['method'] == 'cash' ? 'Nakit' : ($transaction['method'] == 'bank' ? 'Banka' : 'Kasa'); ?></td>
                        <td><?php echo sanitize($transaction['bank_name'] ?? $transaction['cash_register_name'] ?? '-'); ?></td>
                        <td><?php echo sanitize($transaction['description'] ?? '-'); ?></td>
                        <td><?php echo sanitize($transaction['status']); ?></td>
                        <td>
                            <a href="<?php echo url('check/editTransaction/' . $transaction['id']); ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Düzenle
                            </a>
                            <button class="btn btn-sm btn-danger" onclick="deleteTransaction(<?php echo $transaction['id']); ?>)">
                                <i class="fas fa-trash"></i> Sil
                            </button>
                            <?php if ($transaction['status'] == 'pending'): ?>
                                <button class="btn btn-sm btn-success" onclick="controlTransaction(<?php echo $transaction['id']; ?>, 'controlled')">
                                    <i class="fas fa-check"></i> Kontrol Et
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="controlTransaction(<?php echo $transaction['id']; ?>, 'rejected')">
                                    <i class="fas fa-times"></i> Reddet
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
function deleteTransaction(id) {
    if (confirm('İşlemi silmek istediğinizden emin misiniz?')) {
        $.post('<?php echo url('check/deleteTransaction'); ?>', {id: id}, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('İşlem silme başarısız.');
            }
        });
    }
}

function controlTransaction(id, status) {
    if (confirm('İşlemi ' + (status == 'controlled' ? 'kontrol etmek' : 'reddetmek') + ' istediğinizden emin misiniz?')) {
        $.post('<?php echo url('check/controlTransaction'); ?>', {id: id, status: status}, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('İşlem kontrol başarısız.');
            }
        });
    }
}

$(document).ready(function() {
    $('.select2').select2();
});
</script>