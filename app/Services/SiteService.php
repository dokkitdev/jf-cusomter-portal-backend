<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\Role;
use App\Models\SimproJob;
use App\Models\Team\SimProCompanies;
use App\Models\Team\SimProTeams;
use App\Repositories\SiteRepository;
use App\Services\SimProTeam\SimProTeamService;
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
    protected ?SimProTeams $team = null;

    public function __construct(
        SimProTeams $team = null
    )
    {
        $this->team = $team;
        parent::__construct();

        $this->setRepository(SiteRepository::class);

        if($this->team){
            $this->simproClient = new SimproApiClient($this->team);
        }else{
            $this->simproClient = app(SimproApiClient::class);
        }

        $this->companyId = config('services.simpro.company_id');
        $this->customerService = app(CustomerService::class);
        $this->siteContactService = app(SiteContactService::class);
    }

    public function search(array $filters, SimProTeams $team = null): LengthAwarePaginator
    {
        if (Arr::get($filters, 'order_by') === 'postal_code') {
            $filters['order_by'] = DB::raw('LOWER(postal_code)');
        }

        return $this->repository
            ->with(Arr::get($filters, 'with', []))
            ->withCount(Arr::get($filters, 'with_count', []))
            ->searchQuery($filters)
            ->filterByIntQuery('simpro_site_id')
            ->filterByList('customers.customer_id', 'customer_ids')
            ->filterByQuery(['city', 'county', 'address'])
            ->filterByName()
            ->filterByUprn()
            ->filterByPostalCode()
            ->filterByPrimaryContact()
            ->filterByOpenJobs()
            ->filterByOnlyPermitted()
            ->filterByTeam($team)
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
                    'Value' => $data['uprn'],
                ]);
            }

            return $site;
        });
    }

    public function firstOrCreateBySimpro(int $companyId, int $simproSiteId): Model
    {
        $site = $this->repository->findBy('simpro_site_id', $simproSiteId);

        if (!$site) {
            $site = $this->createOrUpdate($companyId, $simproSiteId);
        }

        return $site;
    }

    public function createOrUpdateBySimpro(SimproJob $webhook): Model
    {
        $companyId = $webhook['data']['reference']['companyID'];
        $simproSiteId = $webhook['data']['reference']['siteID'];
        $build_url = $webhook['data']['build'];

        $this->team = app(SimProTeamService::class)->getSimProTeam($build_url);

        $this->simproClient = new SimproApiClient($this->team);


        return $this->createOrUpdate($companyId, $simproSiteId);
    }

    public function deleteBySimpro(SimproJob $webhook): int
    {
        $simproSiteId = $webhook['data']['reference']['siteID'];

        return $this->repository->delete([
            'simpro_site_id' => $simproSiteId,
        ]);
    }

    protected function createOrUpdate(int $companyId, int $simproSiteId): Model
    {
        $simproSite = $this->simproClient->getSite($companyId, $simproSiteId);

        $uprnCustomField = collect($simproSite['CustomFields'])->first(function ($value) {
            return Arr::get($value, 'CustomField.ID') === config('defaults.site_uprn_custom_field_id');
        });

        $company = app(SimProTeamService::class)
            ->fetchCompanyBySimProId($this->team->id, $companyId);

        $site = $this->repository->updateOrCreate([
            'simpro_site_id' => $simproSite['ID'],
            'team_id' => $this->team->id,
            'company_id' => $company->id,
        ], [
            'name' => $simproSite['Name'],
            'address' => $simproSite['Address']['Address'],
            'postal_code' => $simproSite['Address']['PostalCode'],
            'city' => $simproSite['Address']['City'],
            'country' => $simproSite['Address']['Country'],
            'county' => $simproSite['Address']['State'],
            'uprn' => Arr::get($uprnCustomField, 'Value'),
        ]);

        $siteContactService = new SiteContactService($this->team);
        $siteContactService->syncBySite($companyId, $simproSiteId, $site['id']);

        $customerIds = [];

        $customerService = new CustomerService($this->team);
        foreach ($simproSite['Customers'] as $siteCustomer) {
            $customer = $customerService->firstOrCreateBySimpro($companyId, $siteCustomer['ID']);
            $customerIds[] = $customer['id'];
        }
        $site->customers()->sync($customerIds);

        $site = $this->repository->update($site['id'], ['customer_id' => Arr::get($customerIds, '0')]);

        return $site;
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
