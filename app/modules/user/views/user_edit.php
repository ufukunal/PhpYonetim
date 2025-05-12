<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user-edit mr-2"></i>Kullanıcı Düzenle</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo url('user/edit/' . $user['id']); ?>">
            <div class="form-group">
                <label for="email">E-posta</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo sanitize($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Şifre (değiştirmek için doldurun)</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <div class="form-group">
                <label for="first_name">Ad</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo sanitize($user['first_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="last_name">Soyad</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo sanitize($user['last_name']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Kaydet</button>
        </form>
    </div>
</div>