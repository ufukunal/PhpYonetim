<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-shopping-cart mr-2"></i>Sipariş Detayı</h3>
        <div class="card-tools">
            <?php if (!empty($items) && array_reduce($items, fn($carry, $item) => $carry || !$item['invoiced'], false)): ?>
                <a href="<?php echo url('order/invoice/' . $order['id']); ?>" class="btn btn-sm btn-success">
                    <i class="fas fa-file-invoice mr-2"></i>Fatura Oluştur
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Sipariş No:</strong> <?php echo sanitize($order['order_no']); ?></p>
                <p><strong>Müşteri:</strong> <?php echo sanitize($order['customer_title']); ?></p>
                <p><strong>Sipariş Tarihi:</strong> <?php echo $order['order_date']; ?></p>
                <p><strong>Teslim Tarihi:</strong> <?php echo $order['delivery_date'] ?? '-'; ?></p>
                <p><strong>Durum:</strong> <?php echo sanitize($order['status']); ?></p>
                <p><strong>Toplam Tutar:</strong> <?php echo number_format($order['total_amount'], 2); ?> TL</p>
                <p><strong>Açıklama:</strong> <?php echo sanitize($order['description'] ?? '-'); ?></p>
            </div>
        </div>
        <h5 class="mt-4">Sipariş Kalemleri</h5>
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
                        <th>Faturalandırma</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo sanitize($item['product_name']); ?> (<?php echo $item['unit']; ?>)</td>
                            <td><?php echo number_format($item['quantity'], 2); ?></td>
                            <td><?php echo number_format($item['unit_price'], 2); ?> TL</td>
                            <td><?php echo number_format($item['tax_rate'], 2); ?></td>
                            <td><?php echo number_format($item['discount'], 2); ?></td>
                            <td><?php echo number_format($item['total'], 2); ?> TL</td>
                            <td><?php echo $item['invoiced'] ? 'Faturalı' : 'Faturalanmamış'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <h5 class="mt-4">Oluşturulmuş Faturalar</h5>
        <?php if (empty($invoices)): ?>
            <p>Fatura bulunamadı.</p>
        <?php else: ?>
            <table class="table table-bordered data-table">
                <thead>
                    <tr>
                        <th>Fatura No</th>
                        <th>Tarih</th>
                        <th>Tutar</th>
                        <th>Durum</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoices as $invoice): ?>
                        <tr>
                            <td><?php echo sanitize($invoice['invoice_no']); ?></td>
                            <td><?php echo $invoice['invoice_date']; ?></td>
                            <td><?php echo number_format($invoice['net_total'], 2); ?> <?php echo $invoice['currency']; ?></td>
                            <td><?php echo sanitize($invoice['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <?php if (isset($invoice_form) && $invoice_form): ?>
            <h5 class="mt-4">Parçalı Fatura Oluştur</h5>
            <form method="POST" action="<?php echo url('order/invoice/' . $order['id']); ?>">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="invoice_no">Fatura No</label>
                            <input type="text" class="form-control" id="invoice_no" name="invoice_no" required>
                        </div>
                        <div class="form-group">
                            <label for="type">Fatura Türü</label>
                            <select class="form-control" id="type" name="type" required>
                                <option value="sale">Satış</option>
                                <option value="purchase">Alış</option>
                                <option value="return">İade</option>
                                <option value="proforma">Proforma</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="invoice_date">Fatura Tarihi</label>
                            <input type="datetime-local" class="form-control" id="invoice_date" name="invoice_date" required>
                        </div>
                        <div class="form-group">
                            <label for="due_date">Vade Tarihi</label>
                            <input type="datetime-local" class="form-control" id="due_date" name="due_date">
                        </div>
                        <div class="form-group">
                            <label for="currency">Para Birimi</label>
                            <select class="form-control" id="currency" name="currency" required>
                                <option value="TL">TL</option>
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="invoice_address_id">Fatura Adresi</label>
                            <select class="form-control select2" id="invoice_address_id" name="invoice_address_id">
                                <option value="">Seçiniz</option>
                                <?php foreach ($addresses as $address): ?>
                                    <option value="<?php echo $address['id']; ?>"><?php echo sanitize($address['title'] . ' (' . $address['type'] . ')'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="delivery_address_id">Sevk Adresi</label>
                            <select class="form-control select2" id="delivery_address_id" name="delivery_address_id">
                                <option value="">Seçiniz</option>
                                <?php foreach ($addresses as $address): ?>
                                    <option value="<?php echo $address['id']; ?>"><?php echo sanitize($address['title'] . ' (' . $address['type'] . ')'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5>Faturalandırılacak Kalemler</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Seç</th>
                                    <th>Ürün</th>
                                    <th>Miktar</th>
                                    <th>Birim Fiyat</th>
                                    <th>KDV (%)</th>
                                    <th>İskonto (%)</th>
                                    <th>Toplam</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $index => $item): ?>
                                    <?php if (!$item['invoiced']): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="item-checkbox" name="items[<?php echo $index; ?>][selected]" value="1">
                                                <input type="hidden" name="items[<?php echo $index; ?>][order_item_id]" value="<?php echo $item['id']; ?>">
                                                <input type="hidden" name="items[<?php echo $index; ?>][product_id]" value="<?php echo $item['product_id']; ?>">
                                            </td>
                                            <td><?php echo sanitize($item['product_name']); ?></td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control item-quantity" name="items[<?php echo $index; ?>][quantity]" value="<?php echo $item['quantity']; ?>" max="<?php echo $item['quantity']; ?>" disabled>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control item-unit-price" name="items[<?php echo $index; ?>][unit_price]" value="<?php echo $item['unit_price']; ?>" disabled>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control item-tax-rate" name="items[<?php echo $index; ?>][tax_rate]" value="<?php echo $item['tax_rate']; ?>" disabled>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control item-discount" name="items[<?php echo $index; ?>][discount]" value="<?php echo $item['discount']; ?>" disabled>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control item-total" name="items[<?php echo $index; ?>][total]" value="<?php echo $item['total']; ?>" readonly>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <label for="subtotal">Ara Toplam</label>
                            <input type="number" step="0.01" class="form-control" id="subtotal" name="subtotal" readonly>
                        </div>
                        <div class="form-group">
                            <label for="tax_total">KDV Toplam</label>
                            <input type="number" step="0.01" class="form-control" id="tax_total" name="tax_total" readonly>
                        </div>
                        <div class="form-group">
                            <label for="discount_total">İskonto Toplam</label>
                            <input type="number" step="0.01" class="form-control" id="discount_total" name="discount_total" readonly>
                        </div>
                        <div class="form-group">
                            <label for="net_total">Net Toplam</label>
                            <input type="number" step="0.01" class="form-control" id="net_total" name="net_total" readonly>
                        </div>
                        <div class="form-group">
                            <label for="description">Açıklama</label>
                            <textarea class="form-control" id="description" name="description"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="status">Fatura Durumu</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="pending">Beklemede</option>
                                <option value="paid">Ödendi</option>
                                <option value="partially_paid">Kısmen Ödendi</option>
                                <option value="canceled">İptal</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Fatura Oluştur</button>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>
<script>
$(document).ready(function() {
    $('.select2').select2();

    $(document).on('change', '.item-checkbox', function() {
        const row = $(this).closest('tr');
        const isChecked = $(this).is(':checked');
        row.find('.item-quantity, .item-unit-price, .item-tax-rate, .item-discount').prop('disabled', !isChecked);
        calculateInvoiceTotals();
    });

    $(document).on('change', '.item-quantity, .item-unit-price, .item-tax-rate, .item-discount', function() {
        const row = $(this).closest('tr');
        const quantity = parseFloat(row.find('.item-quantity').val()) || 0;
        const unit_price = parseFloat(row.find('.item-unit-price').val()) || 0;
        const tax_rate = parseFloat(row.find('.item-tax-rate').val()) || 0;
        const discount = parseFloat(row.find('.item-discount').val()) || 0;

        const base_total = quantity * unit_price;
        const discount_amount = base_total * (discount / 100);
        const tax_amount = (base_total - discount_amount) * (tax_rate / 100);
        const total = base_total - discount_amount + tax_amount;

        row.find('.item-total').val(total.toFixed(2));
        calculateInvoiceTotals();
    });

    function calculateInvoiceTotals() {
        let subtotal = 0, tax_total = 0, discount_total = 0, net_total = 0;
        $('.item-checkbox:checked').each(function() {
            const row = $(this).closest('tr');
            const quantity = parseFloat(row.find('.item-quantity').val()) || 0;
            const unit_price = parseFloat(row.find('.item-unit-price').val()) || 0;
            const tax_rate = parseFloat(row.find('.item-tax-rate').val()) || 0;
            const discount = parseFloat(row.find('.item-discount').val()) || 0;

            const base_total = quantity * unit_price;
            const discount_amount = base_total * (discount / 100);
            const tax_amount = (base_total - discount_amount) * (tax_rate / 100);
            const total = base_total - discount_amount + tax_amount;

            subtotal += base_total;
            tax_total += tax_amount;
            discount_total += discount_amount;
            net_total += total;
        });

        $('#subtotal').val(subtotal.toFixed(2));
        $('#tax_total').val(tax_total.toFixed(2));
        $('#discount_total').val(discount_total.toFixed(2));
        $('#net_total').val(net_total.toFixed(2));
    }
});
</script>