<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-invoice-dollar mr-2"></i>Çek/Senet Listesi</h3>
        <div class="card-tools">
            <a href="<?php echo url('check/add'); ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-2"></i>Yeni Çek/Senet
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo url('check/list'); ?>" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <label>Tür</label>
                    <select name="type" class="form-control">
                        <option value="">Tümü</option>
                        <option value="check" <?php echo isset($_GET['type']) && $_GET['type'] == 'check' ? 'selected' : ''; ?>>Çek</option>
                        <option value="note" <?php echo isset($_GET['type']) && $_GET['type'] == 'note' ? 'selected' : ''; ?>>Senet</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Belge Numarası</label>
                    <input type="text" name="document_number" class="form-control" value="<?php echo isset($_GET['document_number']) ? sanitize($_GET['document_number']) : ''; ?>">
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
                        <option value="due" <?php echo isset($_GET['status']) && $_GET['status'] == 'due' ? 'selected' : ''; ?>>Vadesi Geldi</option>
                        <option value="collected" <?php echo isset($_GET['status']) && $_GET['status'] == 'collected' ? 'selected' : ''; ?>>Tahsil Edildi</option>
                        <option value="paid" <?php echo isset($_GET['status']) && $_GET['status'] == 'paid' ? 'selected' : ''; ?>>Ödendi</option>
                        <option value="returned" <?php echo isset($_GET['status']) && $_GET['status'] == 'returned' ? 'selected' : ''; ?>>İade Edildi</option>
                        <option value="protested" <?php echo isset($_GET['status']) && $_GET['status'] == 'protested' ? 'selected' : ''; ?>>Protesto Edildi</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Başlangıç Vade Tarihi</label>
                    <input type="date" name="start_date" class="form-control" value="<?php echo isset($_GET['start_date']) ? sanitize($_GET['start_date']) : ''; ?>">
                </div>
                <div class="col-md-3">
                    <label>Bitiş Vade Tarihi</label>
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
                    <th>Tür</th>
                    <th>Belge No</th>
                    <th>Müşteri</th>
                    <th>Vade Tarihi</th>
                    <th>Tutar</th>
                    <th>Durum</th>
                    <th>Fatura No</th>
                    <th>Sipariş No</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($check_notes as $check_note): ?>
                    <tr>
                        <td><?php echo $check_note['type'] == 'check' ? 'Çek' : 'Senet'; ?></td>
                        <td><?php echo sanitize($check_note['document_number']); ?></td>
                        <td><?php echo sanitize($check_note['customer_title']); ?></td>
                        <td><?php echo $check_note['due_date']; ?></td>
                        <td><?php echo number_format($check_note['amount'], 2); ?> <?php echo $check_note['currency']; ?></td>
                        <td><?php echo sanitize($check_note['status']); ?></td>
                        <td><?php echo sanitize($check_note['invoice_no'] ?? '-'); ?></td>
                        <td><?php echo sanitize($check_note['order_no'] ?? '-'); ?></td>
                        <td>
                            <a href="<?php echo url('check/edit/' . $check_note['id']); ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Düzenle
                            </a>
                            <button class="btn btn-sm btn-danger" onclick="deleteCheckNote(<?php echo $check_note['id']); ?>)">
                                <i class="fas fa-trash"></i> Sil
                            </button>
                            <a href="<?php echo url('check/transactions/' . $check_note['id']); ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-exchange-alt"></i> İşlemler
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
function deleteCheckNote(id) {
    if (confirm('Çek/Seneti silmek istediğinizden emin misiniz?')) {
        $.post('<?php echo url('check/delete'); ?>', {id: id}, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Çek/Senet silme başarısız.');
            }
        });
    }
}
$(document).ready(function() {
    $('.select2').select2();
});
</script>