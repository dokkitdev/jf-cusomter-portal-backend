<?php

namespace App\Http\Requests\Jobs;

use App\Http\Requests\Request;

class SearchJobRequest extends Request
{
    public function rules(): array
    {
        return [
            'simpro_job_id' => 'integer',
            'customer_id' => 'integer',
            'site_id' => 'integer',
            'order_no' => 'string',
            'customer_name' => 'string',
            'site_name' => 'string',
            'uprn' => 'string',
            'postal_code' => 'string',
            'priority' => 'array',
            'priority.*' => 'string',
            'cost_center_name' => 'array',
            'cost_center_name.*' => 'string',
            'stage' => 'array',
            'stage.*' => 'string',
            'job_status' => 'array',
            'job_status.*' => 'string',
            'appointment_from' => 'date',
            'appointment_to' => 'date',
            'start_time_from' => 'date',
            'start_time_to' => 'date',
            'end_time_from' => 'date',
            'end_time_to' => 'date',
            'date_created_from' => 'date',
            'date_created_to' => 'date',
            'made_safe_date_from' => 'date',
            'made_safe_date_to' => 'date',
            'completion_date_from' => 'date',
            'completion_date_to' => 'date',
            'due_date_from' => 'date',
            'due_date_to' => 'date',
            'page' => 'integer',
            'per_page' => 'integer',
            'all' => 'integer',
            'query' => 'string',
            'order_by' => 'string',
            'desc' => 'boolean',
            'with' => 'array',
            'with.*' => 'string|in:site,customer,recent_schedule,schedules,job_catalogs,job_attachments,job_work_orders,job_no_access_dates',
            'with_count' => 'array',
            'with_count.*' => 'string|in:job_attachments'
        ];
    }
}