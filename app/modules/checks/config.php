<?php
// Çek Senet modülü ayarları
return [
    'module_name' => 'Check',
    'default_action' => 'list',
    'permissions' => [
        'check.view' => 'Çek/senetleri ve işlemlerini görüntüleme',
        'check.add' => 'Yeni çek/senet ve işlem ekleme',
        'check.edit' => 'Çek/senet ve işlemlerini düzenleme',
        'check.delete' => 'Çek/senet ve işlemlerini silme',
        'check.control' => 'İşlemleri kontrol etme ve reddetme'
    ]
];
?>