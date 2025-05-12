<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-box mr-2"></i>Ürün Detayı</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Kod:</strong> <?php echo sanitize($product['code']); ?></p>
                <p><strong>Ad:</strong> <?php echo sanitize($product['name']); ?></p>
                <p><strong>Birim:</strong> <?php echo sanitize($product['unit']); ?></p>
                <p><strong>Stok Miktarı:</strong> <?php echo $product['quantity']; ?></p>
                <p><strong>Minimum Stok:</strong> <?php echo $product['min_quantity']; ?></p>
                <p><strong>Açıklama:</strong> <?php echo sanitize($product['description']); ?></p>
                <p><strong>Stok Grubu:</strong> <?php echo sanitize($product['group_name']); ?></p>
                <p><strong>Ara Grubu:</strong> <?php echo sanitize($product['sub_group_name']); ?></p>
                <p><strong>Alt Grubu:</strong> <?php echo sanitize($product['sub_sub_group_name']); ?></p>
                <p><strong>Renk:</strong> <?php echo implode(', ', array_map(fn($attr) => sanitize($attr['attribute_value']), array_filter($attributes, fn($attr) => $attr['attribute_type'] === 'color'))); ?></p>
                <p><strong>Boyut:</strong> <?php echo ($size = array_filter($attributes, fn($attr) => $attr['attribute_type'] === 'size')[0] ?? null) ? sanitize($size['attribute_value']) : '-'; ?></p>
                <p><strong>Ağırlık:</strong> <?php echo ($weight = array_filter($attributes, fn($attr) => $attr['attribute_type'] === 'weight')[0] ?? null) ? sanitize($weight['attribute_value']) : '-'; ?></p>
            </div>
            <div class="col-md-6">
                <h5>Resimler</h5>
                <div class="row">
                    <?php foreach ($images as $image): ?>
                        <div class="col-md-4">
                            <img src="<?php echo asset('uploads/products/' . $image['image_path']); ?>" class="img-fluid mb-2" style="max-height: 150px;">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <h5 class="mt-4">Stok Hareketleri</h5>
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>Tür</th>
                    <th>Tarih</th>
                    <th>Miktar</th>
                    <th>Açıklama</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($entries as $entry): ?>
                    <tr>
                        <td>Giriş</td>
                        <td><?php echo $entry['entry_date']; ?></td>
                        <td><?php echo $entry['quantity']; ?></td>
                        <td><?php echo sanitize($entry['invoice_no']); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php foreach ($exits as $exit): ?>
                    <tr>
                        <td>Çıkış</td>
                        <td><?php echo $exit['exit_date']; ?></td>
                        <td><?php echo $exit['quantity']; ?></td>
                        <td><?php echo sanitize($exit['reason']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>