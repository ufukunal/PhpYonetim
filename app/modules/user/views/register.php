<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user-plus mr-2"></i>Kayıt Ol</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo url('user/register'); ?>">
            <div class="form-group">
                <label for="email">E-posta</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Şifre</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="first_name">Ad</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="last_name">Soyad</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-user-plus mr-2"></i>Kayıt Ol</button>
        </form>
    </div>
</div>