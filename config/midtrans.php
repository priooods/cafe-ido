<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi Midtrans untuk integrasi pembayaran.
    |
    */

    'client_key' => env('MIDTRANS_CLIENT_KEY', 'your-client-key'),
    'server_key' => env('MIDTRANS_SERVER_KEY', 'your-server-key'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false), // false = sandbox
];
