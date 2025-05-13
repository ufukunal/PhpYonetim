<?php
return [
    'jwt_secret' => 'your_jwt_secret_very_long_and_secure', // JWT token doğrulama için benzersiz secret
    'rate_limit' => 100, // Dakikada maksimum istek sayısı
    'rate_limit_window' => 60, // Rate limit penceresi (saniye)
    'api_version' => 'v1', // API versiyonu
    'log_path' => __DIR__ . '/../logs/api.log', // API loglarının yolu
    'cors' => [
        'allowed_origins' => ['*'], // İzin verilen domain’ler, üretimde belirli domain’lerle sınırlayın
        'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        'allowed_headers' => ['X-API-Key', 'Authorization', 'Content-Type']
    ]
];
?>