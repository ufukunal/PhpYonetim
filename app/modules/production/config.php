<?php
// Üretim modülü ayarları
return [
    'module_name' => 'Production',
    'default_action' => 'order_list',
    'permissions' => [
        'production.view' => 'Reçeteleri ve üretim emirlerini görüntüleme',
        'production.add' => 'Yeni reçete ve üretim emri ekleme',
        'production.edit' => 'Reçeteleri ve üretim emirlerini düzenleme',
        'production.delete' => 'Reçeteleri ve üretim emirlerini silme',
        'production.track' => 'Üretim takip adımlarını yönetme'
    ]
];
?>