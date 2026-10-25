<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Resmi Bakso Pesona
    |--------------------------------------------------------------------------
    | Kontak WhatsApp dan QRIS harus diisi dari data resmi pemilik usaha.
    */

    'whatsapp_number' => env('BAKSO_PESONA_WHATSAPP'),
    'store_name' => 'Bakso Pesona',
    'contact_email' => env('BAKSO_PESONA_CONTACT_EMAIL'),
    'qris' => [
        'image' => env('BAKSO_PESONA_QRIS_IMAGE'),
        'merchant_name' => env('BAKSO_PESONA_QRIS_MERCHANT_NAME'),
    ],
    'admin_seed' => [
        'email' => env('BAKSO_PESONA_ADMIN_EMAIL'),
        'password' => env('BAKSO_PESONA_ADMIN_PASSWORD'),
    ],
];
