<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    | On irwebspace shared hosting, PHP's sendmail is always available.
    | MAIL_MAILER defaults to 'sendmail' so mail works without any SMTP config.
    | To switch to SMTP, set MAIL_MAILER=smtp and the SMTP_* variables in .env.
    */
    'default' => env('MAIL_MAILER', 'sendmail'),

    'mailers' => [
        'smtp' => [
            'transport'   => 'smtp',
            'scheme'      => env('MAIL_SCHEME'),
            'url'         => env('MAIL_URL'),
            'host'        => env('MAIL_HOST', '127.0.0.1'),
            'port'        => env('MAIL_PORT', 465),
            'username'    => env('MAIL_USERNAME'),
            'password'    => env('MAIL_PASSWORD'),
            'timeout'     => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path'      => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel'   => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => ['transport' => 'array'],
    ],

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'no-reply@aavaan.com'),
        'name'    => env('MAIL_FROM_NAME', 'آوان'),
    ],
];
