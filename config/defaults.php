<?php

return [
    'items_per_page' => 10,
    'permitted_media_types' => ['jpg', 'jpeg', 'bmp', 'png', 'pdf', 'doc', 'docx'],

    /*
    |--------------------------------------------------------------------------
    | Password hash lifetime, hours
    |--------------------------------------------------------------------------
    |
    | Here you can set how often "set_password_hash" field of "users" table will be clearing.
    |
    */

    'password_hash_lifetime' => env('PASSWORD_HASH_LIFETIME', 1),

    'site_uprn_custom_field_id' => 8,
    'customer_job_request_tag' => 21,
    'call_center_job_request_tag' => 22,
    'repair_job_custom_field_ids' => [8, 9],
    'job_cost_center_id' => 8,
    'job_due_date_interval_hours' => 24,

    '2fa_code_ttl_minutes' => (int) env('2FA_CODE_TTL_MINUTES', 10),
];
