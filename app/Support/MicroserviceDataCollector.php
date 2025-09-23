<?php

namespace App\Support;

use RonasIT\Support\DataCollectors\RemoteDataCollector;

class MicroserviceDataCollector extends RemoteDataCollector
{
    public function getDocumentation(): array
    {
        $response = $this->httpRequestService->get($this->getUrl());
        $documentation = $response->json();

        $customPaths = config('auto-doc.paths', []);

        if (!isset($documentation['paths'])) {
            $documentation['paths'] = [];
        }

        foreach ($customPaths as $path => $methods) {
            $documentation['paths'][$path] = $methods;
        }

        return $documentation;
    }
}
