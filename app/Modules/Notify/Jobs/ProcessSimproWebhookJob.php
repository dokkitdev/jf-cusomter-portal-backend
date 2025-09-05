<?php

namespace App\Modules\Notify\Jobs;

use App\Jobs\AbstractJob;
use App\Modules\Notify\Services\SimproWebhooks\ProcessSimproWebhookAction;

class ProcessSimproWebhookJob extends AbstractJob
{
    protected int $notifySimproWebhookId;

    public function __construct(int $notifySimproWebhookId)
    {
        $this->notifySimproWebhookId = $notifySimproWebhookId;

        $this->onQueue('notify:process_simpro_webhook');
    }

    public function handle(): void
    {
        app(ProcessSimproWebhookAction::class)->processSimproWebhook($this->notifySimproWebhookId);
    }

    public function failed(): void
    {
        app(ProcessSimproWebhookAction::class)->simproWebhookProcessingFailed($this->notifySimproWebhookId);
    }
    
    public function getNotifySimproWebhookId(): int
    {
        return $this->notifySimproWebhookId;
    }
}
