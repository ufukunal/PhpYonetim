<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-shopping-cart mr-2"></i>Sipariş Düzenle</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo url('order/edit/' . $order['id']); ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="order_no">Sipariş No</label>
                        <input type="text" class="form-control" id="order_no" name="order_no" value="<?php echo sanitize($order['order_no']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="customer_id">Müşteri</label>
                        <select class="form-control select2" id="customer_id" name="customer_id" required>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?php echo $customer['id']; ?>" <?php echo $order['customer_id'] == $customer['id'] ? 'selected' : ''; ?> data-iskonto1="<?php echo $customer['iskonto1']; ?>" data-iskonto2="<?php echo $customer['iskonto2']; ?>">
                                    <?php echo sanitize($customer['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="order_date">Sipariş Tarihi</label>
                        <input type="datetime-local" class="form-control" id="order_date" name="order_date" value="<?php echo date('Y-m-d\TH:i', strtotime($order['order_date'])); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="delivery_date">Teslim Tarihi</label>
                        <input type="datetime-local" class="form-control" id="delivery_date" name="delivery_date" value="<?php echo $order['delivery_date'] ? date('Y-m-d\TH:i', strtotime($order['delivery_date'])) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="status">Durum</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Beklemede</option>
                            <option value="approved" <?php echo $order['status'] === 'approved' ? 'selected' : ''; ?>>Onaylandı</option>
                            <option value="in_production" <?php echo $order['status'] === 'in_production' ? 'selected' : ''; ?>>Üretimde</option>
                            <option value="ready_for_shipment" <?php echo $order['status'] === 'ready_for_shipment' ? 'selected' : ''; ?>>Sevk Hazır</option>
                            <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Sevk Edildi</option>
                            <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Tamamlandı</option>
                            <option value="canceled" <?php echo $order['status'] === 'canceled' ? 'selected' : ''; ?>>İptal</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <h5>Sipariş Kalemleri</h5>
                    <div id="item-container">
                        <?php foreach ($items as $index => $item): ?>
                            <div class="card mb-2 item-card">
                                <div class="card-body">
                                    <input type="hidden" name="items[<?php echo $index; ?>][id]" value="<?php echo $item['id']; ?>">
                                    <div class="form-group">
                                        <label>Ürün</label>
                                        <select class="form-control select2" name="items[<?php echo $index; ?>][product_id]" required>
                                            <?php foreach ($products as $product): ?>
                                                <option value="<?php echo $product['id']; ?>" <?php echo $item['product_id'] == $product['id'] ? 'selected' : ''; ?> data-price="<?php echo $product['unit_price'] ?? 0; ?>" data-unit="<?php echo $product['unit']; ?>">
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
                        <label for="total_amount">Toplam Tutar</label>
                        <input type="number" step="0.01" class="form-control" id="total_amount" name="total_amount" value="<?php echo $order['total_amount']; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="description">Açıklama</label>
                        <textarea class="form-control" id="description" name="description"><?php echo sanitize($order['description'] ?? ''); ?></textarea>
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
        const iskont1 = $(this).find('option:selected').data('iskonto1') || 0;
        $('.item-discount').val(iskont1); // Varsayılan olarak ilk iskontoyu uygula
        calculateTotals();
    });

    $('#add-item').click(function() {
        const html = `
            <div class="card mb-2 item-card">
                <div class="card-body">
                    <div class="form-group">
                        <label>Ürün</label>
                        <select class="form-control select2" name="items[${itemIndex}][product_id]" required>
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo $product['id']; ?>" data-price="<?php echo $product['unit_price'] ?? 0; ?>" data-unit="<?php echo $product['unit']; ?>">
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
        let total_amount = 0;
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
            total_amount += total;
        });

        $('#total_amount').val(total_amount.toFixed(2));
    }

    calculateTotals();
});
</script>