<?php

namespace App\Modules\Notify\Jobs;

use App\Jobs\AbstractJob;

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
        
    }

    public function failed(): void
    {

    }
    
    public function getNotifySimproWebhookId(): int
    {
        return $this->notifySimproWebhookId;
    }
}
