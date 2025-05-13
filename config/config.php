<?php
return [
    'base_url' => 'https://your_domain.com', // Hosting alan adınız (örn: https://alanadiniz.com)
    'session' => [
        'name' => 'app_session',
        'lifetime' => 7200, // Oturum süresi (saniye, 2 saat)
        'secure' => true, // HTTPS için true
        'httponly' => true // XSS koruması için
    ],
    'timezone' => 'Europe/Istanbul', // Türkiye saat dilimi
    'log_path' => __DIR__ . '/../logs/error.log', // Hata loglarının yolu
    'debug' => false // Üretimde false, geliştirme için true
];
?>