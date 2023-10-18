<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
        'secret' => env('POSTMARK_SECRET')
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'onesignal' => [
        'mode' => env(key: 'ONESIGNAL_MODE', default: 'sandbox'),
        'endpoint' => env(key: 'ONESIGNAL_ENDPOINT', default: 'https://onesignal.com'),
        'live' => [
            'contractor' => [
                'app_id' => env('ONESIGNAL_LIVE_APP_ID'),
                'app_key' => env('ONESIGNAL_LIVE_REST_API_KEY'),
            ],
        ],

        'sandbox' => [
            'contractor' => [
                'app_id' => env('ONESIGNAL_SANDBOX_APP_ID'),
                'app_key' => env('ONESIGNAL_SANDBOX_REST_API_KEY'),
            ],
        ],

        'test' => [
            'device' => [
                'player_id' => [
                    'contractor' => env('TEST_ONESIGNAL_DEVICE_PLAYER_ID'),
                ],
            ],
        ],
    ],
];
