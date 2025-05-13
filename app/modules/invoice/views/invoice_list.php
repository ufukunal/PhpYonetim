<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-invoice mr-2"></i>Faturalar</h3>
        <div class="card-tools">
            <a href="<?php echo url('invoice/add'); ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-2"></i>Yeni Fatura
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo url('invoice/list'); ?>" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <label>Fatura No</label>
                    <input type="text" name="invoice_no" class="form-control" value="<?php echo isset($_GET['invoice_no']) ? sanitize($_GET['invoice_no']) : ''; ?>">
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
                    <label>Fatura Türü</label>
                    <select name="type" class="form-control">
                        <option value="">Tümü</option>
                        <option value="sale" <?php echo isset($_GET['type']) && $_GET['type'] == 'sale' ? 'selected' : ''; ?>>Satış</option>
                        <option value="purchase" <?php echo isset($_GET['type']) && $_GET['type'] == 'purchase' ? 'selected' : ''; ?>>Alış</option>
                        <option value="return" <?php echo isset($_GET['type']) && $_GET['type'] == 'return' ? 'selected' : ''; ?>>İade</option>
                        <option value="proforma" <?php echo isset($_GET['type']) && $_GET['type'] == 'proforma' ? 'selected' : ''; ?>>Proforma</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Durum</label>
                    <select name="status" class="form-control">
                        <option value="">Tümü</option>
                        <option value="paid" <?php echo isset($_GET['status']) && $_GET['status'] == 'paid' ? 'selected' : ''; ?>>Ödendi</option>
                        <option value="pending" <?php echo isset($_GET['status']) && $_GET['status'] == 'pending' ? 'selected' : ''; ?>>Beklemede</option>
                        <option value="partially_paid" <?php echo isset($_GET['status']) && $_GET['status'] == 'partially_paid' ? 'selected' : ''; ?>>Kısmen Ödendi</option>
                        <option value="canceled" <?php echo isset($_GET['status']) && $_GET['status'] == 'canceled' ? 'selected' : ''; ?>>İptal</option>
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
                    <th>Fatura No</th>
                    <th>Müşteri</th>
                    <th>Tür</th>
                    <th>Tarih</th>
                    <th>Net Tutar</th>
                    <th>Durum</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoices as $invoice): ?>
                    <tr>
                        <td><?php echo sanitize($invoice['invoice_no']); ?></td>
                        <td><?php echo sanitize($invoice['customer_title']); ?></td>
                        <td><?php echo sanitize($invoice['type']); ?></td>
                        <td><?php echo $invoice['invoice_date']; ?></td>
                        <td><?php echo number_format($invoice['net_total'], 2) . ' ' . $invoice['currency']; ?></td>
                        <td><?php echo sanitize($invoice['status']); ?></td>
                        <td>
                            <a href="<?php echo url('invoice/edit/' . $invoice['id']); ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Düzenle
                            </a>
                            <a href="<?php echo url('invoice/detail/' . $invoice['id']); ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Detay
                            </a>
                            <button class="btn btn-sm btn-danger" onclick="deleteInvoice(<?php echo $invoice['id']); ?>)">
                                <i class="fas fa-trash"></i> Sil
                            </button>
                            <a href="<?php echo url('invoice/print/' . $invoice['id']); ?>" class="btn btn-sm btn-success">
                                <i class="fas fa-print"></i> Yazdır
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
function deleteInvoice(id) {
    if (confirm('Faturayı silmek istediğinizden emin misiniz?')) {
        $.post('<?php echo url('invoice/delete'); ?>', {id: id}, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Fatura silme başarısız.');
            }
        });
    }
}
$(document).ready(function() {
    $('.select2').select2();
});
</script>