<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-exchange-alt mr-2"></i><?php echo ucfirst($check_note['type'] == 'check' ? 'Çek' : 'Senet'); ?> İşlemi Ekle</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo url('check/addTransaction/' . $check_note['id']); ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="type">İşlem Tipi</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="collection">Tahsilat</option>
                            <option value="payment">Ödeme</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="amount">Tutar</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                    </div>
                    <div class="form-group">
                        <label for="currency">Para Birimi</label>
                        <select class="form-control" id="currency" name="currency" required>
                            <option value="TL" <?php echo $check_note['currency'] === 'TL' ? 'selected' : ''; ?>>TL</option>
                            <option value="USD" <?php echo $check_note['currency'] === 'USD' ? 'selected' : ''; ?>>USD</option>
                            <option value="EUR" <?php echo $check_note['currency'] === 'EUR' ? 'selected' : ''; ?>>EUR</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="transaction_date">İşlem Tarihi</label>
                        <input type="datetime-local" class="form-control" id="transaction_date" name="transaction_date" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="method">Yöntem</label>
                        <select class="form-control" id="method" name="method" required>
                            <option value="cash">Nakit</option>
                            <option value="cash_register">Kasa</option>
                            <option value="bank">Banka</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="cash_register_id">Kasa</label>
                        <select class="form-control select2" id="cash_register_id" name="cash_register_id" disabled>
                            <option value="">Seçiniz</option>
                            <?php foreach ($cash_registers as $cash): ?>
                                <option value="<?php echo $cash['id']; ?>"><?php echo sanitize($cash['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bank_account_id">Banka Hesabı</label>
                        <select class="form-control select2" id="bank_account_id" name="bank_account_id" disabled>
                            <option value="">Seçiniz</option>
                            <?php foreach ($banks as $bank): ?>
                                <option value="<?php echo $bank['id']; ?>"><?php echo sanitize($bank['name'] . ' (' . $bank['iban'] . ')'); ?></option>
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

    $('#method').change(function() {
        const method = $(this).val();
        $('#cash_register_id').prop('disabled', method !== 'cash_register');
        $('#bank_account_id').prop('disabled', method !== 'bank');
    });
});
</script>