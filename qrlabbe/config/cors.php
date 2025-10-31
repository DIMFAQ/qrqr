<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'login',
        'register' // <-- Ini penting untuk registrasi
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000' // <-- Alamat React kamu
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // <-- Ini SANGAT PENTING

];