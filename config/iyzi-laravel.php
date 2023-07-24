<?php

return [
    'models' => [
        'user' => \RodosGrup\IyziLaravel\Models\IyzicoUser::class,
        'cards' => \RodosGrup\IyziLaravel\Models\StoredCreditCard::class
    ],
    'apiKey' => env('IYZI_API_KEY', ''),
    'secretKey' => env('IYZI_SECRET_KEY', ''),
    'baseUrl' => env('IYZI_BASE_URL', ''),
    'billingName' => env('BILLING_NAME', ''),
    'billingCity' => env('BILLING_CITY'),
    'billingAddress' => env('BILLING_ADDRESS')
];
