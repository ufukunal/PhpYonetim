<?php
// Yetkilendirme modülü ayarları
return [
    'module_name' => 'Auth',
    'default_action' => 'roleList',
    'permissions' => [
        'auth.view' => 'Rol ve izinleri görüntüleme',
        'auth.edit' => 'Rol ve izinleri düzenleme'
    ]
];
?>