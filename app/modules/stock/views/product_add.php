<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-box mr-2"></i>Ürün Ekle</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo url('stock/add'); ?>" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="code">Ürün Kodu</label>
                        <input type="text" class="form-control" id="code" name="code" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Ürün Adı</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="unit">Birim</label>
                        <select class="form-control" id="unit" name="unit" required>
                            <option value="adet">Adet</option>
                            <option value="kg">Kilogram</option>
                            <option value="litre">Litre</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Stok Miktarı</label>
                        <input type="number" step="0.01" class="form-control" id="quantity" name="quantity" value="0">
                    </div>
                    <div class="form-group">
                        <label for="min_quantity">Minimum Stok</label>
                        <input type="number" step="0.01" class="form-control" id="min_quantity" name="min_quantity" value="0">
                    </div>
                    <div class="form-group">
                        <label for="description">Açıklama</label>
                        <textarea class="form-control" id="description" name="description"></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="stock_group_id">Stok Grubu</label>
                        <select class="form-control select2" id="stock_group_id" name="stock_group_id" required>
                            <?php foreach ($groups as $group): ?>
                                <option value="<?php echo $group['id']; ?>"><?php echo sanitize($group['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sub_group_id">Ara Grubu</label>
                        <select class="form-control select2" id="sub_group_id" name="sub_group_id" required>
                            <!-- Dinamik olarak AJAX ile doldurulacak -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sub_sub_group_id">Alt Grubu</label>
                        <select class="form-control select2" id="sub_sub_group_id" name="sub_sub_group_id" required>
                            <!-- Dinamik olarak AJAX ile doldurulacak -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="colors">Renk</label>
                        <select class="form-control select2" id="colors" name="colors[]" multiple>
                            <option value="Kırmızı">Kırmızı</option>
                            <option value="Mavi">Mavi</option>
                            <option value="Siyah">Siyah</option>
                            <option value="Beyaz">Beyaz</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Boyut (cm)</label>
                        <div class="row">
                            <div class="col">
                                <input type="number" step="0.01" class="form-control" name="size_length" placeholder="Uzunluk">
                            </div>
                            <div class="col">
                                <input type="number" step="0.01" class="form-control" name="size_width" placeholder="Genişlik">
                            </div>
                            <div class="col">
                                <input type="number" step="0.01" class="form-control" name="size_height" placeholder="Yükseklik">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="weight">Ağırlık (kg)</label>
                        <input type="number" step="0.01" class="form-control" id="weight" name="weight">
                    </div>
                    <div class="form-group">
                        <label for="images">Resimler</label>
                        <input type="file" class="form-control-file" id="images" name="images[]" multiple accept="image/*">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Kaydet</button>
        </form>
    </div>
</div>
<script>
$(document).ready(function() {
    $('.select2').select2();
    $('#stock_group_id').change(function() {
        $.get('<?php echo url('stock/getSubGroups'); ?>?group_id=' + $(this).val(), function(data) {
            $('#sub_group_id').html(data);
        });
    });
    $('#sub_group_id').change(function() {
        $.get('<?php echo url('stock/getSubSubGroups'); ?>?sub_group_id=' + $(this).val(), function(data) {
            $('#sub_sub_group_id').html(data);
        });
    });
});
</script>