<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use RonasIT\Support\Services\EntityService;

class SimproWebhookService extends EntityService
{
    protected SimproJobService $simproJobService;
    protected AssetService $assetService;
    protected CustomerService $customerService;

    public function __construct()
    {
        $this->simproJobService = app(SimproJobService::class);
        $this->assetService = app(AssetService::class);
        $this->customerService = app(CustomerService::class);
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

        if ($data['name'] === 'Job') {
            if ($data['ID'] === 'job.asset.tested') {
                $regexpResult = preg_match('/^Asset #(.+) on .*$/', $data['description'], $matches);

                if ($regexpResult && isset($matches[1])) {
                    $simproEntityId = $matches[1];
                }
            } else {
                $simproEntityId = Arr::get($data, 'reference.jobID');
            }
        }

        if ($data['name'] === 'Job schedule') {
            $simproEntityId = Arr::get($data, 'reference.scheduleID');
        }

        if ($data['name'] === 'Asset') {
            $simproEntityId = $this->assetService->getAssetId(Arr::get($data, 'description'));
        }

        if ($data['name'] === 'Site') {
            $simproEntityId = Arr::get($data, 'reference.siteID');
        }

        if (($data['name'] === 'Company customer') || ($data['name'] === 'Individual customer')) {
            $simproEntityId = $this->customerService->getCustomerId(Arr::get($data, 'description'));
        }

        return $this->simproJobService->create([
            'data' => $data,
            'simpro_entity_id' => $simproEntityId
        ]);
    }
}
