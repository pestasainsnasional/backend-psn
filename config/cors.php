<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines which domains are allowed to access your
    | application's HTTP services from a browser. A static value of '*'
    | means that all domains will be allowed.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'register'], // Tambahkan 'login' dan 'register' jika endpointnya di luar prefix 'api' tapi masih di handle CORS
    'allowed_methods' => ['*'],
    'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:3000')],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
    
];