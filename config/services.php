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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    'eveonline' => [
        'client_id'     => env('EVE_ONLINE_CLIENT_ID'),
        'client_secret' => env('EVE_ONLINE_CLIENT_SECRET'),
        'redirect'      => env('EVE_ONLINE_REDIRECT_URI'),
        'metadata_url'        => env('EVE_ONLINE_METADATA_URL', 'https://login.eveonline.com/.well-known/oauth-authorization-server'),
        'metadata_cache_time' => env('EVE_ONLINE_METADATA_CACHE', 300),
        'accepted_issuers'    => [
            'https://login.eveonline.com',
            'logineveonline.com', // legacy quirk if you want to accept it
        ],
        'accepted_audience'   => env('EVE_ONLINE_ACCEPTED_AUD', 'EVE Online'),
        'alg'                 => 'RS256',
    ],

];
