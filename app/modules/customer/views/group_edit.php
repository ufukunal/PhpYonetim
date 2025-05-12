<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-users mr-2"></i>Grup Düzenle</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo url('customer/groupEdit/' . $group['id']); ?>">
            <div class="form-group">
                <label for="code">Grup Kodu</label>
                <input type="text" class="form-control" id="code" name="code" value="<?php echo sanitize($group['code']); ?>" required>
            </div>
            <div class="form-group">
                <label for="name">Grup Adı</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo sanitize($group['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Açıklama</label>
                <textarea class="form-control" id="description" name="description"><?php echo sanitize($group['description'] ?? ''); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Kaydet</button>
        </form>
    </div>
</div>