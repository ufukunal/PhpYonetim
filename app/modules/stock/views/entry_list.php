<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-arrow-down mr-2"></i>Stok Girişleri</h3>
        <div class="card-tools">
            <a href="<?php echo url('stock/entryAdd'); ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-2"></i>Yeni Giriş
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
                    <th>Fatura No</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($entries as $entry): ?>
                    <tr>
                        <td><?php echo sanitize($entry['product_name']); ?></td>
                        <td><?php echo $entry['entry_date']; ?></td>
                        <td><?php echo $entry['quantity']; ?></td>
                        <td><?php echo sanitize($entry['invoice_no'] ?? '-'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>