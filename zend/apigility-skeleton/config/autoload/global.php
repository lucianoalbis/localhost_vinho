<?php
return [
    'zf-content-negotiation' => [
        'selectors' => [],
    ],
    'db' => [
        'adapters' => [
            'dummy' => [],
            'db Mysql - skeleton-application' => [],
        ],
    ],
    'zf-mvc-auth' => [
        'authentication' => [
            'map' => [
                'API\\V1' => 'auth',
                'APIRest\\V1' => 'auth',
            ],
        ],
    ],
];
