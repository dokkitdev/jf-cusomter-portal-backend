<?php

namespace App\Http\Requests\Sites;

use App\Http\Requests\Request;

class SearchSiteRequest extends Request
{
    public function rules(): array
    {
        return [
            'simpro_site_id' => 'integer',
            'customer_ids' => 'array',
            'customer_ids.*' => 'integer',
            'postal_code' => 'string',
            'primary_contact_query' => 'string',
            'has_open_jobs' => 'boolean',
            'name_query' => 'string',
            'uprn_query' => 'string',
            'page' => 'integer',
            'per_page' => 'integer',
            'all' => 'integer',
            'query' => 'string',
            'order_by' => 'string',
            'desc' => 'boolean',
            'with' => 'array',
            'with.*' => "string|in:site_contacts,primary_site_contact,customers",
            'with_count' => 'array',
            'with_count.*' => 'string|in:open_jobs'
        ];
    }
}