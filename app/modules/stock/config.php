<?php
// Stok modülü ayarları
return [
    'module_name' => 'Stock',
    'default_action' => 'list',
    'permissions' => [
        'stock.view' => 'Ürünleri ve stok durumunu görüntüleme',
        'stock.edit' => 'Ürünleri ve işlemleri düzenleme',
        'stock.delete' => 'Ürün veya işlem silme',
        'stock.entry' => 'Stok girişi yapma',
        'stock.exit' => 'Stok çıkışı yapma',
        'stock.group.edit' => 'Stok grubu, ara grubu ve alt grubu yönetimi'
    ]
];
?>