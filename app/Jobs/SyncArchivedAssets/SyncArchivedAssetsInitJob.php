<?php

namespace App\Jobs\SyncArchivedAssets;

use App\Jobs\AbstractJob;
use App\LogicServices\SyncArchivedAssetsLogicService;

class SyncArchivedAssetsInitJob extends AbstractJob
{
    public function handle()
    {
        app(SyncArchivedAssetsLogicService::class)->init();
    }
}
