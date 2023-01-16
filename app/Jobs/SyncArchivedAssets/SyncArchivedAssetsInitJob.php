<?php

namespace App\Jobs\SyncArchivedAssets;

use App\Jobs\AbstractJob;
use App\LogicServices\SyncArchivedAssetsLogicService;

class SyncArchivedAssetsInitJob extends AbstractJob
{
    public $queue = 'sync_archived_assets:init';

    public function handle()
    {
        app(SyncArchivedAssetsLogicService::class)->init();
    }
}
