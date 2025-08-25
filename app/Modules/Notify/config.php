<?php

return [
    'root_dir' => __DIR__,

    'storage' => [
        'letter_templates' => [
            'driver' => 'local',
            'root' => storage_path('app/notify/letter_templates'),
        ],
        'pdf_reports' => [
            'driver' => 'local',
            'root' => storage_path('app/notify/pdf_reports'),
        ],
    ],

    'simpro' => [
        'token' => env('NOTIFY_SIMPRO_TOKEN', 'fake_notify_simpro_token'),
        'base_url' => env('NOTIFY_SIMPRO_API_URL', 'https://fake.notify.simpro.url'),
        'company_id' => (int) env('NOTIFY_SIMPRO_COMPANY_ID', 0)
    ],
];
