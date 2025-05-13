<?php
// Kasa modülü ayarları
return [
    'module_name' => 'Cash',
    'default_action' => 'list',
    'permissions' => [
        'cash.view' => 'Kasa ve işlemlerini görüntüleme',
        'cash.add' => 'Yeni kasa tanımlama ve işlem ekleme',
        'cash.edit' => 'Kasa ve işlemlerini düzenleme',
        'cash.delete' => 'Kasa ve işlemlerini silme',
        'cash.control' => 'İşlemleri kontrol etme ve reddetme'
    ]
];
?>