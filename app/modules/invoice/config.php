<?php
// Fatura modülü ayarları
return [
    'module_name' => 'Invoice',
    'default_action' => 'list',
    'permissions' => [
        'invoice.view' => 'Faturaları ve detaylarını görüntüleme',
        'invoice.add' => 'Yeni fatura ekleme',
        'invoice.edit' => 'Faturaları ve kalemleri düzenleme',
        'invoice.delete' => 'Fatura silme',
        'invoice.print' => 'Fatura yazdırma ve dışa aktarma'
    ]
];
?>