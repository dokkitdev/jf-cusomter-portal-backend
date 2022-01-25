<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Repositories\SiteContactRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * @property SiteContactRepository $repository
 * @mixin SiteContactRepository
 */
class SiteContactService extends BaseService
{
    protected SimproApiClient $simproClient;
    protected int $companyId;

    public function __construct()
    {
        parent::__construct();

        $this->setRepository(SiteContactRepository::class);

        $this->simproClient = app(SimproApiClient::class);
        $this->companyId = config('services.simpro.company_id');
    }

    public function create(array $data): Model
    {
        $contactData = $this->prepareContactData($data);

        $contact = $this->simproClient->postSiteContact($this->companyId, $data['site_id'], $contactData);

        $data['simpro_contact_id'] = $contact['ID'];
        $data['name'] = trim("{$data['given_name']} {$data['family_name']}");

        return $this->repository->create($data);
    }

    public function update($where, array $data): Model
    {
        $siteContact = $this->repository->with(['site'])->first($where);

        $simproSiteId = Arr::get($siteContact, 'site.simpro_site_id');

        $contactData = $this->prepareContactData($data);

        $this->simproClient->patchSiteContact($this->companyId, $simproSiteId, $siteContact['simpro_contact_id'], $contactData);

        $givenName = Arr::get($data, 'given_name', $siteContact['given_name']);

        $familyName = Arr::has($data, 'family_name') ? $data['family_name'] : $siteContact['family_name'];

        $data['name'] = trim("{$givenName} {$familyName}");

        return $this->repository->update($where, $data);
    }

    public function delete($where): int
    {
        $siteContact = $this->repository->with(['site'])->first($where);

        $simproSiteId = Arr::get($siteContact, 'site.simpro_site_id');

        $this->simproClient->deleteSiteContact($this->companyId, $simproSiteId, $siteContact['simpro_contact_id']);

        return $this->repository->delete($where);
    }

    public function setPrimary(int $siteContactId): void
    {
        DB::transaction(function () use ($siteContactId) {
            $siteContact = $this->repository->with(['site'])->find($siteContactId);

            $this->repository->updateMany(['site_id' => Arr::get($siteContact, 'site.id')], ['is_primary' => false]);

            $siteContact = $this->repository->update($siteContactId, ['is_primary' => true]);

            $simproSiteId = Arr::get($siteContact, 'site.simpro_site_id');

            $this->simproClient->patchSiteContact($this->companyId, $simproSiteId, $siteContact['simpro_contact_id'], [
                'PrimaryContact' => true
            ]);
        });
    }

    public function syncBySite(int $companyId, int $simproSiteId, int $siteId): void
    {
        $contacts = $this->simproClient->getSiteContacts($companyId, $simproSiteId);

        $siteContacts = $this->repository->get(['site_id' => $siteId]);

        foreach ($contacts as $contact) {
            $data = [
                'site_id' => $siteId,
                'simpro_contact_id' => $contact['ID'],
                'title' => $contact['Title'],
                'name' => trim("{$contact['GivenName']} {$contact['FamilyName']}"),
                'given_name' => $contact['GivenName'],
                'family_name' => $contact['FamilyName'],
                'email' => $contact['Email'],
                'work_phone' => $contact['WorkPhone'],
                'cell_phone' => $contact['CellPhone'],
                'position' => $contact['Position'],
                'is_primary' => ($contact['PrimaryContact'] === true)
            ];
            $siteContact = $siteContacts->firstWhere('simpro_contact_id', $contact['ID']);
            if ($siteContact) {
                $this->repository->update($siteContact['id'], $data);
                $siteContacts = $siteContacts->where('id', '!=', $siteContact['id']);
            } else {
                $this->repository->create($data);
            }
        }

        if ($siteContacts->isNotEmpty()) {
            $ids = $siteContacts->pluck('id')->toArray();
            $this->repository->deleteByList($ids);
        }
    }

    protected function prepareContactData(array $data): array
    {
        $siteData = [];

        $siteData['GivenName'] = $data['given_name'];

        if (Arr::has($data, 'family_name')) {
            $siteData['FamilyName'] = $data['family_name'];
        }
        if (Arr::has($data, 'title')) {
            $siteData['Title'] = $data['title'];
        }
        if (Arr::has($data, 'email')) {
            $siteData['Email'] = $data['email'];
        }
        if (Arr::has($data, 'work_phone')) {
            $siteData['WorkPhone'] = $data['work_phone'];
        }
        if (Arr::has($data, 'cell_phone')) {
            $siteData['CellPhone'] = $data['cell_phone'];
        }
        if (Arr::has($data, 'position')) {
            $siteData['Position'] = $data['position'];
        }

        return $siteData;
    }
}
