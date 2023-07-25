<?php

return [
    'models' => [
        'user' => \RodosGrup\IyziLaravel\Models\IyzicoUser::class,
        'cards' => \RodosGrup\IyziLaravel\Models\StoredCreditCard::class
    ],
    'apiKey' => env('IYZI_API_KEY', null),
    'secretKey' => env('IYZI_SECRET_KEY', null),
    'baseUrl' => env('IYZI_BASE_URL', null),
    'billingName' => env('BILLING_NAME', null),
    'billingCity' => env('BILLING_CITY', null),
    'billingAddress' => env('BILLING_ADDRESS', null),
    'returnUrl' => env('IYZI_RETURN_URL', null),
];
