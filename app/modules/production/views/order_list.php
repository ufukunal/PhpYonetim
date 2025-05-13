<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-industry mr-2"></i>Üretim Emirleri</h3>
        <div class="card-tools">
            <a href="<?php echo url('production/order_create'); ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-2"></i>Yeni Emir
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo url('production/order_list'); ?>" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <label>Emir Numarası</label>
                    <input type="text" name="order_number" class="form-control" value="<?php echo isset($_GET['order_number']) ? sanitize($_GET['order_number']) : ''; ?>">
                </div>
                <div class="col-md-3">
                    <label>Durum</label>
                    <select name="status" class="form-control">
                        <option value="">Tümü</option>
                        <option value="pending" <?php echo isset($_GET['status']) && $_GET['status'] == 'pending' ? 'selected' : ''; ?>>Beklemede</option>
                        <option value="in_progress" <?php echo isset($_GET['status']) && $_GET['status'] == 'in_progress' ? 'selected' : ''; ?>>Üretimde</option>
                        <option value="completed" <?php echo isset($_GET['status']) && $_GET['status'] == 'completed' ? 'selected' : ''; ?>>Tamamlandı</option>
                        <option value="canceled" <?php echo isset($_GET['status']) && $_GET['status'] == 'canceled' ? 'selected' : ''; ?>>İptal</option>
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
                    <th>Emir Numarası</th>
                    <th>Reçete</th>
                    <th>Ürün</th>
                    <th>Miktar</th>
                    <th>Takip</th>
                    <th>Durum</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo sanitize($order['order_number']); ?></td>
                        <td><?php echo sanitize($order['recipe_code']); ?></td>
                        <td><?php echo sanitize($order['product_name']); ?></td>
                        <td><?php echo number_format($order['quantity'], 2); ?></td>
                        <td><?php echo $order['tracking'] == 'yes' ? 'Evet' : 'Hayır'; ?></td>
                        <td><?php echo sanitize($order['status']); ?></td>
                        <td>
                            <?php if ($order['tracking'] == 'yes' && $order['status'] != 'completed'): ?>
                                <a href="<?php echo url('production/tracking/' . $order['id']); ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-tasks"></i> Takip
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
$(document).ready(function() {
    $('.select2').select2();
});
</script>