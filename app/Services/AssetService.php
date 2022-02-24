<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\Role;
use App\Models\SimproJob;
use App\Repositories\AssetRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * @property AssetRepository $repository
 * @mixin AssetRepository
 */
class AssetService extends BaseService
{
    protected SimproApiClient $simproClient;
    protected SiteService $siteService;
    protected AssetCustomFieldService $assetCustomFieldService;
    protected AssetAttachmentService $assetAttachmentService;
    protected AssetTestRecordService $assetTestRecordService;
    protected int $companyId;

    public function __construct()
    {
        parent::__construct();

        $this->setRepository(AssetRepository::class);

        $this->companyId = config('services.simpro.company_id');

        $this->simproClient = app(SimproApiClient::class);
        $this->siteService = app(SiteService::class);
        $this->assetCustomFieldService = app(AssetCustomFieldService::class);
        $this->assetAttachmentService = app(AssetAttachmentService::class);
        $this->assetTestRecordService = app(AssetTestRecordService::class);
    }

    public function getAssetServiceLevels()
    {
        $assetServiceLevelPages = $this->simproClient->getAssetServiceLevelSetup($this->companyId);

        $serviceLevels = [];
        foreach ($assetServiceLevelPages as $assetServiceLevelPage) {
            $serviceLevels = array_merge($serviceLevels, $assetServiceLevelPage);
        }

        return $serviceLevels;
    }

    public function search(array $filters): LengthAwarePaginator
    {
        $authUser = $this->getAuthUser();

        if ($authUser && ($authUser['role_id'] === Role::CUSTOMER)) {
            $filters['asset_has_user'] = $authUser['id'];
        }

        return $this->repository
            ->with(Arr::get($filters, 'with', []))
            ->searchQuery($filters)
            ->filterBy('asset_test_record.job.stage', 'job_stage')
            ->filterBy('asset_type')
            ->filterBy('custom_asset_type_value')
            ->filterByIntQuery('simpro_asset_id')
            ->filterBy('asset_test_records.job_id')
            ->filterBy('site.customer_id')
            ->filterBy('site_id')
            ->filterBy('archived')
            ->filterByList('service_level_name', 'service_level_names')
            ->filterByQuery(['name'])
            ->filterByQueryWithValue('location', 'location_query')
            ->filterByQueryWithValue('customer_name', 'customer_name_query')
            ->filterByQueryWithValue('make', 'make_query')
            ->filterByQueryWithValue('model', 'model_query')
            ->filterBy('last_test_date')
            ->filterFrom('last_test_date', false, 'last_test_date_from')
            ->filterTo('last_test_date', false, 'last_test_date_to')
            ->filterBy('next_service_date')
            ->filterFrom('next_service_date', false, 'next_service_date_from')
            ->filterTo('next_service_date', false, 'next_service_date_to')
            ->filterByLastTestResult()
            ->filterByOnlyPermitted()
            ->filterBySiteName()
            ->filterBySiteUprn()
            ->filterByCP12Status()
            ->filterByJobDueDate()
            ->filterByJobLoggedCompletionDate()
            ->getSearchResults();
    }

    public function updateOrCreateBySimpro(SimproJob $webhook): Model
    {
        $companyId = 0;
        $assetId = $this->getAssetId($webhook);

        return $this->createOrUpdateAsset($companyId, $assetId);
    }

    public function deleteBySimpro(SimproJob $webhook): int
    {
        $simproAssetId = $this->getAssetId($webhook);

        return $this->repository->delete([
            'simpro_asset_id' => $simproAssetId,
        ]);
    }

    protected function createOrUpdateAsset(int $companyId, int $simproAssetId): Model
    {
        $simproAsset = $this->simproClient->getAsset($companyId, $simproAssetId);

        $simproSiteId = $simproAsset['Site']['ID'];

        $site = $this->siteService->firstOrCreateBySimpro($companyId, $simproSiteId);

        $asset = $this->createOrUpdate($companyId, $simproAsset, $site['id'], $site['simpro_site_id']);

        $this->assetCustomFieldService->syncByAsset($simproAsset, $asset['id']);

        $this->assetAttachmentService->syncByAsset($companyId, $simproSiteId, $simproAssetId, $asset['id']);

        $this->assetTestRecordService->syncByAsset($companyId, $simproSiteId, $simproAssetId, $asset['id']);

        return $asset;
    }

    protected function createOrUpdate(int $companyId, array $simproAsset, int $siteId, int $simproSiteId): Model
    {
        $serviceLevel = $this->findRecentServiceLevel($this->simproClient->getAssetServiceLevels($companyId, $simproSiteId, $simproAsset['ID']));

        $locationCustomField = $this->findCustomFieldByName(Arr::get($simproAsset, 'CustomFields'), 'Location');
        $makeCustomField = $this->findCustomFieldByName(Arr::get($simproAsset, 'CustomFields'), 'Make');
        $modelCustomField = $this->findCustomFieldByName(Arr::get($simproAsset, 'CustomFields'), 'Model');

        $assetTypeCustomField = $this->findCustomFieldById(Arr::get($simproAsset, 'CustomFields'), 15);
        $cp12CustomField = $this->findCustomFieldById(Arr::get($simproAsset, 'CustomFields'), 59);

        return $this->repository->updateOrCreate([
            'simpro_asset_id' => $simproAsset['ID'],
        ], [
            'site_id' => $siteId,
            'name' => Arr::get($simproAsset, 'AssetType.Name'),
            'asset_type' => Arr::get($simproAsset, 'AssetType.ID'),
            'customer_name' => Arr::get($simproAsset, 'CustomerContract.Name'),
            'last_test_date' => Arr::get($simproAsset, 'LastTest.Date'),
            'next_service_date' => Arr::get($serviceLevel, 'ServiceDate'),
            'last_test_result' => Arr::get($simproAsset, 'LastTest.Result'),
            'service_level_name' => Arr::get($serviceLevel, 'ServiceLevel.Name'),
            'archived' => $simproAsset['Archived'],
            'location' => Arr::get($locationCustomField, 'Value'),
            'make' => Arr::get($makeCustomField, 'Value'),
            'model' => Arr::get($modelCustomField, 'Value'),
            'last_cp12_date' => Arr::get($cp12CustomField, 'Value', Arr::get($simproAsset, 'LastTest.Date')),
            'custom_asset_type_value' => Arr::get($assetTypeCustomField, 'Value')
        ]);
    }

    protected function getAssetId(SimproJob $webhook): string
    {
        preg_match('/(\d+)/', $webhook['data']['description'], $matches);

        return $matches[0];
    }

    protected function findRecentServiceLevel(array $serviceLevels): ?array
    {
        return collect($serviceLevels)->sortByDesc('ServiceDate')->first();
    }

    protected function findCustomFieldByName(array $customFields, string $name): ?array
    {
        return collect($customFields)->first(function ($customField) use ($name) {
            return Arr::get($customField, 'CustomField.Name') === $name;
        });
    }

    protected function findCustomFieldById(array $customFields, int $id): ?array
    {
        return collect($customFields)->first(function ($customField) use ($id) {
            return Arr::get($customField, 'CustomField.ID') === $id;
        });
    }
}
