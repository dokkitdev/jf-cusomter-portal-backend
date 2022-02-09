<?php

namespace App\Tests\Support;

use App\Services\SimproWebhookService;
use RonasIT\Support\Traits\MockClassTrait;

trait SimproWebhookTestTrait
{
    use MockClassTrait;

    public function mockRequestVerification($result)
    {
        $this->mockClass(SimproWebhookService::class, [
            ['method' => 'isWebhookVerified', 'result' => $result]
        ]);
    }
}