<?php

return [
    "morphMap" => [],
    "router" => [
        "default" => [
            "prefix" => [
                'web' => env('WEB_PREFIX', '/'),
                'api' => env('API_PREFIX', '/api/v1'),
                'admin_web' => env('ADMIN_WEB_PREFIX', '/admin'),
                'admin_web_api' => env('ADMIN_WEB_API_PREFIX', '/api/admin'),
            ],
            "namespaces" => 'Properos\Users\Controllers',
            "options" => [
                "prefix" => "/"
            ],
            "middleware" => [
                "public" => [],
                "private" => ['auth', 'role:admin|sales'],
                "admin" => ['auth', 'role:admin|sales'],
                "api" => ['api', 'role:admin|sales'],
            ]
        ],
        
    ],
    "socialite" => [
        "default" => [
            "role" => "customer"
        ]
    ],
    'register_role' => null,
    'mail' => [
        "email" => env("MAIL_FROM_ADDRESS", "support@properos.com"),
        "reset_password_subject" => "Listiva"
    ]
];