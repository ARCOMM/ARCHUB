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
        'model' => App\Models\Portal\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'discord' => [
        'client_id' => env('DISCORD_CLIENT_ID'),
        'client_secret' => env('DISCORD_CLIENT_SECRET'),
        'redirect' => env('DISCORD_REDIRECT_URI'),
        'token' => env('DISCORD_TOKEN'),

        'server_id' => env('DISCORD_SERVER_ID'),
        'recruit_role' => env('DISCORD_RECRUIT_ROLE'),
        'member_role' => env('DISCORD_MEMBER_ROLE'),
        'retired_role' => env('DISCORD_RETIRED_ROLE'),
        'tester_role' => env("DISCORD_TESTER_ROLE"),
        'senior_tester_role' => env("DISCORD_SENIOR_TESTER_ROLE"),
        'operations_role' => env("DISCORD_OPERATIONS_ROLE"),
        'recruiter_role' => env("DISCORD_RECRUITER_ROLE"),
        'staff_role' => env("DISCORD_STAFF_ROLE"),
        'admin_role' => env("DISCORD_ADMIN_ROLE"),

        'archub_webhook' => env('DISCORD_ARCHUB_WEBHOOK'),
        'staff_webhook' => env('DISCORD_STAFF_WEBHOOK'),
    ],
];
