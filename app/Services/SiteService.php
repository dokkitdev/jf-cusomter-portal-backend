<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\Role;
use App\Models\SimproJob;
use App\Repositories\SiteRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * @property SiteRepository $repository
 * @mixin SiteRepository
 */
class SiteService extends BaseService
{
    protected CustomerService $customerService;
    protected SimproApiClient $simproClient;
    protected int $companyId;
    protected SiteContactService $siteContactService;

    public function __construct()
    {
        parent::__construct();

        $this->setRepository(SiteRepository::class);

        $this->simproClient = app(SimproApiClient::class);
        $this->companyId = config('services.simpro.company_id');
        $this->customerService = app(CustomerService::class);
        $this->siteContactService = app(SiteContactService::class);
    }

    public function search(array $filters): LengthAwarePaginator
    {
        $authUser = $this->getAuthUser();

        if ($authUser['role_id'] === Role::CUSTOMER) {
            $filters['site_has_user'] = $authUser['id'];
        }

        return $this->repository
            ->with(Arr::get($filters, 'with', []))
            ->withCount(Arr::get($filters, 'with_count', []))
            ->searchQuery($filters)
            ->filterByIntQuery('simpro_site_id')
            ->filterByList('customers.customer_id', 'customer_ids')
            ->filterByQuery(['city', 'county', 'address'])
            ->filterByName()
            ->filterByPostalCode()
            ->filterByPrimaryContact()
            ->filterByOpenJobs()
            ->filterByOnlyPermitted()
            ->getSearchResults();
    }

    public function update($where, array $data): Model
    {
        return DB::transaction(function () use ($where, $data) {
            $site = $this->repository->update($where, $data);

            $siteData = $this->prepareSiteData($data);

            $this->simproClient->patchSite($this->companyId, $site['simpro_site_id'], $siteData);

            if (Arr::has($data, 'primary_site_contact_id')) {
                $this->siteContactService->setPrimary($data['primary_site_contact_id']);
            }

            if (Arr::has($data, 'uprn')) {
                $uprnSiteCustomFieldId = config('defaults.site_uprn_custom_field_id');

                $this->simproClient->patchSiteCustomField($this->companyId, $site['simpro_site_id'], $uprnSiteCustomFieldId, [
                    'Value' => $data['uprn']
                ]);
            }

            return $site;
        });
    }

    public function attachSites(int $simproCustomerId, $group): void
    {
        $simproCustomer = $this->customerService->find($simproCustomerId);

        $sitePages = $this->simproClient->getAsGenerator(
            "companies/{$this->companyId}/sites/",
            [
                'Customers.ID' => $simproCustomer['customer_id'],
                'columns' => 'ID,Name,Address'
            ]
        );

        $isSiteEnabled = $this->groupSimproSiteService->isSiteEnabled($group['id']);

        foreach ($sitePages as $sitePage) {
            foreach ($sitePage as $site) {
                $simproSite = $this->createOrUpdate($site, $simproCustomerId);

                $this->siteCustomFieldService->createOrUpdateBySite($site, $simproSite['id']);

                $this->siteContactService->syncBySite($this->companyId, $site['ID'], $simproSite['id']);

                $this->getOrCreateGroupSimproSite($group['id'], $simproSite['id'], $isSiteEnabled);
            }
        }
    }

    public function firstOrCreateBySimpro(int $companyId, int $siteId, int $simproCustomerId): Model
    {
        $simproSite = $this->repository->findBy('simpro_site_id', $siteId);

        if (!$simproSite) {
            $site = $this->simproClient->getSite($companyId, $siteId);

            if (!$simproCustomerId) {
                $siteCustomer = Arr::first($site['Customers']);
                if ($siteCustomer) {
                    $simproCustomer = $this->customerService->firstOrCreateBySimpro($companyId, $siteCustomer);
                    $simproCustomerId = $simproCustomer['id'];
                }
            }

            $simproSite = $this->createOrUpdate($site, $simproCustomerId);

            $this->siteCustomFieldService->createOrUpdateBySite($site, $simproSite['id']);

            $this->siteContactService->syncBySite($companyId, $siteId, $simproSite['id']);

            if ($simproCustomerId) {
                $this->createGroupSimproSites($simproCustomerId, $simproSite['id']);
            }
        }

        return $simproSite;
    }

    public function createOrUpdateBySimpro(SimproJob $webhook): Model
    {
        $companyId = $webhook['data']['reference']['companyID'];
        $simproSiteId = $webhook['data']['reference']['siteID'];

        $simproSite = $this->simproClient->getSite($companyId, $simproSiteId);

        $site = $this->createOrUpdate($simproSite);

        $this->siteContactService->syncBySite($companyId, $simproSiteId, $site['id']);

        foreach ($simproSite['Customers'] as $siteCustomer) {
            $customer = $this->customerService->firstOrCreateBySimpro($companyId, $siteCustomer['ID']);

            $site->customers()->attach($customer['id']);
        }

        return $site;
    }

    public function deleteBySimpro(SimproJob $webhook): int
    {
        $simproSiteId = $webhook['data']['reference']['siteID'];

        return $this->repository->delete([
            'simpro_site_id' => $simproSiteId,
        ]);
    }

    protected function createOrUpdate(array $site): Model
    {
        $uprnCustomField = collect($site['CustomFields'])->first(function ($value) {
            return Arr::get($value, 'CustomField.ID') === config('defaults.site_uprn_custom_field_id');
        });

        return $this->repository->updateOrCreate([
            'simpro_site_id' => $site['ID']
        ], [
            'name' => $site['Name'],
            'address' => $site['Address']['Address'],
            'postal_code' => $site['Address']['PostalCode'],
            'city' => $site['Address']['City'],
            'country' => $site['Address']['Country'],
            'county' => $site['Address']['State'],
            'uprn' => Arr::get($uprnCustomField, 'Value')
        ]);
    }

    protected function prepareSiteData(array $data): array
    {
        $siteData = [];

        if (Arr::has($data, 'name')) {
            $siteData['Name'] = $data['name'];
        }
        if (Arr::has($data, 'address')) {
            $siteData['Address']['Address'] = $data['address'];
        }
        if (Arr::has($data, 'postal_code')) {
            $siteData['Address']['PostalCode'] = $data['postal_code'];
        }
        if (Arr::has($data, 'city')) {
            $siteData['Address']['City'] = $data['city'];
        }
        if (Arr::has($data, 'country')) {
            $siteData['Address']['Country'] = $data['country'];
        }
        if (Arr::has($data, 'county')) {
            $siteData['Address']['State'] = $data['county'];
        }

        return $siteData;
    }
}
