<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-invoice-dollar mr-2"></i>Çek/Senet Düzenle</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo url('check/edit/' . $check_note['id']); ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="type">Tür</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="check" <?php echo $check_note['type'] === 'check' ? 'selected' : ''; ?>>Çek</option>
                            <option value="note" <?php echo $check_note['type'] === 'note' ? 'selected' : ''; ?>>Senet</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="document_number">Belge Numarası</label>
                        <input type="text" class="form-control" id="document_number" name="document_number" value="<?php echo sanitize($check_note['document_number']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="customer_id">Müşteri</label>
                        <select class="form-control select2" id="customer_id" name="customer_id" required>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?php echo $customer['id']; ?>" <?php echo $check_note['customer_id'] == $customer['id'] ? 'selected' : ''; ?>>
                                    <?php echo sanitize($customer['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="issue_date">Düzenleme Tarihi</label>
                        <input type="datetime-local" class="form-control" id="issue_date" name="issue_date" value="<?php echo date('Y-m-d\TH:i', strtotime($check_note['issue_date'])); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="due_date">Vade Tarihi</label>
                        <input type="datetime-local" class="form-control" id="due_date" name="due_date" value="<?php echo date('Y-m-d\TH:i', strtotime($check_note['due_date'])); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="amount">Tutar</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="<?php echo $check_note['amount']; ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="currency">Para Birimi</label>
                        <select class="form-control" id="currency" name="currency" required>
                            <option value="TL" <?php echo $check_note['currency'] === 'TL' ? 'selected' : ''; ?>>TL</option>
                            <option value="USD" <?php echo $check_note['currency'] === 'USD' ? 'selected' : ''; ?>>USD</option>
                            <option value="EUR" <?php echo $check_note['currency'] === 'EUR' ? 'selected' : ''; ?>>EUR</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bank_id">Banka</label>
                        <select class="form-control select2" id="bank_id" name="bank_id">
                            <option value="">Seçiniz</option>
                            <?php foreach ($banks as $bank): ?>
                                <option value="<?php echo $bank['id']; ?>" <?php echo $check_note['bank_id'] == $bank['id'] ? 'selected' : ''; ?>>
                                    <?php echo sanitize($bank['name'] . ' (' . $bank['iban'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="check_number">Çek Numarası</label>
                        <input type="text" class="form-control" id="check_number" name="check_number" value="<?php echo sanitize($check_note['check_number'] ?? ''); ?>" <?php echo $check_note['type'] !== 'check' ? 'disabled' : ''; ?>>
                    </div>
                    <div class="form-group">
                        <label for="serial_number">Seri Numarası</label>
                        <input type="text" class="form-control" id="serial_number" name="serial_number" value="<?php echo sanitize($check_note['serial_number'] ?? ''); ?>" <?php echo $check_note['type'] !== 'note' ? 'disabled' : ''; ?>>
                    </div>
                    <div class="form-group">
                        <label for="invoice_id">Fatura</label>
                        <select class="form-control select2" id="invoice_id" name="invoice_id">
                            <option value="">Seçiniz</option>
                            <?php foreach ($invoices as $invoice): ?>
                                <option value="<?php echo $invoice['id']; ?>" <?php echo $check_note['invoice_id'] == $invoice['id'] ? 'selected' : ''; ?>>
                                    <?php echo sanitize($invoice['invoice_no']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="order_id">Sipariş</label>
                        <select class="form-control select2" id="order_id" name="order_id">
                            <option value="">Seçiniz</option>
                            <?php foreach ($orders as $order): ?>
                                <option value="<?php echo $order['id']; ?>" <?php echo $check_note['order_id'] == $order['id'] ? 'selected' : ''; ?>>
                                    <?php echo sanitize($order['order_no']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Açıklama</label>
                        <textarea class="form-control" id="description" name="description"><?php echo sanitize($check_note['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="status">Durum</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="pending" <?php echo $check_note['status'] === 'pending' ? 'selected' : ''; ?>>Beklemede</option>
                            <option value="due" <?php echo $check_note['status'] === 'due' ? 'selected' : ''; ?>>Vadesi Geldi</option>
                            <option value="collected" <?php echo $check_note['status'] === 'collected' ? 'selected' : ''; ?>>Tahsil Edildi</option>
                            <option value="paid" <?php echo $check_note['status'] === 'paid' ? 'selected' : ''; ?>>Ödendi</option>
                            <option value="returned" <?php echo $check_note['status'] === 'returned' ? 'selected' : ''; ?>>İade Edildi</option>
                            <option value="protested" <?php echo $check_note['status'] === 'protested' ? 'selected' : ''; ?>>Protesto Edildi</option>
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
        $('#check_number').prop('disabled', type !== 'check');
        $('#serial_number').prop('disabled', type !== 'note');
    });

    // Form yüklendiğinde mevcut türe göre alanları ayarla
    const initialType = $('#type').val();
    $('#check_number').prop('disabled', initialType !== 'check');
    $('#serial_number').prop('disabled', initialType !== 'note');
});
</script>