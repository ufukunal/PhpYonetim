<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-box mr-2"></i>Ürün Düzenle</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo url('stock/edit/' . $product['id']); ?>" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="code">Ürün Kodu</label>
                        <input type="text" class="form-control" id="code" name="code" value="<?php echo sanitize($product['code']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Ürün Adı</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo sanitize($product['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="unit">Birim</label>
                        <select class="form-control" id="unit" name="unit" required>
                            <option value="adet" <?php echo $product['unit'] === 'adet' ? 'selected' : ''; ?>>Adet</option>
                            <option value="kg" <?php echo $product['unit'] === 'kg' ? 'selected' : ''; ?>>Kilogram</option>
                            <option value="litre" <?php echo $product['unit'] === 'litre' ? 'selected' : ''; ?>>Litre</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Stok Miktarı</label>
                        <input type="number" step="0.01" class="form-control" id="quantity" name="quantity" value="<?php echo $product['quantity']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="min_quantity">Minimum Stok</label>
                        <input type="number" step="0.01" class="form-control" id="min_quantity" name="min_quantity" value="<?php echo $product['min_quantity']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="description">Açıklama</label>
                        <textarea class="form-control" id="description" name="description"><?php echo sanitize($product['description']); ?></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="stock_group_id">Stok Grubu</label>
                        <select class="form-control select2" id="stock_group_id" name="stock_group_id" required>
                            <?php foreach ($groups as $group): ?>
                                <option value="<?php echo $group['id']; ?>" <?php echo $product['stock_group_id'] == $group['id'] ? 'selected' : ''; ?>>
                                    <?php echo sanitize($group['name']); ?>
                                </option>
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
                            <?php
                            $current_colors = array_map(fn($attr) => $attr['attribute_value'], array_filter($attributes, fn($attr) => $attr['attribute_type'] === 'color'));
                            ?>
                            <option value="Kırmızı" <?php echo in_array('Kırmızı', $current_colors) ? 'selected' : ''; ?>>Kırmızı</option>
                            <option value="Mavi" <?php echo in_array('Mavi', $current_colors) ? 'selected' : ''; ?>>Mavi</option>
                            <option value="Siyah" <?php echo in_array('Siyah', $current_colors) ? 'selected' : ''; ?>>Siyah</option>
                            <option value="Beyaz" <?php echo in_array('Beyaz', $current_colors) ? 'selected' : ''; ?>>Beyaz</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Boyut (cm)</label>
                        <?php
                        $size = array_filter($attributes, fn($attr) => $attr['attribute_type'] === 'size')[0] ?? null;
                        $size_parts = $size ? explode('x', $size['attribute_value']) : ['', '', ''];
                        ?>
                        <div class="row">
                            <div class="col">
                                <input type="number" step="0.01" class="form-control" name="size_length" value="<?php echo $size_parts[0]; ?>" placeholder="Uzunluk">
                            </div>
                            <div class="col">
                                <input type="number" step="0.01" class="form-control" name="size_width" value="<?php echo $size_parts[1]; ?>" placeholder="Genişlik">
                            </div>
                            <div class="col">
                                <input type="number" step="0.01" class="form-control" name="size_height" value="<?php echo $size_parts[2]; ?>" placeholder="Yükseklik">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="weight">Ağırlık (kg)</label>
                        <?php $weight = array_filter($attributes, fn($attr) => $attr['attribute_type'] === 'weight')[0] ?? null; ?>
                        <input type="number" step="0.01" class="form-control" id="weight" name="weight" value="<?php echo $weight ? $weight['attribute_value'] : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Mevcut Resimler</label>
                        <div class="row">
                            <?php foreach ($images as $image): ?>
                                <div class="col-md-3">
                                    <img src="<?php echo asset('uploads/products/' . $image['image_path']); ?>" class="img-fluid mb-2" style="max-height: 100px;">
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteImage(<?php echo $image['id']; ?>)">
                                        <i class="fas fa-trash"></i> Sil
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="images">Yeni Resimler</label>
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
    function deleteImage(id) {
        if (confirm('Resmi silmek istediğinizden emin misiniz?')) {
            $.post('<?php echo url('stock/deleteImage'); ?>', {id: id}, function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Resim silme başarısız.');
                }
            });
        }
    }
});
</script>