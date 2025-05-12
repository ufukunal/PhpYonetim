<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-folder mr-2"></i>Grup Düzenle</h3>
    </div>
    <div class="card-body">
        <?php
        $group = [];
        if ($type === 'group') {
            $group = $this->groupModel->getGroupById($id);
        } elseif ($type === 'sub_group') {
            $group = $this->groupModel->getSubGroupById($id);
        } elseif ($type === 'sub_sub_group') {
            $group = $this->groupModel->getSubSubGroupById($id);
        }
        ?>
        <form method="POST" action="<?php echo url('stock/groupEdit/' . $id . '/' . $type); ?>">
            <input type="hidden" name="type" value="<?php echo $type; ?>">
            <?php if ($type === 'sub_group' || $type === 'sub_sub_group'): ?>
                <div class="form-group">
                    <label for="group_id">Stok Grubu</label>
                    <select class="form-control select2" id="group_id" name="group_id" required>
                        <?php foreach ($groups as $g): ?>
                            <option value="<?php echo $g['id']; ?>" <?php echo isset($group['group_id']) && $group['group_id'] == $g['id'] ? 'selected' : ''; ?>>
                                <?php echo sanitize($g['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
            <?php if ($type === 'sub_sub_group'): ?>
                <div class="form-group">
                    <label for="sub_group_id">Ara Grubu</label>
                    <select class="form-control select2" id="sub_group_id" name="sub_group_id" required>
                        <!-- Dinamik olarak AJAX ile doldurulacak -->
                    </select>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <label for="code">Grup Kodu</label>
                <input type="text" class="form-control" id="code" name="code" value="<?php echo sanitize($group['code']); ?>" required>
            </div>
            <div class="form-group">
                <label for="name">Grup Adı</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo sanitize($group['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Açıklama</label>
                <textarea class="form-control" id="description" name="description"><?php echo sanitize($group['description'] ?? ''); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Kaydet</button>
        </form>
    </div>
</div>
<script>
$(document).ready(function() {
    $('.select2').select2();
    <?php if ($type === 'sub_sub_group'): ?>
        $.get('<?php echo url('stock/getSubGroups'); ?>?group_id=' + $('#group_id').val(), function(data) {
            $('#sub_group_id').html(data);
            $('#sub_group_id').val('<?php echo isset($group['sub_group_id']) ? $group['sub_group_id'] : ''; ?>');
        });
    <?php endif; ?>
    $('#group_id').change(function() {
        $.get('<?php echo url('stock/getSubGroups'); ?>?group_id=' + $(this).val(), function(data) {
            $('#sub_group_id').html(data);
        });
    });
});
</script>