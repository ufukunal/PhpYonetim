<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-exchange-alt mr-2"></i>Müşteri İşlemleri</h3>
        <div class="card-tools">
            <a href="<?php echo url('customer/transactionAdd'); ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-2"></i>Yeni İşlem
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo url('customer/transactionList'); ?>" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <label>Müşteri</label>
                    <select name="customer_id" class="form-control select2">
                        <option value="">Tümü</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?php echo $customer['id']; ?>" <?php echo isset($_GET['customer_id']) && $_GET['customer_id'] == $customer['id'] ? 'selected' : ''; ?>>
                                <?php echo sanitize($customer['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>İşlem Türü</label>
                    <select name="type" class="form-control">
                        <option value="">Tümü</option>
                        <option value="purchase" <?php echo isset($_GET['type']) && $_GET['type'] == 'purchase' ? 'selected' : ''; ?>>Alış</option>
                        <option value="sale" <?php echo isset($_GET['type']) && $_GET['type'] == 'sale' ? 'selected' : ''; ?>>Satış</option>
                        <option value="payment" <?php echo isset($_GET['type']) && $_GET['type'] == 'payment' ? 'selected' : ''; ?>>Ödeme</option>
                        <option value="collection" <?php echo isset($_GET['type']) && $_GET['type'] == 'collection' ? 'selected' : ''; ?>>Tahsilat</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Başlangıç Tarihi</label>
                    <input type="date" name="start_date" class="form-control" value="<?php echo isset($_GET['start_date']) ? sanitize($_GET['start_date']) : ''; ?>">
                </div>
                <div class="col-md-3">
                    <label>Bitiş Tarihi</label>
                    <input type="date" name="end_date" class="form-control" value="<?php echo isset($_GET['end_date']) ? sanitize($_GET['end_date']) : ''; ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary mt-4"><i class="fas fa-filter mr-2"></i>Filtrele</button>
                </div>
            </div>
        </form>
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>Müşteri</th>
                    <th>Tür</th>
                    <th>Tarih</th>
                    <th>Tutar</th>
                    <th>Fatura No</th>
                    <th>Fatura Adresi</th>
                    <th>Sevk Adresi</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?php echo sanitize($transaction['customer_title']); ?></td>
                        <td><?php echo sanitize($transaction['type']); ?></td>
                        <td><?php echo $transaction['transaction_date']; ?></td>
                        <td><?php echo number_format($transaction['amount'], 2) . ' ' . $transaction['currency']; ?></td>
                        <td><?php echo sanitize($transaction['invoice_no'] ?? '-'); ?></td>
                        <td><?php echo sanitize($transaction['invoice_address_title'] ?? '-'); ?></td>
                        <td><?php echo sanitize($transaction['delivery_address_title'] ?? '-'); ?></td>
                        <td>
                            <a href="<?php echo url('customer/transactionEdit/' . $transaction['id']); ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Düzenle
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>