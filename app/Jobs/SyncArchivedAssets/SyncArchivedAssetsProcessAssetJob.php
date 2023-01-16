<?php

namespace App\Jobs\SyncArchivedAssets;

use App\Jobs\AbstractJob;
use App\LogicServices\SyncArchivedAssetsLogicService;

class SyncArchivedAssetsProcessAssetJob extends AbstractJob
{
    protected int $simproAssetId;

    public function __construct(int $simproAssetId)
    {
        $this->simproAssetId = $simproAssetId;
    }

    public function handle()
    {
        app(SyncArchivedAssetsLogicService::class)->processAsset($this->simproAssetId);
    }

    public function getSimproAssetId(): int
    {
        return $this->simproAssetId;
    }
}
