<?php

return [
    'artist_subscription' => [
        'monthly_price' => (int) env('SUBSCRIPTION_MONTHLY_PRICE', 150000),
        'yearly_price'  => (int) env('SUBSCRIPTION_YEARLY_PRICE', 1500000),
    ],
    'production_access' => [
        'single_price'    => (int) env('ACCESS_SINGLE_PRICE', 200000),
        'bundle_5_price'  => (int) env('ACCESS_BUNDLE_5_PRICE', 850000),
        'bundle_10_price' => (int) env('ACCESS_BUNDLE_10_PRICE', 1500000),
    ],
    'upload' => [
        'max_avatar_kb'       => 2048,
        'max_portfolio_kb'    => 5120,
        'max_reel_mb'         => 100,
        'max_portfolio_items' => 10,
        'allowed_image_types' => ['jpg', 'jpeg', 'png', 'webp'],
    ],
    'artistic_fields' => [
        'بازیگری', 'کارگردانی', 'فیلمبرداری', 'تصویربرداری',
        'موسیقی', 'طراحی صحنه', 'طراحی لباس', 'گریم',
        'تدوین', 'صداگذاری', 'نویسندگی', 'تئاتر',
        'رقص و کوریوگرافی', 'سایر',
    ],
];
