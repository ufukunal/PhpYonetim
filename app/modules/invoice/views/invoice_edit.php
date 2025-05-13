<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-invoice mr-2"></i>Fatura Düzenle</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo url('invoice/edit/' . $invoice['id']); ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="invoice_no">Fatura No</label>
                        <input type="text" class="form-control" id="invoice_no" name="invoice_no" value="<?php echo sanitize($invoice['invoice_no']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="type">Fatura Türü</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="sale" <?php echo $invoice['type'] === 'sale' ? 'selected' : ''; ?>>Satış</option>
                            <option value="purchase" <?php echo $invoice['type'] === 'purchase' ? 'selected' : ''; ?>>Alış</option>
                            <option value="return" <?php echo $invoice['type'] === 'return' ? 'selected' : ''; ?>>İade</option>
                            <option value="proforma" <?php echo $invoice['type'] === 'proforma' ? 'selected' : ''; ?>>Proforma</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="customer_id">Müşteri</label>
                        <select class="form-control select2" id="customer_id" name="customer_id" required>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?php echo $customer['id']; ?>" <?php echo $invoice['customer_id'] == $customer['id'] ? 'selected' : ''; ?>>
                                    <?php echo sanitize($customer['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="invoice_date">Fatura Tarihi</label>
                        <input type="datetime-local" class="form-control" id="invoice_date" name="invoice_date" value="<?php echo date('Y-m-d\TH:i', strtotime($invoice['invoice_date'])); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="due_date">Vade Tarihi</label>
                        <input type="datetime-local" class="form-control" id="due_date" name="due_date" value="<?php echo $invoice['due_date'] ? date('Y-m-d\TH:i', strtotime($invoice['due_date'])) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="currency">Para Birimi</label>
                        <select class="form-control" id="currency" name="currency" required>
                            <option value="TL" <?php echo $invoice['currency'] === 'TL' ? 'selected' : ''; ?>>TL</option>
                            <option value="USD" <?php echo $invoice['currency'] === 'USD' ? 'selected' : ''; ?>>USD</option>
                            <option value="EUR" <?php echo $invoice['currency'] === 'EUR' ? 'selected' : ''; ?>>EUR</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="invoice_address_id">Fatura Adresi</label>
                        <select class="form-control select2" id="invoice_address_id" name="invoice_address_id">
                            <option value="">Seçiniz</option>
                            <?php foreach ($addresses as $address): ?>
                                <option value="<?php echo $address['id']; ?>" <?php echo $invoice['invoice_address_id'] == $address['id'] ? 'selected' : ''; ?>>
                                    <?php echo sanitize($address['title'] . ' (' . $address['type'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="delivery_address_id">Sevk Adresi</label>
                        <select class="form-control select2" id="delivery_address_id" name="delivery_address_id">
                            <option value="">Seçiniz</option>
                            <?php foreach ($addresses as $address): ?>
                                <option value="<?php echo $address['id']; ?>" <?php echo $invoice['delivery_address_id'] == $address['id'] ? 'selected' : ''; ?>>
                                    <?php echo sanitize($address['title'] . ' (' . $address['type'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <h5>Fatura Kalemleri</h5>
                    <div id="item-container">
                        <?php foreach ($items as $index => $item): ?>
                            <div class="card mb-2 item-card">
                                <div class="card-body">
                                    <input type="hidden" name="items[<?php echo $index; ?>][id]" value="<?php echo $item['id']; ?>">
                                    <div class="form-group">
                                        <label>Ürün</label>
                                        <select class="form-control select2" name="items[<?php echo $index; ?>][product_id]" required>
                                            <?php foreach ($products as $product): ?>
                                                <option value="<?php echo $product['id']; ?>" <?php echo $item['product_id'] == $product['id'] ? 'selected' : ''; ?> data-price="<?php echo $product['unit_price'] ?? 0; ?>">
                                                    <?php echo sanitize($product['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Miktar</label>
                                        <input type="number" step="0.01" class="form-control item-quantity" name="items[<?php echo $index; ?>][quantity]" value="<?php echo $item['quantity']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Birim Fiyat</label>
                                        <input type="number" step="0.01" class="form-control item-unit-price" name="items[<?php echo $index; ?>][unit_price]" value="<?php echo $item['unit_price']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>KDV Oranı (%)</label>
                                        <input type="number" step="0.01" class="form-control item-tax-rate" name="items[<?php echo $index; ?>][tax_rate]" value="<?php echo $item['tax_rate']; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>İskonto (%)</label>
                                        <input type="number" step="0.01" class="form-control item-discount" name="items[<?php echo $index; ?>][discount]" value="<?php echo $item['discount']; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Toplam</label>
                                        <input type="number" step="0.01" class="form-control item-total" name="items[<?php echo $index; ?>][total]" value="<?php echo $item['total']; ?>" readonly>
                                    </div>
                                    <button type="button" class="btn btn-danger btn-sm remove-item"><i class="fas fa-trash"></i> Sil</button>
                                    <input type="hidden" name="delete_items[]" class="delete-item-input" value="<?php echo $item['id']; ?>" disabled>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-primary mb-3" id="add-item"><i class="fas fa-plus"></i> Kalem Ekle</button>
                    <div class="form-group">
                        <label for="subtotal">Ara Toplam</label>
                        <input type="number" step="0.01" class="form-control" id="subtotal" name="subtotal" value="<?php echo $invoice['subtotal']; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="tax_total">KDV Toplam</label>
                        <input type="number" step="0.01" class="form-control" id="tax_total" name="tax_total" value="<?php echo $invoice['tax_total']; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="discount_total">İskonto Toplam</label>
                        <input type="number" step="0.01" class="form-control" id="discount_total" name="discount_total" value="<?php echo $invoice['discount_total']; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="net_total">Net Toplam</label>
                        <input type="number" step="0.01" class="form-control" id="net_total" name="net_total" value="<?php echo $invoice['net_total']; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="description">Açıklama</label>
                        <textarea class="form-control" id="description" name="description"><?php echo sanitize($invoice['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="status">Durum</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="pending" <?php echo $invoice['status'] === 'pending' ? 'selected' : ''; ?>>Beklemede</option>
                            <option value="paid" <?php echo $invoice['status'] === 'paid' ? 'selected' : ''; ?>>Ödendi</option>
                            <option value="partially_paid" <?php echo $invoice['status'] === 'partially_paid' ? 'selected' : ''; ?>>Kısmen Ödendi</option>
                            <option value="canceled" <?php echo $invoice['status'] === 'canceled' ? 'selected' : ''; ?>>İptal</option>
                        </select>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Kaydet</button>
        </form>
    </div>
</div>
<script>
$(document).ready(function() {
    $('.select2').select2();
    let itemIndex = <?php echo count($items); ?>;

    $('#customer_id').change(function() {
        const customerId = $(this).val();
        $.get('<?php echo url('customer/getAddresses'); ?>?customer_id=' + customerId, function(data) {
            $('#invoice_address_id').html('<option value="">Seçiniz</option>' + data);
            $('#delivery_address_id').html('<option value="">Seçiniz</option>' + data);
        });
    });

    $('#add-item').click(function() {
        const html = `
            <div class="card mb-2 item-card">
                <div class="card-body">
                    <div class="form-group">
                        <label>Ürün</label>
                        <select class="form-control select2" name="items[${itemIndex}][product_id]" required>
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo $product['id']; ?>" data-price="<?php echo $product['unit_price'] ?? 0; ?>">
                                    <?php echo sanitize($product['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Miktar</label>
                        <input type="number" step="0.01" class="form-control item-quantity" name="items[${itemIndex}][quantity]" required>
                    </div>
                    <div class="form-group">
                        <label>Birim Fiyat</label>
                        <input type="number" step="0.01" class="form-control item-unit-price" name="items[${itemIndex}][unit_price]" required>
                    </div>
                    <div class="form-group">
                        <label>KDV Oranı (%)</label>
                        <input type="number" step="0.01" class="form-control item-tax-rate" name="items[${itemIndex}][tax_rate]" value="0.00">
                    </div>
                    <div class="form-group">
                        <label>İskonto (%)</label>
                        <input type="number" step="0.01" class="form-control item-discount" name="items[${itemIndex}][discount]" value="0.00">
                    </div>
                    <div class="form-group">
                        <label>Toplam</label>
                        <input type="number" step="0.01" class="form-control item-total" name="items[${itemIndex}][total]" readonly>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm remove-item"><i class="fas fa-trash"></i> Sil</button>
                </div>
            </div>`;
        $('#item-container').append(html);
        $('.select2').select2();
        itemIndex++;
    });

    $(document).on('click', '.remove-item', function() {
        const card = $(this).closest('.item-card');
        const idInput = card.find('.delete-item-input');
        if (idInput.val()) {
            idInput.prop('disabled', false); // Silme işlemi için işaretle
        }
        card.remove();
        calculateTotals();
    });

    $(document).on('change', '.item-quantity, .item-unit-price, .item-tax-rate, .item-discount', function() {
        calculateTotals();
    });

    $(document).on('change', '[name^=items][product_id]', function() {
        const price = $(this).find('option:selected').data('price');
        $(this).closest('.item-card').find('.item-unit-price').val(price);
        calculateTotals();
    });

    function calculateTotals() {
        let subtotal = 0, tax_total = 0, discount_total = 0, net_total = 0;
        $('.item-card').each(function() {
            const quantity = parseFloat($(this).find('.item-quantity').val()) || 0;
            const unit_price = parseFloat($(this).find('.item-unit-price').val()) || 0;
            const tax_rate = parseFloat($(this).find('.item-tax-rate').val()) || 0;
            const discount = parseFloat($(this).find('.item-discount').val()) || 0;

            const base_total = quantity * unit_price;
            const discount_amount = base_total * (discount / 100);
            const tax_amount = (base_total - discount_amount) * (tax_rate / 100);
            const total = base_total - discount_amount + tax_amount;

            $(this).find('.item-total').val(total.toFixed(2));
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

    calculateTotals();
});
</script>