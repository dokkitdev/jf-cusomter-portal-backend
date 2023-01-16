<?php

namespace App\Jobs\SyncArchivedAssets;

use App\Jobs\AbstractJob;

class SyncArchivedAssetsProcessAssetJob extends AbstractJob
{
    protected int $simproAssetId;

    public function __construct(int $simproAssetId)
    {
        $this->simproAssetId = $simproAssetId;
    }

    public function handle()
    {
    }

    public function getSimproAssetId(): int
    {
        return $this->simproAssetId;
    }
}
