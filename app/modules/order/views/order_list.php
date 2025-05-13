<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-shopping-cart mr-2"></i>Siparişler</h3>
        <div class="card-tools">
            <a href="<?php echo url('order/add'); ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-2"></i>Yeni Sipariş
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo url('order/list'); ?>" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <label>Sipariş No</label>
                    <input type="text" name="order_no" class="form-control" value="<?php echo isset($_GET['order_no']) ? sanitize($_GET['order_no']) : ''; ?>">
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
                        <option value="approved" <?php echo isset($_GET['status']) && $_GET['status'] == 'approved' ? 'selected' : ''; ?>>Onaylandı</option>
                        <option value="in_production" <?php echo isset($_GET['status']) && $_GET['status'] == 'in_production' ? 'selected' : ''; ?>>Üretimde</option>
                        <option value="ready_for_shipment" <?php echo isset($_GET['status']) && $_GET['status'] == 'ready_for_shipment' ? 'selected' : ''; ?>>Sevk Hazır</option>
                        <option value="shipped" <?php echo isset($_GET['status']) && $_GET['status'] == 'shipped' ? 'selected' : ''; ?>>Sevk Edildi</option>
                        <option value="completed" <?php echo isset($_GET['status']) && $_GET['status'] == 'completed' ? 'selected' : ''; ?>>Tamamlandı</option>
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
                    <th>Sipariş No</th>
                    <th>Müşteri</th>
                    <th>Tarih</th>
                    <th>Toplam Tutar</th>
                    <th>Durum</th>
                    <th>Faturalandırma</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <?php
                        $items = $this->db->query("SELECT SUM(CASE WHEN invoiced = TRUE THEN quantity ELSE 0 END) AS invoiced_quantity, SUM(quantity) AS total_quantity FROM order_items WHERE order_id = :order_id", ['order_id' => $order['id']])[0];
                        $invoicing_status = ($items['total_quantity'] == 0) ? 'Faturalanmamış' : ($items['invoiced_quantity'] == $items['total_quantity'] ? 'Tamamen Faturalı' : ($items['invoiced_quantity'] > 0 ? 'Kısmen Faturalı' : 'Faturalanmamış'));
                    ?>
                    <tr>
                        <td><?php echo sanitize($order['order_no']); ?></td>
                        <td><?php echo sanitize($order['customer_title']); ?></td>
                        <td><?php echo $order['order_date']; ?></td>
                        <td><?php echo number_format($order['total_amount'], 2); ?> TL</td>
                        <td><?php echo sanitize($order['status']); ?></td>
                        <td><?php echo $invoicing_status; ?></td>
                        <td>
                            <a href="<?php echo url('order/edit/' . $order['id']); ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Düzenle
                            </a>
                            <a href="<?php echo url('order/detail/' . $order['id']); ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Detay
                            </a>
                            <button class="btn btn-sm btn-danger" onclick="deleteOrder(<?php echo $order['id']); ?>)">
                                <i class="fas fa-trash"></i> Sil
                            </button>
                            <?php if ($invoicing_status != 'Tamamen Faturalı'): ?>
                                <a href="<?php echo url('order/invoice/' . $order['id']); ?>" class="btn btn-sm btn-success">
                                    <i class="fas fa-file-invoice"></i> Fatura Oluştur
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
function deleteOrder(id) {
    if (confirm('Siparişi silmek istediğinizden emin misiniz?')) {
        $.post('<?php echo url('order/delete'); ?>', {id: id}, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Sipariş silme başarısız.');
            }
        });
    }
}
$(document).ready(function() {
    $('.select2').select2();
});
</script>