<?php

namespace App\Modules\Notify\Exceptions;

use Exception;

class ApiClientException extends Exception
{
    public function __construct(string $url, int $statusCode, string $responseContent)
    {
        parent::__construct("Url: {$url}; Status code: {$statusCode}; Response: {$responseContent}");
    }
}