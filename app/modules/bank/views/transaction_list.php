<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-money-check-alt mr-2"></i>Banka İşlemleri - <?php echo sanitize($bank_account['name']); ?></h3>
        <div class="card-tools">
            <a href="<?php echo url('bank/addTransaction/' . $bank_account['id']); ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-2"></i>Yeni İşlem
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo url('bank/transactions/' . $bank_account['id']); ?>" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <label>İşlem Tipi</label>
                    <select name="type" class="form-control">
                        <option value="">Tümü</option>
                        <option value="in" <?php echo isset($_GET['type']) && $_GET['type'] == 'in' ? 'selected' : ''; ?>>Giriş</option>
                        <option value="out" <?php echo isset($_GET['type']) && $_GET['type'] == 'out' ? 'selected' : ''; ?>>Çıkış</option>
                        <option value="havale" <?php echo isset($_GET['type']) && $_GET['type'] == 'havale' ? 'selected' : ''; ?>>Havale</option>
                        <option value="eft" <?php echo isset($_GET['type']) && $_GET['type'] == 'eft' ? 'selected' : ''; ?>>EFT</option>
                        <option value="virman" <?php echo isset($_GET['type']) && $_GET['type'] == 'virman' ? 'selected' : ''; ?>>Virman</option>
                        <option value="tahsilat" <?php echo isset($_GET['type']) && $_GET['type'] == 'tahsilat' ? 'selected' : ''; ?>>Tahsilat</option>
                        <option value="odeme" <?php echo isset($_GET['type']) && $_GET['type'] == 'odeme' ? 'selected' : ''; ?>>Ödeme</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Müşteri</label>
                    <select name="customer_id" class="form-control select2">
                        <option value="">Tümü</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?php echo $customer['id']; ?>" <?php echo isset($_GET['customer_id']) && $_GET['customer_id'] == $customer['id'] ? 'selected' : ''; ?>>
                                <?php echo sanitize($customer['title']); ?>
                            </option>
                        <?php endforeach; ?>
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
                    <label>Başlangıç Tarihi</label>
                    <input type="date" name="start_date" class="form-control" value="<?php echo isset($_GET['start_date']) ? sanitize($_GET['start_date']) : ''; ?>">
                </div>
                <div class="col-md-3">
                    <label>Bitiş Tarihi</label>
                    <input type="date" name="end_date" class="form-control" value="<?php echo isset($_GET['end_date']) ? sanitize($_GET['end_date']) : ''; ?>">
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
                    <th>Müşteri</th>
                    <th>Fatura No</th>
                    <th>Sipariş No</th>
                    <th>Açıklama</th>
                    <th>Durum</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?php echo $transaction['transaction_date']; ?></td>
                        <td><?php echo sanitize($transaction['type']); ?></td>
                        <td><?php echo number_format($transaction['amount'], 2); ?> <?php echo $transaction['currency']; ?></td>
                        <td><?php echo sanitize($transaction['customer_title'] ?? '-'); ?></td>
                        <td><?php echo sanitize($transaction['invoice_no'] ?? '-'); ?></td>
                        <td><?php echo sanitize($transaction['order_no'] ?? '-'); ?></td>
                        <td><?php echo sanitize($transaction['description'] ?? '-'); ?></td>
                        <td><?php echo sanitize($transaction['status']); ?></td>
                        <td>
                            <a href="<?php echo url('bank/editTransaction/' . $transaction['id']); ?>" class="btn btn-sm btn-primary">
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
        $.post('<?php echo url('bank/deleteTransaction'); ?>', {id: id}, function(response) {
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
        $.post('<?php echo url('bank/controlTransaction'); ?>', {id: id, status: status}, function(response) {
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