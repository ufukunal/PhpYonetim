<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-money-check-alt mr-2"></i>Banka İşlemi Ekle</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo url('bank/addTransaction/' . $bank_account['id']); ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="type">İşlem Tipi</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="in">Giriş</option>
                            <option value="out">Çıkış</option>
                            <option value="havale">Havale</option>
                            <option value="eft">EFT</option>
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
                            <option value="TL" <?php echo $bank_account['currency'] === 'TL' ? 'selected' : ''; ?>>TL</option>
                            <option value="USD" <?php echo $bank_account['currency'] === 'USD' ? 'selected' : ''; ?>>USD</option>
                            <option value="EUR" <?php echo $bank_account['currency'] === 'EUR' ? 'selected' : ''; ?>>EUR</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="transaction_date">İşlem Tarihi</label>
                        <input type="datetime-local" class="form-control" id="transaction_date" name="transaction_date" required>
                    </div>
                    <div class="form-group">
                        <label for="target_bank_account_id">Hedef Banka Hesabı (Virman için)</label>
                        <select class="form-control select2" id="target_bank_account_id" name="target_bank_account_id" disabled>
                            <option value="">Seçiniz</option>
                            <?php foreach ($bank_accounts as $account): ?>
                                <?php if ($account['id'] != $bank_account['id']): ?>
                                    <option value="<?php echo $account['id']; ?>"><?php echo sanitize($account['name'] . ' (' . $account['iban'] . ')'); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="cash_register_id">Hedef Kasa (Kasa-Banka Virmanı için)</label>
                        <select class="form-control select2" id="cash_register_id" name="cash_register_id" disabled>
                            <option value="">Seçiniz</option>
                            <?php foreach ($cash_registers as $cash): ?>
                                <option value="<?php echo $cash['id']; ?>"><?php echo sanitize($cash['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
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
        $('#target_bank_account_id').prop('disabled', type !== 'virman');
        $('#cash_register_id').prop('disabled', type !== 'virman');
    });
});
</script>