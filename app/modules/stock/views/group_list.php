<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-folder mr-2"></i>Stok Grupları</h3>
        <div class="card-tools">
            <a href="<?php echo url('stock/groupAdd'); ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-2"></i>Yeni Grup
            </a>
        </div>
    </div>
    <div class="card-body">
        <h5>Stok Grupları</h5>
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>Kod</th>
                    <th>Ad</th>
                    <th>Açıklama</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($groups as $group): ?>
                    <tr>
                        <td><?php echo sanitize($group['code']); ?></td>
                        <td><?php echo sanitize($group['name']); ?></td>
                        <td><?php echo sanitize($group['description'] ?? '-'); ?></td>
                        <td>
                            <a href="<?php echo url('stock/groupEdit/' . $group['id'] . '/group'); ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Düzenle
                            </a>
                            <button class="btn btn-sm btn-danger" onclick="deleteGroup(<?php echo $group['id']; ?>, 'group')">
                                <i class="fas fa-trash"></i> Sil
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h5 class="mt-4">Ara Gruplar</h5>
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>Kod</th>
                    <th>Ad</th>
                    <th>Stok Grubu</th>
                    <th>Açıklama</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sub_groups as $sub_group): ?>
                    <tr>
                        <td><?php echo sanitize($sub_group['code']); ?></td>
                        <td><?php echo sanitize($sub_group['name']); ?></td>
                        <td>
                            <?php
                            $parent_group = array_filter($groups, fn($g) => $g['id'] == $sub_group['group_id']);
                            echo !empty($parent_group) ? sanitize(reset($parent_group)['name']) : '-';
                            ?>
                        </td>
                        <td><?php echo sanitize($sub_group['description'] ?? '-'); ?></td>
                        <td>
                            <a href="<?php echo url('stock/groupEdit/' . $sub_group['id'] . '/sub_group'); ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Düzenle
                            </a>
                            <button class="btn btn-sm btn-danger" onclick="deleteGroup(<?php echo $sub_group['id']; ?>, 'sub_group')">
                                <i class="fas fa-trash"></i> Sil
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h5 class="mt-4">Alt Gruplar</h5>
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>Kod</th>
                    <th>Ad</th>
                    <th>Ara Grubu</th>
                    <th>Açıklama</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sub_sub_groups as $sub_sub_group): ?>
                    <tr>
                        <td><?php echo sanitize($sub_sub_group['code']); ?></td>
                        <td><?php echo sanitize($sub_sub_group['name']); ?></td>
                        <td>
                            <?php
                            $parent_sub_group = array_filter($sub_groups, fn($sg) => $sg['id'] == $sub_sub_group['sub_group_id']);
                            echo !empty($parent_sub_group) ? sanitize(reset($parent_sub_group)['name']) : '-';
                            ?>
                        </td>
                        <td><?php echo sanitize($sub_sub_group['description'] ?? '-'); ?></td>
                        <td>
                            <a href="<?php echo url('stock/groupEdit/' . $sub_sub_group['id'] . '/sub_sub_group'); ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Düzenle
                            </a>
                            <button class="btn btn-sm btn-danger" onclick="deleteGroup(<?php echo $sub_sub_group['id']; ?>, 'sub_sub_group')">
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
function deleteGroup(id, type) {
    if (confirm('Grubu silmek istediğinizden emin misiniz?')) {
        $.post('<?php echo url('stock/deleteGroup'); ?>', {id: id, type: type}, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Grup silme başarısız.');
            }
        });
    }
}