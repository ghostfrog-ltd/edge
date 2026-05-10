<?php

return [
    'plans' => [
        'free' => [
            'name' => 'Free',
            'price_label' => 'PS0',
            'monthly_credits' => 10,
            'credits_label' => 'Small starter allowance',
            'summary' => 'A lightweight try-before-you-pay tier so sellers can understand the workflow before buying credits or a subscription.',
            'stripe_price_id' => null,
        ],
        'starter' => [
            'name' => 'Starter',
            'price_label' => 'PS29 / month',
            'monthly_credits' => 50,
            'credits_label' => '50 credits each month',
            'summary' => 'For solo sellers who want regular niche scans without team complexity.',
            'stripe_price_id' => env('STRIPE_PRICE_STARTER'),
        ],
        'pro' => [
            'name' => 'Pro',
            'price_label' => 'PS79 / month',
            'monthly_credits' => 200,
            'credits_label' => '200 credits each month',
            'summary' => 'For power sellers who need deeper usage and more frequent report runs.',
            'stripe_price_id' => env('STRIPE_PRICE_PRO'),
        ],
        'team' => [
            'name' => 'Team',
            'price_label' => 'PS199 / month',
            'monthly_credits' => 600,
            'credits_label' => '600 pooled credits each month',
            'summary' => 'For shared workspaces, agencies, and higher-volume operators.',
            'stripe_price_id' => env('STRIPE_PRICE_TEAM'),
        ],
    ],

    'top_ups' => [
        'boost_25' => [
            'name' => '25-credit boost',
            'price_label' => 'PS15 one-off',
            'credits' => 25,
            'summary' => 'A small refill for testing, urgent rescans, or one-off client work.',
            'stripe_price_id' => env('STRIPE_PRICE_TOP_UP_25'),
        ],
        'boost_100' => [
            'name' => '100-credit boost',
            'price_label' => 'PS49 one-off',
            'credits' => 100,
            'summary' => 'The sensible mid-pack for extra research bursts without changing subscription tier.',
            'stripe_price_id' => env('STRIPE_PRICE_TOP_UP_100'),
        ],
        'boost_250' => [
            'name' => '250-credit boost',
            'price_label' => 'PS99 one-off',
            'credits' => 250,
            'summary' => 'For bigger catalog pushes, agency work, or concentrated analysis windows.',
            'stripe_price_id' => env('STRIPE_PRICE_TOP_UP_250'),
        ],
    ],
];
