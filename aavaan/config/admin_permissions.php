<?php

return [
    /*
     * Maps each admin_role to the panel sections it can access.
     * Use '*' to grant access to all sections (super_admin only).
     */
    'super_admin' => ['*'],

    'support' => [
        'dashboard',
        'users',
        'tickets',
        'reports_violations',
        'verifications',
        'search',
        'activity_logs',
    ],

    'finance' => [
        'dashboard',
        'payments',
        'subscriptions',
        'reports',
        'search',
    ],

    'content_moderator' => [
        'dashboard',
        'content',
        'moderation',
        'search',
    ],
];
