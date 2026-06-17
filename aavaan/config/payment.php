<?php

return [
    'driver' => env('PAYMENT_DRIVER', 'zarinpal'),
    'zarinpal' => [
        'merchant_id' => env('ZARINPAL_MERCHANT_ID', ''),
        'sandbox' => env('ZARINPAL_SANDBOX', true),
    ],
];
