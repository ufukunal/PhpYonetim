<?php
// Customer modülü ayarları
return [
    'module_name' => 'Customer',
    'default_action' => 'list',
    'permissions' => [
        'customer.view' => 'Müşteri hesaplarını ve işlemleri görüntüleme',
        'customer.edit' => 'Müşteri hesaplarını ve işlemleri düzenleme',
        'customer.delete' => 'Müşteri hesap veya işlem silme',
        'customer.transaction.add' => 'Müşteri işlemi ekleme',
        'customer.group.edit' => 'Müşteri grubu yönetimi',
        'customer.address.edit' => 'Müşteri adreslerini düzenleme',
        'customer.contact.edit' => 'Müşteri yetkili iletişim bilgilerini düzenleme'
    ]
];
?>