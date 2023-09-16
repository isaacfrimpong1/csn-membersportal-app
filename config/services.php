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

    'firebase' => [
    'database_url' => env('https://membersportal-76cc1-default-rtdb.europe-west1.firebasedatabase.app'),
    'project_id' => env('membersportal-76cc1'),
    'storage_bucket' => env('https://membersportal-76cc1-default-rtdb.europe-west1.appspot.com'),
    'credentials' => [
        'file' => env('membersportal-app/firebasePrivateKey/membersportal-76cc1-firebase-adminsdk-cmiry-47be31277a.json'),
        'auto_discovery' => true,
        ],
    ],

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

];
