<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user-tag mr-2"></i>Rol Ekle</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo url('auth/roleEdit'); ?>">
            <div class="form-group">
                <label for="name">Rol Adı</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="description">Açıklama</label>
                <textarea class="form-control" id="description" name="description"></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Kaydet</button>
        </form>
    </div>
</div>