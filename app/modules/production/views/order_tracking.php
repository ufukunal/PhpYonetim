<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-tasks mr-2"></i>Üretim Takibi - <?php echo sanitize($order['order_number']); ?></h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Adım</th>
                    <th>Durum</th>
                    <th>Tamamlanma Tarihi</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tracking_steps as $step): ?>
                    <tr>
                        <td><?php echo sanitize($step['step']); ?></td>
                        <td><?php echo sanitize($step['status']); ?></td>
                        <td><?php echo $step['completed_at'] ? $step['completed_at'] : '-'; ?></td>
                        <td>
                            <?php if ($step['status'] != 'completed'): ?>
                                <form method="POST" action="<?php echo url('production/tracking/' . $order['id']); ?>">
                                    <input type="hidden" name="tracking_id" value="<?php echo $step['id']; ?>">
                                    <select name="status" class="form-control">
                                        <option value="pending" <?php echo $step['status'] == 'pending' ? 'selected' : ''; ?>>Beklemede</option>
                                        <option value="in_progress" <?php echo $step['status'] == 'in_progress' ? 'selected' : ''; ?>>Devam Ediyor</option>
                                        <option value="completed" <?php echo $step['status'] == 'completed' ? 'selected' : ''; ?>>Tamamlandı</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary mt-2"><i class="fas fa-save"></i> Güncelle</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
$(document).ready(function() {
    $('.select2').select2();
});
</script>