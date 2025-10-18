<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'auth/google/*'],

    'allowed_methods' => ['*'],

    // Permitir todos los orígenes temporalmente para debug
    'allowed_origins' => ['*'],

    // Permite todos los subdominios de Vercel
    'allowed_origins_patterns' => [
        '/^https:\/\/.*\.vercel\.app$/'
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,


];
