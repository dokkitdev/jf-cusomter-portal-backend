<?php

namespace App\Tests\Support;

use App\Models\SimproJob;

trait CreateSimproJobTrait
{
    protected function createSimproJob($fixture)
    {
        $webhookData = $this->getJsonFixture($fixture);
        $webhookData['data'] = json_decode($webhookData['data'], true);
        SimproJob::create($webhookData);
    }
}