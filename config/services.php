<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
    'facebook' => [
        'client_id' => env('FACEBOOK_ID'),
        'client_secret' => env('FACEBOOK_SECRET'),
        'redirect' => env('FACEBOOK_URL'),
    ],
    'google' => [
        'client_id' => env('GOOGLE_KEY'),
        'client_secret' => env('GOOGLE_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],
    'hacienda' => [
        'base_uri' => env('HACIENDA_BASE_URI'),
        'oauth_uri' => env('HACIENDA_OAUTH_URI'),
        'logout_uri' => env('HACIENDA_LOGOUT_URI'),
        'client_id' => env('HACIENDA_CLIENT_ID'),
        'client_secret' => env('HACIENDA_CLIENT_SECRET'),
    ],
    'onesignal' => [
        'url_api' => env('URL_ONESIGNAL_API', 'https://onesignal.com/api/v1/notifications'),
        'api_key' => env('API_KEY_ONESIGNAL'),
        'app_id' => env('APP_ID_ONESIGNAL', '46fd644f-f655-4066-8b6d-9fe9b3786bc5'),
    ],
    'pasarela' => [
        'currency_code' => env('CURRENCY_CODE'),
        'currency_code_colon' => env('CURRENCY_CODE_COLON'),
        'acquire_id' => env('ACQUIRE_ID'),
        'commerce_id' => env('COMMERCE_ID'),
        'mall_id' => env('MALL_ID'),
        'terminal_code' => env('TERMINAL_CODE'),
        'clave_sha2' => env('CLAVE_SHA2'),
        'simulation_vpos' => env('SIMULATION_VPOS'),
        'url_vpos2' => env('URL_VPOS2'),
        'url_modal' => env('URL_MODAL')
    ],
    'twilio' => [
        'from' => env('TWILIO_FROM'),
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_TOKEN'),
       
    ],


];
