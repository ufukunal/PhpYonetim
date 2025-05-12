<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-sign-in-alt mr-2"></i>Giriş Yap</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo url('user/login'); ?>">
            <div class="form-group">
                <label for="email">E-posta</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Şifre</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-sign-in-alt mr-2"></i>Giriş</button>
        </form>
    </div>
</div>
