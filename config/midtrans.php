<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Midtrans Credentials
    |--------------------------------------------------------------------------
    |
    | Kredensial diambil lewat config() supaya tetap terbaca setelah
    | `php artisan config:cache`. Memanggil env() langsung di luar file config
    | akan mengembalikan null pada environment yang config-nya sudah di-cache.
    |
    */

    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'merchant_id' => env('MIDTRANS_MERCHANT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Endpoint
    |--------------------------------------------------------------------------
    |
    | Sandbox : https://api.sandbox.midtrans.com/
    | Produksi: https://api.midtrans.com/
    |
    */

    'endpoint' => rtrim(env('MIDTRANS_ENDPOINT', 'https://api.sandbox.midtrans.com/'), '/') . '/',

    /*
    |--------------------------------------------------------------------------
    | Batas Waktu Pembayaran
    |--------------------------------------------------------------------------
    |
    | Durasi (menit) sebelum tagihan kadaluarsa di sisi Midtrans.
    |
    */

    'expiry_minutes' => (int) env('MIDTRANS_EXPIRY_MINUTES', 60),

    /*
    |--------------------------------------------------------------------------
    | Timeout HTTP
    |--------------------------------------------------------------------------
    */

    'timeout' => (int) env('MIDTRANS_TIMEOUT', 15),

    'recipient_name' => env('MIDTRANS_RECIPIENT_NAME', 'Kusuka Catering'),

    /*
    |--------------------------------------------------------------------------
    | Metode Pembayaran yang Didukung
    |--------------------------------------------------------------------------
    |
    | Dipakai untuk memvalidasi input `payment_type` dari form checkout.
    |
    */

    'payment_types' => ['bca', 'bni', 'bri', 'permata', 'cimb', 'qris'],

];
