<?php

namespace App\Http\Requests\Assets;

use App\Http\Requests\Request;

class SearchAssetRequest extends Request
{
    public function rules(): array
    {
        return [
            'customer_name_query' => 'string',
            'location_query' => 'string',
            'make_query' => 'string',
            'model_query' => 'string',
            'simpro_asset_id' => 'integer',
            'customer_id' => 'integer',
            'site_id' => 'integer',
            'archived' => 'boolean',
            'last_test_date' => 'date',
            'last_test_date_from' => 'date',
            'last_test_date_to' => 'date',
            'next_service_date' => 'date',
            'next_service_date_from' => 'date',
            'next_service_date_to' => 'date',
            'last_test_result_query' => 'string',
            'service_level_names' => 'array',
            'service_level_names.*' => 'string',
            'page' => 'integer',
            'per_page' => 'integer',
            'all' => 'integer',
            'query' => 'string',
            'order_by' => 'string',
            'desc' => 'boolean',
            'with' => 'array',
            'with.*' => 'string|in:site,site.customers,asset_custom_fields,asset_attachments,asset_test_records,asset_test_records.job,asset_test_records.asset_test_record_readings',
        ];
    }
}
