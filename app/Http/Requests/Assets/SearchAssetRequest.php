<?php

namespace App\Http\Requests\Assets;

use App\Http\Requests\Request;

class SearchAssetRequest extends Request
{
    public function rules(): array
    {
        $with = implode(',', [
            'site',
            'site.primary_site_contact',
            'site.customers',
            'site.customer',
            'asset_custom_fields',
            'asset_attachments',
            'asset_test_records',
            'asset_test_records.job',
            'asset_test_records.asset_test_record_readings',

            'asset_test_record',
            'job',
            'job_customer',
            'job.job_no_access_dates',
            'next_schedule'
        ]);

        return [
            'report' => 'boolean',
            'site_name_query' => 'string',
            'site_uprn_query' => 'string',
            'cp12_status' => 'string',
            'asset_type' => 'integer',
            'custom_asset_type_value' => 'array',
            'custom_asset_type_value.*' => 'string',
            'job_stage' => 'array',
            'job_stage.*' => 'string',
            'job_due_date_from' => 'date',
            'job_due_date_to' => 'date',
            'job_logged_completion_date_from' => 'date',
            'job_logged_completion_date_to' => 'date',
            'customer_name_query' => 'string',
            'location_query' => 'string',
            'make_query' => 'string',
            'model_query' => 'string',
            'simpro_asset_id' => 'integer',
            'customer_id' => 'integer',
            'job_customer_id' => 'integer',
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
            'names' => 'array',
            'names.*' => 'string',
            'page' => 'integer',
            'per_page' => 'integer',
            'all' => 'integer',
            'query' => 'string',
            'order_by' => 'string',
            'desc' => 'boolean',
            'with' => 'array',
            'with.*' => "string|in:{$with}",
        ];
    }
}
