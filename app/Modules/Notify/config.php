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
        'csv_reports' => [
            'driver' => 'local',
            'root' => storage_path('app/notify/csv_reports'),
        ],
    ],

    'simpro' => [
        'token' => env('NOTIFY_SIMPRO_TOKEN', 'fake_notify_simpro_token'),
        'base_url' => env('NOTIFY_SIMPRO_API_URL', 'https://fake.notify.simpro.url'),
        'company_id' => (int) env('NOTIFY_SIMPRO_COMPANY_ID', 0)
    ],

    'zero_reports' => [
        'job_customer_id' => (int) env('NOTIFY_ZERO_REPORTS_JOB_CUSTOMER_ID', 11514),
    ],
];
