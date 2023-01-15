<?php

namespace App\LogicServices;

use App\ApiClients\SimproApiClient;
use App\Jobs\SyncArchivedAssets\SyncArchivedAssetsProcessPageJob;
use App\Services\AssetService;

class SyncArchivedAssetsLogicService
{
    protected SimproApiClient $simproApiClient;
    protected AssetService $assetService;

    protected int $companyId;

    public function __construct()
    {
        $this->simproApiClient = app(SimproApiClient::class);
        $this->assetService = app(AssetService::class);

        $this->companyId = config('services.simpro.company_id');
    }

    public function init()
    {
        $pagesCount = $this->simproApiClient->getArchivedAssetPagesCount($this->companyId);

        if (is_null($pagesCount)) {
            return;
        }

        for ($page = 1; $page <= $pagesCount; $page++) {
            dispatch(new SyncArchivedAssetsProcessPageJob($page));
        }
    }
}
