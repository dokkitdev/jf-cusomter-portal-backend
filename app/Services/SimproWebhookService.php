<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use RonasIT\Support\Services\EntityService;

class SimproWebhookService extends EntityService
{
    protected SimproJobService $simproJobService;

    public function __construct()
    {
        $this->simproJobService = app(SimproJobService::class);
    }

    public function isWebhookVerified(?string $header, string $body): bool
    {
        $webhookSecret = config('services.simpro.webhook_secret');

        if (is_null($webhookSecret)) {
            return true;
        }

        return hash_equals($header, hash_hmac('sha1', $body, $webhookSecret));
    }

    public function process(array $data): Model
    {
        $simproEntityId = null;

        if (($data['name'] === 'Job') || ($data['name'] === 'Job schedule')) {
            $simproEntityId = Arr::get($data, 'reference.jobID');
        }

        return $this->simproJobService->create([
            'data' => $data,
            'simpro_entity_id' => $simproEntityId
        ]);
    }
}
