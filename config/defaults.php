<?php

use App\Models\PrivateContract;
use App\Models\PrivateContractCostCenterItem;

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

    'private_contract_cost_center_item' => [
        PrivateContractCostCenterItem::TYPE_PREBUILDS => [
            'order' => 1,
            'key_name' => 'Prebuild.Name',
        ],
        PrivateContractCostCenterItem::TYPE_CATALOGS => [
            'order' => 2,
            'key_name' => 'Catalog.Name',
        ],
        PrivateContractCostCenterItem::TYPE_ONEOFF => [
            'order' => 3,
            'key_name' => 'Description',
        ],
        PrivateContractCostCenterItem::TYPE_DISCOUNT => [
            'order' => 4,
            'inc_tax_coef' => 0.2,
        ],
    ],

    'private_contract' => [
        'templates' => [
            'names' => [
                PrivateContract::TEMPLATE_TYPE_ANNUAL_PAYMENT => 'annual-contracts.docx',
                PrivateContract::TEMPLATE_TYPE_DIRECT_DEBIT => 'direct-debit-contracts.docx',
           ],
            'permitted_template_types' => ['docx'],
        ],
    ],
];
