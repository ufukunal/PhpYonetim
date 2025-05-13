<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-invoice mr-2"></i>Fatura Detayı</h3>
        <div class="card-tools">
            <a href="<?php echo url('invoice/print/' . $invoice['id']); ?>" class="btn btn-sm btn-success">
                <i class="fas fa-print mr-2"></i>Yazdır
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Fatura No:</strong> <?php echo sanitize($invoice['invoice_no']); ?></p>
                <p><strong>Müşteri:</strong> <?php echo sanitize($invoice['customer_title']); ?></p>
                <p><strong>Tür:</strong> <?php echo sanitize($invoice['type']); ?></p>
                <p><strong>Fatura Tarihi:</strong> <?php echo $invoice['invoice_date']; ?></p>
                <p><strong>Vade Tarihi:</strong> <?php echo $invoice['due_date'] ?? '-'; ?></p>
                <p><strong>Para Birimi:</strong> <?php echo sanitize($invoice['currency']); ?></p>
                <p><strong>Durum:</strong> <?php echo sanitize($invoice['status']); ?></p>
                <p><strong>Açıklama:</strong> <?php echo sanitize($invoice['description'] ?? '-'); ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Fatura Adresi:</strong> <?php echo sanitize($invoice['invoice_address_title'] ?? '-'); ?></p>
                <p><strong>Sevk Adresi:</strong> <?php echo sanitize($invoice['delivery_address_title'] ?? '-'); ?></p>
                <p><strong>Ara Toplam:</strong> <?php echo number_format($invoice['subtotal'], 2); ?> <?php echo $invoice['currency']; ?></p>
                <p><strong>KDV Toplam:</strong> <?php echo number_format($invoice['tax_total'], 2); ?> <?php echo $invoice['currency']; ?></p>
                <p><strong>İskonto Toplam:</strong> <?php echo number_format($invoice['discount_total'], 2); ?> <?php echo $invoice['currency']; ?></p>
                <p><strong>Net Toplam:</strong> <?php echo number_format($invoice['net_total'], 2); ?> <?php echo $invoice['currency']; ?></p>
            </div>
        </div>
        <h5 class="mt-4">Fatura Kalemleri</h5>
        <?php if (empty($items)): ?>
            <p>Kalem bulunamadı.</p>
        <?php else: ?>
            <table class="table table-bordered data-table">
                <thead>
                    <tr>
                        <th>Ürün</th>
                        <th>Miktar</th>
                        <th>Birim Fiyat</th>
                        <th>KDV (%)</th>
                        <th>İskonto (%)</th>
                        <th>Toplam</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo sanitize($item['product_name']); ?></td>
                            <td><?php echo number_format($item['quantity'], 2); ?></td>
                            <td><?php echo number_format($item['unit_price'], 2); ?> <?php echo $invoice['currency']; ?></td>
                            <td><?php echo number_format($item['tax_rate'], 2); ?></td>
                            <td><?php echo number_format($item['discount'], 2); ?></td>
                            <td><?php echo number_format($item['total'], 2); ?> <?php echo $invoice['currency']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>