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


    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],


    'vodacom' => [
        'request_url' => env('VODACOM_REQUEST_URL'),
        'sp_id' => env('VODACOM_SPID'),
        'sp_password' => env('VODACOM_SPPASSWORD'),
        'ips' => [
            '127.0.0.1', //for testing purpose
        ],
        'certs' => [
            'broker' => env('VODACOM_BROKER_CRT'),
            'ssl_key' => env('VODACOM_SLL_KEY'),
            'certificate_authority' => env('VODACOM_CERTIFICATE_AUTHORITY'),
            'ssl_cert' => env('VODACOM_SSL_CERT'),

        ],
    ],

    'airtel' => [
        'request_url' => env('AIRTEL_REQUEST_URL'),
        'api_user' => 'AIRTEL_USERNAME',
        'api_password' => 'AIRTEL_PASSWORD',
        'ips' => [],
    ],
    'pagination' => 25,

    'calin' => [
        'url' => env('CALIN_CLIENT_URL'),
        'key' => env('CALIN_KEY'),
        'api' => env('CALIN_CLIENT_URL'),
        'user_id' => env('CALIN_USER_ID'),
        'meter' => [
            'key' => env('METER_DATA_KEY'),
            'user' => env('METER_DATA_USER'),
            'api' => env('METER_DATA_URL'),
        ],
    ],
    'manufacturer_master_key' => '36dKhvjwE58!M2.A@L', // the key which is required to  add a new  manufacturer

    'sms' => [
        'bongo' => [
            'url' => 'http://www.bongolive.co.tz/api/sendSMS.php',
            'sender' => 'SENDER_NUMBER',
            'username' => 'USER NAME',
            'password' => 'PASSWORD',
            'key' => 'KEY',
        ],
        'android' => [
            'url' => 'https://fcm.googleapis.com/fcm/send',
            'token' => 'FIREBASE_TOKEN',
            'key' => 'PHONE_KEY',
        ],
        'callback' => 'https://mpmanager.local/api/sms/%s/confirm',
    ],

    'queues' => [
        'payment' => env('QUEUE_PAYMENT'),
        'energy' => env('QUEUE_ENERGY'),
        'token' => env('QUEUE_TOKEN'),
        'sms' => env('QUEUE_SMS'),
        'history' => env('QUEUE_HISTORY'),
        'misc' => env('QUEUE_MISC')
    ],

    'weather' => [
        'owm_app_id' => '4a84b68e24abd9d99758a67f8d1d984b',
    ],

    'payment' => [
        'data-stream' => 'placeholder-url',
        'maintenance' => 'placeholder-url',
    ]

];
