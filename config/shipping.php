<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tarif Pengiriman
    |--------------------------------------------------------------------------
    |
    | Ongkos kirim dihitung dari jarak garis lurus (haversine) antara alamat
    | kantor dan alamat pengiriman pelanggan.
    |
    */

    'rate_per_km' => (int) env('SHIPPING_RATE_PER_KM', 3000),

    /** Ongkir minimum, dipakai bila jaraknya sangat dekat. */
    'minimum_cost' => (int) env('SHIPPING_MINIMUM_COST', 5000),

    /** Pembulatan ongkir ke atas (kelipatan rupiah). */
    'rounding' => (int) env('SHIPPING_ROUNDING', 100),

    /** Jarak maksimum yang dilayani, dalam kilometer. */
    'max_distance_km' => (float) env('SHIPPING_MAX_DISTANCE_KM', 50),

    'earth_radius_km' => 6371,

];
