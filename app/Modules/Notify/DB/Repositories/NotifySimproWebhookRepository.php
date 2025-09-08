<?php

namespace App\Modules\Notify\DB\Repositories;

use App\Modules\Notify\DB\Models\NotifySimproWebhook;
use App\Repositories\BaseRepository;

/**
 * @property NotifySimproWebhook $model
*/
class NotifySimproWebhookRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(NotifySimproWebhook::class);
    }
}
