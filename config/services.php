<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services6
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
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT'),
    ],

    'apple' => [
        'client_id' => env('APPLE_CLIENT_ID'),
        'client_secret' => env('APPLE_CLIENT_SECRET'),
        'redirect' => env('APPLE_REDIRECT'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_APP_ID'),
        'client_secret' => env('FACEBOOK_APP_SECRET'),
        'redirect' =>  env('FACEBOOK_REDIRECT'),
    ],

    'stripe' => [
        'pub_key' => env('STRIPE_KEY', 'pk_test_51KoBxjJDWX8dXJe36Nl0GQpegKAG0crdsyGP5fyLrtPxbkrjevFioMqrQgzRS6u6JuAFHzx0QLIfo8uO9rQwRVbC00Z3QGDTwY'),
        'secret_key' => env('STRIPE_SECRET', 'sk_test_51KoBxjJDWX8dXJe3ygTJRI1PHfv3yYweNdp8xM4LRSpHnLya1TTsKjn6SelMOtbkPt56F38ZNYOFhomGTzq9xiV900wKdQUJiA'),
        'connect_client_id' => env('STRIPE_CONNECT_CLIENT_ID', 'ca_I6tO7zkEUzDlrdU84rNAkCdVWBhlGzKx'),
        'default_currency' => env('STRIPE_DEFAULT_CURRENCY', 'USD'),
    ],

    'sendgrid' => [
        'api_key' => env('SENDGRID_API_KEY'),
    ],

    'twilio' => [
        'sid' => env('TWILIO_ACCOUNT_SID'),
        'token' => env('TWILIO_ACCOUNT_TOKEN'),
        'chat' => env('TWILIO_SERVICE_SID'),
        'key' => env('TWILIO_API_KEY_SID'),
        'secret' => env('TWILIO_API_KEY_SECRET')
    ],

];
