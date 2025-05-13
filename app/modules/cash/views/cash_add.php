<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-cash-register mr-2"></i>Kasa Ekle</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo url('cash/add'); ?>">
            <div class="form-group">
                <label for="code">Kasa Kodu</label>
                <input type="text" class="form-control" id="code" name="code" required>
            </div>
            <div class="form-group">
                <label for="name">Kasa Adı</label>
                <input type="text" class="form-control" id="name" name="name" required>
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
                <label for="branch_id">Şube ID</label>
                <input type="number" class="form-control" id="branch_id" name="branch_id">
            </div>
            <div class="form-group">
                <label for="balance">Açılış Bakiyesi</label>
                <input type="number" step="0.01" class="form-control" id="balance" name="balance" value="0.00">
            </div>
            <div class="form-group">
                <label for="status">Durum</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="active">Aktif</option>
                    <option value="passive">Pasif</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Kaydet</button>
        </form>
    </div>
</div>