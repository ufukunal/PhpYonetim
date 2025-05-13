<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-money-bill mr-2"></i>Kasa İşlemi Ekle</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo url('cash/addTransaction/' . $cash_register['id']); ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="cash_register_id">Hedef Kasa (Virman için)</label>
                        <select class="form-control select2" id="cash_register_id" name="cash_register_id">
                            <option value="">Seçiniz</option>
                            <?php foreach ($cash_registers as $cash): ?>
                                <option value="<?php echo $cash['id']; ?>"><?php echo sanitize($cash['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="type">İşlem Tipi</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="in">Giriş</option>
                            <option value="out">Çıkış</option>
                            <option value="virman">Virman</option>
                            <option value="tahsilat">Tahsilat</option>
                            <option value="odeme">Ödeme</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="amount">Tutar</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                    </div>
                    <div class="form-group">
                        <label for="currency">Para Birimi</label>
                        <select class="form-control" id="currency" name="currency" required>
                            <option value="TL" <?php echo $cash_register['currency'] === 'TL' ? 'selected' : ''; ?>>TL</option>
                            <option value="USD" <?php echo $cash_register['currency'] === 'USD' ? 'selected' : ''; ?>>USD</option>
                            <option value="EUR" <?php echo $cash_register['currency'] === 'EUR' ? 'selected' : ''; ?>>EUR</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="transaction_date">İşlem Tarihi</label>
                        <input type="datetime-local" class="form-control" id="transaction_date" name="transaction_date" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="customer_id">Müşteri</label>
                        <select class="form-control select2" id="customer_id" name="customer_id">
                            <option value="">Seçiniz</option>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?php echo $customer['id']; ?>"><?php echo sanitize($customer['title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="invoice_id">Fatura</label>
                        <select class="form-control select2" id="invoice_id" name="invoice_id">
                            <option value="">Seçiniz</option>
                            <?php foreach ($invoices as $invoice): ?>
                                <option value="<?php echo $invoice['id']; ?>"><?php echo sanitize($invoice['invoice_no']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="order_id">Sipariş</label>
                        <select class="form-control select2" id="order_id" name="order_id">
                            <option value="">Seçiniz</option>
                            <?php foreach ($orders as $order): ?>
                                <option value="<?php echo $order['id']; ?>"><?php echo sanitize($order['order_no']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Açıklama</label>
                        <textarea class="form-control" id="description" name="description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="status">Durum</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="pending">Beklemede</option>
                            <option value="controlled">Kontrol Edildi</option>
                            <option value="rejected">Reddedildi</option>
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

    $('#type').change(function() {
        const type = $(this).val();
        $('#cash_register_id').prop('disabled', type !== 'virman');
    });
});
</script>