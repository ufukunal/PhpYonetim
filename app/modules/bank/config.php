<?php
// Banka modülü ayarları
return [
    'module_name' => 'Bank',
    'default_action' => 'list',
    'permissions' => [
        'bank.view' => 'Banka hesaplarını ve işlemlerini görüntüleme',
        'bank.add' => 'Yeni banka hesabı tanımlama ve işlem ekleme',
        'bank.edit' => 'Banka hesaplarını ve işlemlerini düzenleme',
        'bank.delete' => 'Banka hesaplarını ve işlemlerini silme',
        'bank.control' => 'İşlemleri kontrol etme ve reddetme'
    ]
];
?>