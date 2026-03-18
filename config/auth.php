<?php

return [

    'defaults' => [
        'guard'     => 'merchant',
        'passwords' => 'merchants',
    ],

    'guards' => [
        'web' => [
            'driver'   => 'session',
            'provider' => 'users',
        ],

        'merchant' => [
            'driver'   => 'session',
            'provider' => 'merchants',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model'  => App\Models\User::class,
        ],

        'merchants' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Merchant::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],

        'merchants' => [
            'provider' => 'merchants',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];
