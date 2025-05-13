<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-university mr-2"></i>Banka Hesabı Düzenle</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo url('bank/edit/' . $bank_account['id']); ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="code">Hesap Kodu</label>
                        <input type="text" class="form-control" id="code" name="code" value="<?php echo sanitize($bank_account['code']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Hesap Adı</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo sanitize($bank_account['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="bank_name">Banka Adı</label>
                        <input type="text" class="form-control" id="bank_name" name="bank_name" value="<?php echo sanitize($bank_account['bank_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="branch_code">Şube Kodu</label>
                        <input type="text" class="form-control" id="branch_code" name="branch_code" value="<?php echo sanitize($bank_account['branch_code'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="branch_name">Şube Adı</label>
                        <input type="text" class="form-control" id="branch_name" name="branch_name" value="<?php echo sanitize($bank_account['branch_name'] ?? ''); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="account_number">Hesap Numarası</label>
                        <input type="text" class="form-control" id="account_number" name="account_number" value="<?php echo sanitize($bank_account['account_number'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="iban">IBAN</label>
                        <input type="text" class="form-control" id="iban" name="iban" value="<?php echo sanitize($bank_account['iban']); ?>" required>
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
                        <label for="branch_id">Şube ID</label>
                        <input type="number" class="form-control" id="branch_id" name="branch_id" value="<?php echo $bank_account['branch_id'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="status">Durum</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="active" <?php echo $bank_account['status'] === 'active' ? 'selected' : ''; ?>>Aktif</option>
                            <option value="passive" <?php echo $bank_account['status'] === 'passive' ? 'selected' : ''; ?>>Pasif</option>
                        </select>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Kaydet</button>
        </form>
    </div>
</div>