<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-exchange-alt mr-2"></i>İşlem Ekle</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo url('customer/transactionAdd'); ?>">
            <div class="form-group">
                <label for="customer_id">Müşteri</label>
                <select class="form-control select2" id="customer_id" name="customer_id" required>
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?php echo $customer['id']; ?>"><?php echo sanitize($customer['title']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="type">İşlem Türü</label>
                <select class="form-control" id="type" name="type" required>
                    <option value="purchase">Alış</option>
                    <option value="sale">Satış</option>
                    <option value="payment">Ödeme</option>
                    <option value="collection">Tahsilat</option>
                </select>
            </div>
            <div class="form-group">
                <label for="amount">Tutar</label>
                <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
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
                <label for="transaction_date">İşlem Tarihi</label>
                <input type="datetime-local" class="form-control" id="transaction_date" name="transaction_date" required>
            </div>
            <div class="form-group">
                <label for="invoice_no">Fatura No</label>
                <input type="text" class="form-control" id="invoice_no" name="invoice_no">
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
            <div class="form-group">
                <label for="description">Açıklama</label>
                <textarea class="form-control" id="description" name="description"></textarea>
            </div>
            <div class="form-group">
                <label for="stock_entry_id">Bağlantılı Stok Girişi</label>
                <input type="number" class="form-control" id="stock_entry_id" name="stock_entry_id">
            </div>
            <div class="form-group">
                <label for="stock_exit_id">Bağlantılı Stok Çıkışı</label>
                <input type="number" class="form-control" id="stock_exit_id" name="stock_exit_id">
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