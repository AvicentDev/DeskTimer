<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'auth/google/*'],

    'allowed_methods' => ['*'],

    // Aquí pones tu frontend (Vite, React, etc.) que corre en http://localhost:5173
    'allowed_origins' => [
        'http://localhost:5173',
        'https://frontend-desk-timer.vercel.app'
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];

