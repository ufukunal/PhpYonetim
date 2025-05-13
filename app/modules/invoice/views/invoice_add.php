<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-invoice mr-2"></i>Fatura Ekle</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo url('invoice/add'); ?>">
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
                        <label for="customer_id">Müşteri</label>
                        <select class="form-control select2" id="customer_id" name="customer_id" required>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?php echo $customer['id']; ?>"><?php echo sanitize($customer['title']); ?></option>
                            <?php endforeach; ?>
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
                            <!-- AJAX ile dinamik doldurulacak -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="delivery_address_id">Sevk Adresi</label>
                        <select class="form-control select2" id="delivery_address_id" name="delivery_address_id">
                            <option value="">Seçiniz</option>
                            <!-- AJAX ile dinamik doldurulacak -->
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <h5>Fatura Kalemleri</h5>
                    <div id="item-container">
                        <div class="card mb-2 item-card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Ürün</label>
                                    <select class="form-control select2" name="items[0][product_id]" required>
                                        <?php foreach ($products as $product): ?>
                                            <option value="<?php echo $product['id']; ?>" data-price="<?php echo $product['unit_price'] ?? 0; ?>">
                                                <?php echo sanitize($product['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Miktar</label>
                                    <input type="number" step="0.01" class="form-control item-quantity" name="items[0][quantity]" required>
                                </div>
                                <div class="form-group">
                                    <label>Birim Fiyat</label>
                                    <input type="number" step="0.01" class="form-control item-unit-price" name="items[0][unit_price]" required>
                                </div>
                                <div class="form-group">
                                    <label>KDV Oranı (%)</label>
                                    <input type="number" step="0.01" class="form-control item-tax-rate" name="items[0][tax_rate]" value="0.00">
                                </div>
                                <div class="form-group">
                                    <label>İskonto (%)</label>
                                    <input type="number" step="0.01" class="form-control item-discount" name="items[0][discount]" value="0.00">
                                </div>
                                <div class="form-group">
                                    <label>Toplam</label>
                                    <input type="number" step="0.01" class="form-control item-total" name="items[0][total]" readonly>
                                </div>
                                <button type="button" class="btn btn-danger btn-sm remove-item"><i class="fas fa-trash"></i> Sil</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mb-3" id="add-item"><i class="fas fa-plus"></i> Kalem Ekle</button>
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
                        <label for="status">Durum</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="pending">Beklemede</option>
                            <option value="paid">Ödendi</option>
                            <option value="partially_paid">Kısmen Ödendi</option>
                            <option value="canceled">İptal</option>
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
    let itemIndex = 1;

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
        $(this).closest('.item-card').remove();
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
});
</script>