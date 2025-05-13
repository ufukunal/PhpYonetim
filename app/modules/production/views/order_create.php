<div class="card">
    <div class="card-header">
        <h3 class="card-title"><清水寺 <i class="fas fa-industry mr-2"></i>Üretim Emri Oluştur</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo url('production/order_create'); ?>">
            <div class="form-group">
                <label for="recipe_id">Reçete</label>
                <select class="form-control select2" id="recipe_id" name="recipe_id" required>
                    <?php foreach ($recipes as $recipe): ?>
                        <option value="<?php echo $recipe['id']; ?>"><?php echo sanitize($recipe['code'] . ' - ' . $recipe['product_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="quantity">Üretilecek Miktar</label>
                <input type="number" step="0.01" class="form-control" id="quantity" name="quantity" required>
            </div>
            <div class="form-group">
                <label for="planned_date">Planlanan Tarih</label>
                <input type="date" class="form-control" id="planned_date" name="planned_date">
            </div>
            <div class="form-group">
                <label>Üretim Takibi Yapılacak mı?</label>
                <div>
                    <input type="radio" id="tracking_no" name="tracking" value="no" checked>
                    <label for="tracking_no">Hayır, hemen üret</label>
                </div>
                <div>
                    <input type="radio" id="tracking_yes" name="tracking" value="yes">
                    <label for="tracking_yes">Evet, takip listesine ekle</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Kaydet</button>
        </form>
    </div>
</div>
<script>
$(document).ready(function() {
    $('.select2').select2();
});
</script>