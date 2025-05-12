<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-box mr-2"></i>Ürünler</h3>
        <div class="card-tools">
            <a href="<?php echo url('stock/add'); ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-2"></i>Yeni Ürün
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo url('stock/list'); ?>" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <label>Stok Grubu</label>
                    <select name="stock_group_id" class="form-control select2">
                        <option value="">Tümü</option>
                        <?php foreach ($groups as $group): ?>
                            <option value="<?php echo $group['id']; ?>" <?php echo isset($_GET['stock_group_id']) && $_GET['stock_group_id'] == $group['id'] ? 'selected' : ''; ?>>
                                <?php echo sanitize($group['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Minimum Stok</label>
                    <input type="number" name="min_quantity" class="form-control" value="<?php echo isset($_GET['min_quantity']) ? sanitize($_GET['min_quantity']) : ''; ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary mt-4"><i class="fas fa-filter mr-2"></i>Filtrele</button>
                </div>
            </div>
        </form>
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>Kod</th>
                    <th>Ad</th>
                    <th>Birim</th>
                    <th>Stok</th>
                    <th>Grup</th>
                    <th>Renk</th>
                    <th>Boyut</th>
                    <th>Ağırlık</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <?php
                    $attributes = $this->attributeModel->getAttributesByProduct($product['id']);
                    $colors = array_filter($attributes, fn($attr) => $attr['attribute_type'] === 'color');
                    $size = array_filter($attributes, fn($attr) => $attr['attribute_type'] === 'size')[0] ?? null;
                    $weight = array_filter($attributes, fn($attr) => $attr['attribute_type'] === 'weight')[0] ?? null;
                    ?>
                    <tr>
                        <td><?php echo sanitize($product['code']); ?></td>
                        <td><?php echo sanitize($product['name']); ?></td>
                        <td><?php echo sanitize($product['unit']); ?></td>
                        <td><?php echo $product['quantity']; ?></td>
                        <td><?php echo sanitize($product['group_name'] . ' > ' . $product['sub_group_name'] . ' > ' . $product['sub_sub_group_name']); ?></td>
                        <td><?php echo implode(', ', array_map(fn($c) => sanitize($c['attribute_value']), $colors)); ?></td>
                        <td><?php echo $size ? sanitize($size['attribute_value']) : '-'; ?></td>
                        <td><?php echo $weight ? sanitize($weight['attribute_value']) : '-'; ?></td>
                        <td>
                            <a href="<?php echo url('stock/edit/' . $product['id']); ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Düzenle
                            </a>
                            <a href="<?php echo url('stock/detail/' . $product['id']); ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Detay
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>