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
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // Firebase web app used by the chat screens; leave FIREBASE_API_KEY empty to switch chat off
    'firebase' => [
        // service account json used by the server to write chat messages
        'credentials' => env('FIREBASE_CREDENTIALS', base_path('app/Http/Controllers/web/firebase-secretkey.json')),
        // server key for mobile push notifications; empty = no push
        'fcm_server_key' => env('FCM_SERVER_KEY'),
        'web' => [
            'apiKey' => env('FIREBASE_API_KEY'),
            'authDomain' => env('FIREBASE_AUTH_DOMAIN'),
            'databaseURL' => env('FIREBASE_DATABASE_URL'),
            'projectId' => env('FIREBASE_PROJECT_ID'),
            'storageBucket' => env('FIREBASE_STORAGE_BUCKET'),
            'messagingSenderId' => env('FIREBASE_MESSAGING_SENDER_ID'),
            'appId' => env('FIREBASE_APP_ID'),
            'measurementId' => env('FIREBASE_MEASUREMENT_ID'),
        ],
    ],

    // public digital business card page opened by the employee QR code; empty = this app's own card data route
    'ecard' => [
        'url' => env('ECARD_URL'),
    ],

    // shared key the attendance machine sends in the Machine-Key header (empty = machine import closed)
    'attendance_machine' => [
        'key' => env('ATTENDANCE_MACHINE_KEY'),
    ],

];
