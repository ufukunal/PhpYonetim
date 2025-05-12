<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-arrow-up mr-2"></i>Stok Çıkışları</h3>
        <div class="card-tools">
            <a href="<?php echo url('stock/exitAdd'); ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-2"></i>Yeni Çıkış
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>Ürün</th>
                    <th>Tarih</th>
                    <th>Miktar</th>
                    <th>Neden</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($exits as $exit): ?>
                    <tr>
                        <td><?php echo sanitize($exit['product_name']); ?></td>
                        <td><?php echo $exit['exit_date']; ?></td>
                        <td><?php echo $exit['quantity']; ?></td>
                        <td><?php echo sanitize($exit['reason'] ?? '-'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>