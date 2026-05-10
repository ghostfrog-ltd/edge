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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'ghostfrog_engine' => [
        'url' => env('GHOSTFROG_ENGINE_URL', 'http://127.0.0.1:8001'),
        'callback_base_url' => env('GHOSTFROG_ENGINE_CALLBACK_BASE_URL'),
        'shared_secret' => env('GHOSTFROG_ENGINE_SHARED_SECRET', 'ghostfrog-engine-secret'),
        'callback_secret' => env('GHOSTFROG_ENGINE_CALLBACK_SECRET', 'ghostfrog-callback-secret'),
        'timeout_seconds' => env('GHOSTFROG_ENGINE_TIMEOUT_SECONDS', 10),
    ],

    'ebay' => [
        'client_id' => env('EBAY_CLIENT_ID', env('EBAY_APP_ID')),
        'client_secret' => env('EBAY_CLIENT_SECRET', env('EBAY_CERT_ID')),
        'oauth_url' => env('EBAY_OAUTH_URL', 'https://api.ebay.com/identity/v1/oauth2/token'),
        'taxonomy_url' => env('EBAY_TAXONOMY_URL', 'https://api.ebay.com/commerce/taxonomy/v1'),
        'browse_url' => env('EBAY_BROWSE_URL', 'https://api.ebay.com/buy/browse/v1'),
    ],

];
