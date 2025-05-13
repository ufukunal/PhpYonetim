<?php
// RestAPI modülü ayarları
return [
    'module_name' => 'Api',
    'default_action' => 'ping',
    'permissions' => [
        'api.key.generate' => 'API anahtarı oluşturma',
        'stock.view' => 'Stok ürünlerini görüntüleme',
        'stock.add' => 'Yeni stok ürünü ekleme',
        'stock.edit' => 'Stok ürünlerini düzenleme',
        'stock.delete' => 'Stok ürünlerini silme',
        // Diğer modüller için izinler burada tanımlanabilir
    ],
    'api_version' => 'v1',
    'rate_limit' => 100, // Dakikada maksimum istek
    'rate_limit_window' => 60 // Saniye cinsinden pencere
];
?>