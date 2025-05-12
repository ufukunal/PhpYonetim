<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-folder mr-2"></i>Grup Ekle</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo url('stock/groupAdd'); ?>">
            <div class="form-group">
                <label for="type">Grup Türü</label>
                <select class="form-control" id="type" name="type" required>
                    <option value="group">Stok Grubu</option>
                    <option value="sub_group">Ara Grubu</option>
                    <option value="sub_sub_group">Alt Grubu</option>
                </select>
            </div>
            <div class="form-group" id="group_id_container" style="display: none;">
                <label for="group_id">Stok Grubu</label>
                <select class="form-control select2" id="group_id" name="group_id">
                    <?php foreach ($groups as $group): ?>
                        <option value="<?php echo $group['id']; ?>"><?php echo sanitize($group['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" id="sub_group_id_container" style="display: none;">
                <label for="sub_group_id">Ara Grubu</label>
                <select class="form-control select2" id="sub_group_id" name="sub_group_id">
                    <!-- Dinamik olarak AJAX ile doldurulacak -->
                </select>
            </div>
            <div class="form-group">
                <label for="code">Grup Kodu</label>
                <input type="text" class="form-control" id="code" name="code" required>
            </div>
            <div class="form-group">
                <label for="name">Grup Adı</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="description">Açıklama</label>
                <textarea class="form-control" id="description" name="description"></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Kaydet</button>
        </form>
    </div>
</div>
<script>
$(document).ready(function() {
    $('.select2').select2();
    $('#type').change(function() {
        var type = $(this).val();
        $('#group_id_container').hide();
        $('#sub_group_id_container').hide();
        if (type === 'sub_group' || type === 'sub_sub_group') {
            $('#group_id_container').show();
        }
        if (type === 'sub_sub_group') {
            $('#sub_group_id_container').show();
            $.get('<?php echo url('stock/getSubGroups'); ?>?group_id=' + $('#group_id').val(), function(data) {
                $('#sub_group_id').html(data);
            });
        }
    });
    $('#group_id').change(function() {
        if ($('#type').val() === 'sub_sub_group') {
            $.get('<?php echo url('stock/getSubGroups'); ?>?group_id=' + $(this).val(), function(data) {
                $('#sub_group_id').html(data);
            });
        }
    });
});
</script>