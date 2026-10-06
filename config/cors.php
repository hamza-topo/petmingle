<?php

use App\Support\FrontendOrigins;

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'broadcasting/auth'],

    'allowed_methods' => ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    // Local Vite (5173) and the existing Docker-published frontend (5174).
    // Other environments must explicitly configure their frontend origins.
    'allowed_origins' => FrontendOrigins::parse(
        (string) env('CORS_ALLOWED_ORIGINS', env('APP_ENV') === 'local'
            ? 'http://localhost:5173,http://127.0.0.1:5173,http://localhost:5174,http://127.0.0.1:5174'
            : ''),
        ! in_array(env('APP_ENV'), ['local', 'testing'], true),
    ),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Accept', 'Authorization', 'Content-Type'],

    'exposed_headers' => [],

    'max_age' => 600,

    'supports_credentials' => false,

];
