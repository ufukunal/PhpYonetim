<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-arrow-up mr-2"></i>Stok Çıkışı Ekle</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo url('stock/exitAdd'); ?>">
            <div class="form-group">
                <label for="product_id">Ürün</label>
                <select class="form-control select2" id="product_id" name="product_id" required>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['id']; ?>"><?php echo sanitize($product['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="quantity">Miktar</label>
                <input type="number" step="0.01" class="form-control" id="quantity" name="quantity" required>
            </div>
            <div class="form-group">
                <label for="exit_date">Çıkış Tarihi</label>
                <input type="datetime-local" class="form-control" id="exit_date" name="exit_date" required>
            </div>
            <div class="form-group">
                <label for="reason">Neden</label>
                <textarea class="form-control" id="reason" name="reason"></textarea>
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