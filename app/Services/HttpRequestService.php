<?php

namespace App\Services;

use RonasIT\Support\Services\HttpRequestService as RonasHttpRequestService;

class HttpRequestService extends RonasHttpRequestService
{
    public function jsonOrNull(): ?array
    {
        $stringResponse = (string) $this->response->getBody();

        return json_decode($stringResponse, true);
    }
}