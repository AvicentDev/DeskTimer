<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'auth/google/*'],

    'allowed_methods' => ['*'],

    // Aquí pones tu frontend (Vite, React, etc.) que corre en http://localhost:5173
    'allowed_origins' => [
        'http://localhost:5173',
        'https://frontend-desk-timer.vercel.app',
        'https://frontend-desk-timer-lwexgoudv-avicentdevs-projects.vercel.app'
    ],

    // Permite todos los subdominios de Vercel
    'allowed_origins_patterns' => [
        '/^https:\/\/.*\.vercel\.app$/'
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,


];
