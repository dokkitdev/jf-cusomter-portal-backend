<?php

namespace App\Modules\Notify\Services\SimproWebhooks;

use App\Modules\Notify\DB\Models\NotifySimproWebhook;
use App\Modules\Notify\DB\Services\NotifySimproWebhookService;
use App\Modules\Notify\Jobs\ProcessSimproWebhookJob;

class RegisterSimproWebhookAction
{
    protected NotifySimproWebhookService $notifySimproWebhookService;

    public function __construct()
    {
        $this->notifySimproWebhookService = app(NotifySimproWebhookService::class);
    }

    public function handle(array $webhookData): void
    {
        /** @var NotifySimproWebhook $webhook */
        $webhook = $this->notifySimproWebhookService->create([
            'data' => $webhookData,
            'status' => NotifySimproWebhook::STATUS_PENDING,
        ]);

        dispatch(new ProcessSimproWebhookJob($webhook->id));
    }
}
