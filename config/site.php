<?php

return [
    'admin_path' => trim((string) (env('ADMIN_PANEL_PATH') ?: 'jts-secure-portal'), '/'),

    'initial_admin' => [
        'name' => env('LOCAL_ADMIN_NAME', 'Super Admin'),
        'email' => env('LOCAL_ADMIN_EMAIL', 'admin@jts.local'),
        'password' => env('LOCAL_ADMIN_PASSWORD'),
    ],

    'contact_form' => [
        'recipient' => env('CONTACT_FORM_RECIPIENT', 'jtis.org@gmail.com'),
    ],

    'contact' => [
        'address' => [
            'street' => 'JL. Raya Kelapa Hibrida Blok QH, Kelapa Gading Permai',
            'city' => 'Jakarta 14240',
            'country' => 'Indonesia',
            'full' => 'JL. Raya Kelapa Hibrida Blok QH, Kelapa Gading Permai, Jakarta 14240, Indonesia',
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
        'whatsapp' => [
            'admissions' => [
                'display' => '+62 813-8543-5909',
                'url' => 'https://wa.me/6281385435909',
                'qr_image' => 'images/qr-whatsapp.webp',
            ],
            'mandarin' => [
                'display' => '+62 811-1180-5213',
                'url' => 'https://wa.me/6281111805213',
                'qr_image' => 'images/qr-whatsapp-mandarin.webp',
            ],
        ],
        'social' => [
            'tiktok' => 'https://www.tiktok.com/@jts.jakartataipeischool?_r=1&_t=ZS-9AmzNRYa4TS',
            'instagram' => 'https://www.instagram.com/jakartataipeischoolofficial/',
            'youtube' => 'https://youtube.com/@jakartataipeischool?si=5LP55sxC05ZOteLH',
        ],
    ],
];
