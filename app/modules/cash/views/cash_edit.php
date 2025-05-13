<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-cash-register mr-2"></i>Kasa Düzenle</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo url('cash/edit/' . $cash_register['id']); ?>">
            <div class="form-group">
                <label for="code">Kasa Kodu</label>
                <input type="text" class="form-control" id="code" name="code" value="<?php echo sanitize($cash_register['code']); ?>" required>
            </div>
            <div class="form-group">
                <label for="name">Kasa Adı</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo sanitize($cash_register['name']); ?>" required>
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
                <label for="branch_id">Şube ID</label>
                <input type="number" class="form-control" id="branch_id" name="branch_id" value="<?php echo $cash_register['branch_id'] ?? ''; ?>">
            </div>
            <div class="form-group">
                <label for="status">Durum</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="active" <?php echo $cash_register['status'] === 'active' ? 'selected' : ''; ?>>Aktif</option>
                    <option value="passive" <?php echo $cash_register['status'] === 'passive' ? 'selected' : ''; ?>>Pasif</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Kaydet</button>
        </form>
    </div>
</div>