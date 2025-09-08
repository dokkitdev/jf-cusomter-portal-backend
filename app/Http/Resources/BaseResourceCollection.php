<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BaseResourceCollection extends ResourceCollection
{
    public function paginationInformation(Request $request, array $paginationData): array
    {
        return [
            'current_page' => $paginationData['current_page'],
            'first_page_url' => $paginationData['first_page_url'],
            'from' => $paginationData['from'],
            'last_page' => $paginationData['last_page'],
            'last_page_url' => $paginationData['last_page_url'],
            'links' => $paginationData['links'],
            'next_page_url' => $paginationData['next_page_url'],
            'path' => $paginationData['path'],
            'per_page' => $paginationData['per_page'],
            'prev_page_url' => $paginationData['prev_page_url'],
            'to' => $paginationData['to'],
            'total' => $paginationData['total'],
        ];
    }
}
