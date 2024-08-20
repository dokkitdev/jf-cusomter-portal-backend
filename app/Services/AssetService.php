<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\Asset;
use App\Models\Role;
use App\Models\SimproJob;
use App\Repositories\AssetRepository;
use Exception;
use Generator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

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

    public function getAssetTypes()
    {
        $assetTypes = $this->simproClient->getAssetTypeSetup($this->companyId);

        return $assetTypes['ListItems'];
    }

    public function getAssetNames()
    {
        $assetNames = $this->simproClient->getAssetNames($this->companyId);

        return $assetNames;
    }

    public function search(array $filters): LengthAwarePaginator
    {
        $filters = $this->prepareSearchFilters($filters);

        $assets = $this->repository
            ->searchQuery($filters)
            ->getSearchResults();

        if (Arr::get($filters, 'report')) {
            $assets->map(function (Asset $asset) {
                return $asset->append('cp12_status');
            });
        }

        return $assets;
    }

    public function iterateByFilters(array $filters, ?int $chunkSize = null): Generator
    {
        $filters = $this->prepareSearchFilters($filters);

        $assets = $this->repository
            ->searchQuery($filters)
            ->iterateSearchResults($chunkSize);

        if (Arr::get($filters, 'report')) {
            foreach ($assets as $asset) {
                $asset->append('cp12_status');

                yield $asset;
            }
        } else {
            yield from $assets->getIterator();
        }
    }

    public function updateOrCreateBySimpro(SimproJob $webhook): Model
    {
        $companyId = 0;
        $assetId = $this->getAssetId(Arr::get($webhook, 'data.description'));

        return $this->createOrUpdateAsset($companyId, $assetId);
    }

    public function deleteBySimpro(SimproJob $webhook): int
    {
        $simproAssetId = $this->getAssetId(Arr::get($webhook, 'data.description'));

        return $this->repository->delete([
            'simpro_asset_id' => $simproAssetId,
        ]);
    }

    public function processAssetTestedWebhook(SimproJob $webhook): void
    {
        $companyId = $this->companyId;
        $simproAssetId = $webhook['simpro_entity_id'];

        $this->createOrUpdateAsset($companyId, $simproAssetId);
    }

    public function createOrUpdateAsset(int $companyId, int $simproAssetId): Model
    {
        $simproAsset = $this->simproClient->getAsset($companyId, $simproAssetId);

        $simproSiteId = $simproAsset['Site']['ID'];

        $site = $this->siteService->firstOrCreateBySimpro($companyId, $simproSiteId);

        $asset = $this->createOrUpdate($companyId, $simproAsset, $site['id'], $site['simpro_site_id']);

        $this->assetCustomFieldService->syncByAsset($simproAsset, $asset['id']);

        $this->assetAttachmentService->syncByAsset($companyId, $simproSiteId, $simproAssetId, $asset['id']);

        $this->assetTestRecordService->syncByAsset($companyId, $simproSiteId, $simproAssetId, $asset['id']);

        $asset = $this->updateReportFields($asset);

        return $asset;
    }

    protected function prepareSearchFilters(array $filters): array
    {
        $authUser = $this->getAuthUser();

        if ($authUser && ($authUser['role_id'] === Role::CUSTOMER)) {
            $filters['asset_has_user'] = $authUser['id'];
        }

        if (Arr::get($filters, 'report')) {
            $filters['with'][] = 'job';
        }

        return $filters;
    }

    protected function createOrUpdate(int $companyId, array $simproAsset, int $siteId, int $simproSiteId): Model
    {
        $serviceLevel = $this->findRecentServiceLevel($this->simproClient->getAssetServiceLevels($companyId, $simproSiteId, $simproAsset['ID']));

        $locationCustomField = $this->findCustomFieldByName(Arr::get($simproAsset, 'CustomFields'), 'Location');
        $makeCustomField = $this->findCustomFieldByName(Arr::get($simproAsset, 'CustomFields'), 'Make');
        $modelCustomField = $this->findCustomFieldByName(Arr::get($simproAsset, 'CustomFields'), 'Model');

        $assetTypeCustomField = $this->findCustomFieldById(Arr::get($simproAsset, 'CustomFields'), 15);
        $cp12CustomField = $this->findCustomFieldById(Arr::get($simproAsset, 'CustomFields'), 59);

        $expiryDateCustomField = $this->findCustomFieldByContainedString(Arr::get($simproAsset, 'CustomFields'), 'Expiry Date');

        $cp12CustomFieldValue = Arr::get($cp12CustomField, 'Value');

        if (empty($cp12CustomFieldValue)) {
            $lastCp12Date = null;
        } else {
            try {
                $lastCp12Date = Carbon::parse($cp12CustomFieldValue);
            } catch (Exception $exception) {
                try {
                    $lastCp12Date = Carbon::createFromFormat('d/m/Y', $cp12CustomFieldValue);
                } catch (Exception $exception) {
                    $lastCp12Date = null;
                }
            }
        }

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
            'last_cp12_date' => $lastCp12Date,
            'custom_asset_type_value' => Arr::get($assetTypeCustomField, 'Value'),
            'expiry_date' => Arr::get($expiryDateCustomField, 'Value'),
        ]);
    }

    public function updateReportFields(Model $asset): Model
    {
        $data = [];

        $assetTestRecord = $this->assetTestRecordService->getAssetTestRecordForReport($asset['id']);

        $data['asset_test_record_id'] = Arr::get($assetTestRecord, 'id');
        $data['job_id'] = Arr::get($assetTestRecord, 'job_id');
        $data['customer_id'] = Arr::get($assetTestRecord, 'job.customer.id');
        $data['next_schedule_id'] = Arr::get($assetTestRecord, 'job.next_schedule.id');

        $data['no_access_date_1'] = null;
        $data['no_access_date_2'] = null;
        $data['no_access_date_3'] = null;
        $data['no_access_date_4'] = null;
        $data['no_access_date_5'] = null;

        if (Arr::has($assetTestRecord, 'job.job_no_access_dates')) {
            $noAccessDates = Arr::get($assetTestRecord, 'job.job_no_access_dates');

            $noAccessDatesSorted = $noAccessDates->sortBy('id');

            $index = 1;

            foreach ($noAccessDatesSorted as $value) {
                $data["no_access_date_{$index}"] = $value['date'];

                if ($index === 5) {
                    break;
                }

                $index++;
            }
        }

        return $this->repository->retryForeignKeyViolation(function () use ($asset, $data) {
            $testRecordDate = $this->assetTestRecordService->getAssetTestRecordDateForReport($asset['id']);

            $data['test_record_date'] = $testRecordDate;
            $data['sortable_date'] = $asset['last_test_date'] ?? $asset['last_cp12_date'] ?? $testRecordDate ?? null;

            return $this->repository->update($asset['id'], $data);
        });
    }

    public function updateReportFieldsFromJob(int $jobId): void
    {
        $this->repository->get(['job_id' => $jobId])->each(function (Asset $asset) {
            $this->updateReportFields($asset);
        });
    }

    public function getAssetId(string $description): string
    {
        preg_match('/(\d+)/', $description, $matches);

        return $matches[0];
    }

    protected function findRecentServiceLevel(?array $serviceLevels): ?array
    {
        return collect($serviceLevels)->sortByDesc('ServiceDate')->first();
    }

    protected function findCustomFieldByName(array $customFields, string $name): ?array
    {
        return collect($customFields)->first(function ($customField) use ($name) {
            return Arr::get($customField, 'CustomField.Name') === $name;
        });
    }

    protected function findCustomFieldByContainedString(array $customFields, string $string): ?array
    {
        return collect($customFields)->first(function ($customField) use ($string) {
            return Str::contains(Arr::get($customField, 'CustomField.Name', ''), $string);
        });
    }

    protected function findCustomFieldById(array $customFields, int $id): ?array
    {
        return collect($customFields)->first(function ($customField) use ($id) {
            return Arr::get($customField, 'CustomField.ID') === $id;
        });
    }
}
