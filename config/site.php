<?php

return [
    'admin_path' => trim((string) (env('ADMIN_PANEL_PATH') ?: 'jts-secure-portal'), '/'),

    'contact' => [
        'address' => [
            'street' => 'Jl. Raya Kelapa Hybrida Blok QA3, Kelapa Gading',
            'city' => 'Jakarta Utara 14250',
            'country' => 'Indonesia',
            'full' => 'Jl. Raya Kelapa Hybrida Blok QA3, Kelapa Gading, Jakarta Utara 14250, Indonesia',
        ],
        'phones' => [
            'primary' => [
                'display' => '+62 (21) 452 3273',
                'url' => 'tel:+62214523273',
            ],
            'secondary' => [
                'display' => '+62 (21) 453 0535',
                'url' => 'tel:+62214530535',
            ],
        ],
        'email' => [
            'display' => 'jts@jtsid.org',
            'url' => 'mailto:jts@jtsid.org',
        ],
        'whatsapp' => [
            'display' => '+62 813-8543-5909',
            'url' => 'https://wa.me/6281385435909',
            'qr_image' => 'images/qr-whatsapp.webp',
        ],
        'social' => [
            'facebook' => 'https://www.facebook.com/profile.php?id=100057615161035',
            'instagram' => 'https://www.instagram.com/jakartataipeischoolofficial/',
            'youtube' => 'https://youtube.com/@jakartataipeischool?si=5LP55sxC05ZOteLH',
        ],
    ],
];
