<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list mr-2"></i>Reçeteler</h3>
        <div class="card-tools">
            <a href="<?php echo url('production/recipe_add'); ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-2"></i>Yeni Reçete
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo url('production/recipe_list'); ?>" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <label>Reçete Kodu</label>
                    <input type="text" name="code" class="form-control" value="<?php echo isset($_GET['code']) ? sanitize($_GET['code']) : ''; ?>">
                </div>
                <div class="col-md-3">
                    <label>Durum</label>
                    <select name="status" class="form-control">
                        <option value="">Tümü</option>
                        <option value="active" <?php echo isset($_GET['status']) && $_GET['status'] == 'active' ? 'selected' : ''; ?>>Aktif</option>
                        <option value="passive" <?php echo isset($_GET['status']) && $_GET['status'] == 'passive' ? 'selected' : ''; ?>>Pasif</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary mt-4"><i class="fas fa-filter mr-2"></i>Filtrele</button>
                </div>
            </div>
        </form>
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>Reçete Kodu</th>
                    <th>Ürün</th>
                    <th>Bileşen Sayısı</th>
                    <th>Durum</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recipes as $recipe): ?>
                    <tr>
                        <td><?php echo sanitize($recipe['code']); ?></td>
                        <td><?php echo sanitize($recipe['product_name']); ?></td>
                        <td><?php echo count($this->recipeModel->getIngredientsByRecipeId($recipe['id'])); ?></td>
                        <td><?php echo $recipe['status'] == 'active' ? 'Aktif' : 'Pasif'; ?></td>
                        <td>
                            <a href="<?php echo url('production/recipe_edit/' . $recipe['id']); ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Düzenle
                            </a>
                            <button class="btn btn-sm btn-danger" onclick="deleteRecipe(<?php echo $recipe['id']); ?>)">
                                <i class="fas fa-trash"></i> Sil
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
function deleteRecipe(id) {
    if (confirm('Reçeteyi silmek istediğinizden emin misiniz?')) {
        $.post('<?php echo url('production/recipe_delete'); ?>', {id: id}, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Reçete silme başarısız.');
            }
        });
    }
}
$(document).ready(function() {
    $('.select2').select2();
});
</script>