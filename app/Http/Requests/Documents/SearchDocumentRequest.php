<?php

namespace App\Http\Requests\Documents;

use App\Http\Requests\Request;

class SearchDocumentRequest extends Request
{
    public function rules(): array
    {
        return [
            'created_at_from' => 'date',
            'created_at_to' => 'date',
            'title_query' => 'string',
            'page' => 'integer',
            'per_page' => 'integer',
            'all' => 'integer',
            'query' => 'string',
            'order_by' => 'string',
            'desc' => 'boolean',
            'with' => 'array',
            'with.*' => 'string|in:media'
        ];
    }
}