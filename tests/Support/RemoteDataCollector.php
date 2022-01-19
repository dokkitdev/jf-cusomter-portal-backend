<?php

namespace App\Tests\Support;

class RemoteDataCollector extends \RonasIT\Support\DataCollectors\RemoteDataCollector
{
    public function getDocumentation(): array
    {
        $response = $this->httpRequestService->get($this->getUrl());

        return $response->json();
    }

}