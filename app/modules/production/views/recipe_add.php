<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list mr-2"></i>Reçete Ekle</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo url('production/recipe_add'); ?>">
            <div class="form-group">
                <label for="code">Reçete Kodu</label>
                <input type="text" class="form-control" id="code" name="code" required>
            </div>
            <div class="form-group">
                <label for="product_id">Ürün</label>
                <select class="form-control select2" id="product_id" name="product_id" required>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['id']; ?>"><?php echo sanitize($product['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Açıklama</label>
                <textarea class="form-control" id="description" name="description"></textarea>
            </div>
            <div class="form-group">
                <label for="status">Durum</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="active">Aktif</option>
                    <option value="passive">Pasif</option>
                </select>
            </div>
            <div class="form-group">
                <label>Bileşenler</label>
                <div id="ingredients">
                    <div class="ingredient-row">
                        <select class="form-control select2" name="ingredients[0][ingredient_id]" required>
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo $product['id']; ?>"><?php echo sanitize($product['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="number" step="0.01" class="form-control" name="ingredients[0][quantity]" placeholder="Miktar" required>
                        <input type="text" class="form-control" name="ingredients[0][unit]" placeholder="Birim" required>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-primary mt-2" onclick="addIngredientRow()">Bileşen Ekle</button>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Kaydet</button>
        </form>
    </div>
</div>
<script>
function addIngredientRow() {
    const index = document.querySelectorAll('.ingredient-row').length;
    const row = document.createElement('div');
    row.className = 'ingredient-row mt-2';
    row.innerHTML = `
        <select class="form-control select2" name="ingredients[${index}][ingredient_id]" required>
            <?php foreach ($products as $product): ?>
                <option value="<?php echo $product['id']; ?>"><?php echo sanitize($product['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="number" step="0.01" class="form-control" name="ingredients[${index}][quantity]" placeholder="Miktar" required>
        <input type="text" class="form-control" name="ingredients[${index}][unit]" placeholder="Birim" required>
    `;
    document.getElementById('ingredients').appendChild(row);
    $('.select2').select2();
}
$(document).ready(function() {
    $('.select2').select2();
});
</script>