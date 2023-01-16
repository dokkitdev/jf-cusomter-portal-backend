<?php

namespace App\LogicServices;

use App\ApiClients\SimproApiClient;
use App\Jobs\SyncArchivedAssets\SyncArchivedAssetsProcessAssetJob;
use App\Jobs\SyncArchivedAssets\SyncArchivedAssetsProcessPageJob;
use App\Services\AssetService;
use Illuminate\Support\Arr;

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

    public function processPage(int $page)
    {
        $simproAssetIds = Arr::pluck($this->simproApiClient->getArchivedAssets($this->companyId, $page), 'ID');

        foreach ($simproAssetIds as $simproAssetId) {
            $asset = $this->assetService->first([
                'simpro_asset_id' => $simproAssetId,
            ]);

            if (!empty($asset)) {
                if ($asset['archived'] === false) {
                    dispatch(new SyncArchivedAssetsProcessAssetJob($simproAssetId));
                }
            }
        }
    }
}
