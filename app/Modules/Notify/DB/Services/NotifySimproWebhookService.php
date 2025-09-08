<?php

namespace App\Modules\Notify\DB\Services;

use App\Modules\Notify\DB\Repositories\NotifySimproWebhookRepository;
use App\Services\BaseService;

/**
 * @property NotifySimproWebhookRepository $repository
 * @mixin NotifySimproWebhookRepository
 */
class NotifySimproWebhookService extends BaseService
{
    public function __construct()
    {
        parent::__construct();

        $this->setRepository(NotifySimproWebhookRepository::class);
    }
}
