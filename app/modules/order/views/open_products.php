<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list mr-2"></i>Açık Sipariş Ürünleri</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo url('order/openProducts'); ?>" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <label>Ürün Kodu</label>
                    <input type="text" name="product_code" class="form-control" value="<?php echo isset($_GET['product_code']) ? sanitize($_GET['product_code']) : ''; ?>">
                </div>
                <div class="col-md-3">
                    <label>Ürün Adı</label>
                    <input type="text" name="product_name" class="form-control" value="<?php echo isset($_GET['product_name']) ? sanitize($_GET['product_name']) : ''; ?>">
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
                    <th>Ürün Kodu</th>
                    <th>Ürün Adı</th>
                    <th>Toplam Miktar</th>
                    <th>Faturalı Miktar</th>
                    <th>Faturalanmamış Miktar</th>
                    <th>Birim</th>
                    <th>Müşteri Sayısı</th>
                    <th>Detay</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo sanitize($product['product_code']); ?></td>
                        <td><?php echo sanitize($product['product_name']); ?></td>
                        <td><?php echo number_format($product['total_quantity'], 2); ?></td>
                        <td><?php echo number_format($product['invoiced_quantity'], 2); ?></td>
                        <td><?php echo number_format($product['uninvoiced_quantity'], 2); ?></td>
                        <td><?php echo sanitize($product['unit']); ?></td>
                        <td><?php echo $product['customer_count']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="showProductOrders(<?php echo $product['product_id']; ?>)">
                                <i class="fas fa-eye"></i> Detay
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="productOrdersModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ürün Sipariş Detayları</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="productOrdersTable">
                    <thead>
                        <tr>
                            <th>Sipariş No</th>
                            <th>Müşteri</th>
                            <th>Miktar</th>
                            <th>Durum</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('.select2').select2();
});

function showProductOrders(product_id) {
    $.get('<?php echo url('order/getOpenOrdersByProduct'); ?>?product_id=' + product_id, function(data) {
        const tbody = $('#productOrdersTable tbody');
        tbody.empty();
        data.forEach(order => {
            tbody.append(`
                <tr>
                    <td>${order.order_no}</td>
                    <td>${order.customer_title}</td>
                    <td>${parseFloat(order.quantity).toFixed(2)}</td>
                    <td>${order.status}</td>
                </tr>
            `);
        });
        $('#productOrdersModal').modal('show');
    });
}
</script>