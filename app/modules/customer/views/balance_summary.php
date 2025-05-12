<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-money-bill mr-2"></i>Bakiye Özeti</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo url('customer/balanceSummary'); ?>" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <label>Müşteri Grubu</label>
                    <select name="group_id" class="form-control select2">
                        <option value="">Tümü</option>
                        <?php foreach ($groups as $group): ?>
                            <option value="<?php echo $group['id']; ?>" <?php echo isset($_GET['group_id']) && $_GET['group_id'] == $group['id'] ? 'selected' : ''; ?>>
                                <?php echo sanitize($group['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Bakiye Türü</label>
                    <select name="balance_type" class="form-control">
                        <option value="">Tümü</option>
                        <option value="positive" <?php echo isset($_GET['balance_type']) && $_GET['balance_type'] == 'positive' ? 'selected' : ''; ?>>Alacak</option>
                        <option value="negative" <?php echo isset($_GET['balance_type']) && $_GET['balance_type'] == 'negative' ? 'selected' : ''; ?>>Borç</option>
                    </select>
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
                    <th>Unvan</th>
                    <th>Grup</th>
                    <th>Bakiye</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($summary as $item): ?>
                    <tr>
                        <td><?php echo sanitize($item['code']); ?></td>
                        <td><?php echo sanitize($item['title']); ?></td>
                        <td><?php echo sanitize($item['group_name'] ?? '-'); ?></td>
                        <td><?php echo number_format($item['balance'] ?? 0, 2); ?> TL</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>