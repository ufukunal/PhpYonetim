<?php
// Sipariş modülü ayarları
return [
    'module_name' => 'Order',
    'default_action' => 'list',
    'permissions' => [
        'order.view' => 'Siparişleri ve detaylarını görüntüleme',
        'order.add' => 'Yeni sipariş ekleme',
        'order.edit' => 'Siparişleri düzenleme',
        'order.delete' => 'Sipariş silme',
        'order.invoice' => 'Siparişten fatura oluşturma',
        'order.open_products' => 'Açık sipariş ürünleri listesini görüntüleme'
    ]
];
?>