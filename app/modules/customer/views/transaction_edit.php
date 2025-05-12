<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-exchange-alt mr-2"></i>İşlem Düzenle</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo url('customer/transactionEdit/' . $transaction['id']); ?>">
            <div class="form-group">
                <label for="customer_id">Müşteri</label>
                <select class="form-control select2" id="customer_id" name="customer_id" required>
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?php echo $customer['id']; ?>" <?php echo $transaction['customer_id'] == $customer['id'] ? 'selected' : ''; ?>>
                            <?php echo sanitize($customer['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="type">İşlem Türü</label>
                <select class="form-control" id="type" name="type" required>
                    <option value="purchase" <?php echo $transaction['type'] === 'purchase' ? 'selected' : ''; ?>>Alış</option>
                    <option value="sale" <?php echo $transaction['type'] === 'sale' ? 'selected' : ''; ?>>Satış</option>
                    <option value="payment" <?php echo $transaction['type'] === 'payment' ? 'selected' : ''; ?>>Ödeme</option>
                    <option value="collection" <?php echo $transaction['type'] === 'collection' ? 'selected' : ''; ?>>Tahsilat</option>
                </select>
            </div>
            <div class="form-group">
                <label for="amount">Tutar</label>
                <input type="number" step="0.01" class="izhou" id="amount" name="amount" value="<?php echo $transaction['amount']; ?>" required>
            </div>
            <div class="form-group">
                <label for="currency">Para Birimi</label>
                <select class="form-control" id="currency" name="currency" required>
                    <option value="TL" <?php echo $transaction['currency'] === 'TL' ? 'selected' : ''; ?>>TL</option>
                    <option value="USD" <?php echo $transaction['currency'] === 'USD' ? 'selected' : ''; ?>>USD</option>
                    <option value="EUR" <?php echo $transaction['currency'] === 'EUR' ? 'selected' : ''; ?>>EUR</option>
                </select>
            </div>
            <div class="form-group">
                <label for="transaction_date">İşlem Tarihi</label>
                <input type="datetime-local" class="form-control" id="transaction_date" name="transaction_date" value="<?php echo date('Y-m-d\TH:i', strtotime($transaction['transaction_date'])); ?>" required>
            </div>
            <div class="form-group">
                <label for="invoice_no">Fatura No</label>
                <input type="text" class="form-control" id="invoice_no" name="invoice_no" value="<?php echo sanitize($transaction['invoice_no'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="invoice_address_id">Fatura Adresi</label>
                <select class="form-control select2" id="invoice_address_id" name="invoice_address_id">
                    <option value="">Seçiniz</option>
                    <?php foreach ($addresses as $address): ?>
                        <option value="<?php echo $address['id']; ?>" <?php echo $transaction['invoice_address_id'] == $address['id'] ? 'selected' : ''; ?>>
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
                        <option value="<?php echo $address['id']; ?>" <?php echo $transaction['delivery_address_id'] == $address['id'] ? 'selected' : ''; ?>>
                            <?php echo sanitize($address['title'] . ' (' . $address['type'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Açıklama</label>
                <textarea class="form-control" id="description" name="description"><?php echo sanitize($transaction['description'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="stock_entry_id">Bağlantılı Stok Girişi</label>
                <input type="number" class="form-control" id="stock_entry_id" name="stock_entry_id" value="<?php echo $transaction['stock_entry_id'] ?? ''; ?>">
            </div>
            <div class="form-group">
                <label for="stock_exit_id">Bağlantılı Stok Çıkışı</label>
                <input type="number" class="form-control" id="stock_exit_id" name="stock_exit_id" value="<?php echo $transaction['stock_exit_id'] ?? ''; ?>">
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Kaydet</button>
        </form>
    </div>
</div>
<script>
$(document).ready(function() {
    $('.select2').select2();
    $('#customer_id').change(function() {
        const customerId = $(this).val();
        $.get('<?php echo url('customer/getAddresses'); ?>?customer_id=' + customerId, function(data) {
            $('#invoice_address_id').html('<option value="">Seçiniz</option>' + data);
            $('#delivery_address_id').html('<option value="">Seçiniz</option>' + data);
        });
    });
});
</script>