<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user-tie mr-2"></i>Müşteri Detayı</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Kod:</strong> <?php echo sanitize($customer['code']); ?></p>
                <p><strong>Unvan:</strong> <?php echo sanitize($customer['title']); ?></p>
                <p><strong>Tür:</strong> <?php echo sanitize($customer['type']); ?></p>
                <p><strong>Vergi Numarası:</strong> <?php echo sanitize($customer['tax_number'] ?? '-'); ?></p>
                <p><strong>Vergi Dairesi:</strong> <?php echo sanitize($customer['tax_office'] ?? '-'); ?></p>
                <p><strong>Grup:</strong> <?php echo sanitize($customer['group_name'] ?? '-'); ?></p>
                <p><strong>İskonto 1 (%):</strong> <?php echo number_format($customer['iskonto1'], 2); ?></p>
                <p><strong>İskonto 2 (%):</strong> <?php echo number_format($customer['iskonto2'], 2); ?></p>
                <p><strong>Bakiye:</strong> <?php echo number_format($balance, 2); ?> TL</p>
            </div>
            <div class="col-md-6">
                <h5>Adresler</h5>
                <?php if (empty($addresses)): ?>
                    <p>Adres bulunamadı.</p>
                <?php else: ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tür</th>
                                <th>Başlık</th>
                                <th>Adres</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($addresses as $address): ?>
                                <tr>
                                    <td><?php echo sanitize($address['type']); ?></td>
                                    <td><?php echo sanitize($address['title']); ?></td>
                                    <td><?php echo sanitize($address['address']); ?>,
                                        <?php echo sanitize($address['city'] ?? '') . ' ' . sanitize($address['country'] ?? '') . ' ' . sanitize($address['postal_code'] ?? ''); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                <h5 class="mt-4">Yetkili Kişiler</h5>
                <?php if (empty($contacts)): ?>
                    <p>Yetkili bulunamadı.</p>
                <?php else: ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>İsim</th>
                                <th>Unvan</th>
                                <th>Telefon</th>
                                <th>E-posta</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contacts as $contact): ?>
                                <tr>
                                    <td><?php echo sanitize($contact['name']); ?></td>
                                    <td><?php echo sanitize($contact['title'] ?? '-'); ?></td>
                                    <td><?php echo sanitize($contact['phone'] ?? '-'); ?></td>
                                    <td><?php echo sanitize($contact['email'] ?? '-'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        <h5 class="mt-4">İşlem Geçmişi</h5>
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>Tür</th>
                    <th>Tarih</th>
                    <th>Tutar</th>
                    <th>Fatura No</th>
                    <th>Fatura Adresi</th>
                    <th>Sevk Adresi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?php echo sanitize($transaction['type']); ?></td>
                        <td><?php echo $transaction['transaction_date']; ?></td>
                        <td><?php echo number_format($transaction['amount'], 2) . ' ' . $transaction['currency']; ?></td>
                        <td><?php echo sanitize($transaction['invoice_no'] ?? '-'); ?></td>
                        <td><?php echo sanitize($transaction['invoice_address_title'] ?? '-'); ?></td>
                        <td><?php echo sanitize($transaction['delivery_address_title'] ?? '-'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>