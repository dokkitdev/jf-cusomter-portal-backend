<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\Role;
use App\Models\Customer;
use App\Models\SimproJob;
use App\Models\Team\SimProTeams;
use App\Repositories\CustomerRepository;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * @property CustomerRepository $repository
 * @mixin CustomerRepository
 */
class CustomerService extends BaseService
{
    protected SimproApiClient $simproClient;
    protected int $companyId;
    protected ?SimProTeams $team = null;

    public function __construct(
        SimProTeams $team = null
    )
    {
        $this->team = $team;
        parent::__construct();

        $this->setRepository(CustomerRepository::class);

        if($team){
            $this->simproClient = new SimproApiClient($team);
        }else{
            $this->simproClient = app(SimproApiClient::class);
        }

        $this->companyId = config('services.simpro.company_id');
    }

    public function search(array $filters): LengthAwarePaginator
    {
        $authUser = $this->getAuthUser();

        if ($authUser['role_id'] === Role::CUSTOMER) {
            $filters['customer_has_user'] = $authUser['id'];
        }

        return $this->repository
            ->with(Arr::get($filters, 'with', []))
            ->searchQuery($filters)
            ->filterBy('users.user_id', 'customer_has_user')
            ->filterByNameOrId()
            ->getSearchResults();
    }

    public function syncCustomers(): void
    {
        $companiesPages = $this->simproClient->getCustomers($this->companyId, Customer::TYPE_COMPANIES);
        $individualPages = $this->simproClient->getCustomers($this->companyId, Customer::TYPE_INDIVIDUALS);

        $companiesMapped = [];
        foreach ($companiesPages as $companyPage) {
            $companies = array_map(function ($company) {
                return [
                    'simpro_customer_id' => $company['ID'],
                    'name' => $this->getName($company, Customer::TYPE_COMPANIES),
                    'type' => Customer::TYPE_COMPANIES,
                    'email' => Arr::get($company, 'Email'),
                    'address' => Arr::get($company, 'Address.Address'),
                    'city' => Arr::get($company, 'Address.City'),
                    'state' => Arr::get($company, 'Address.State'),
                    'postal_code' => Arr::get($company, 'Address.PostalCode'),
                ];
            }, $companyPage);

            $companiesMapped = array_merge($companiesMapped, $companies);
        }

        $individualsMapped = [];
        foreach ($individualPages as $individualPage) {
            $individuals = array_map(function ($individual) {
                return [
                    'simpro_customer_id' => $individual['ID'],
                    'name' => $this->getName($individual, Customer::TYPE_INDIVIDUALS),
                    'type' => Customer::TYPE_INDIVIDUALS,
                    'email' => Arr::get($individual, 'Email'),
                    'title' => Arr::get($individual, 'Title'),
                    'address' => Arr::get($individual, 'Address.Address'),
                    'city' => Arr::get($individual, 'Address.City'),
                    'state' => Arr::get($individual, 'Address.State'),
                    'postal_code' => Arr::get($individual, 'Address.PostalCode'),
                ];
            }, $individualPage);

            $individualsMapped = array_merge($individualsMapped, $individuals);
        }

        $customersFromSimpro = array_merge($companiesMapped, $individualsMapped);

        $customers = $this->repository->get();

        foreach ($customersFromSimpro as $customerFromSimpro) {
            $customer = $customers->first(function ($customer) use ($customerFromSimpro) {
                return ($customer['simpro_customer_id'] === $customerFromSimpro['simpro_customer_id']) && ($customer['type'] === $customerFromSimpro['type']);
            });

            if ($customer) {
                if (!empty($diffs = array_diff_assoc($customerFromSimpro, $customer->toArray()))) {
                    $this->repository->update($customer['id'], $diffs);
                }

                $customers = $customers->where('id', '!=', $customer['id']);
            } else {
                $this->repository->create($customerFromSimpro);
            }
        }

        if ($customers->isNotEmpty()) {
            $ids = $customers->pluck('id')->toArray();
            $this->repository->deleteByList($ids);
        }
    }

    public function firstOrCreateBySimpro(int $companyId, int $simproCustomerId): Model
    {
        $customer = $this->repository->first(['simpro_customer_id' => $simproCustomerId]);

        return $customer ?? $this->createBySimpro($companyId, $simproCustomerId);
    }

    protected function createBySimpro(int $companyId, int $simproCustomerId): Model
    {
        try {
            $type = Customer::TYPE_COMPANIES;
            $customer = $this->simproClient->getCustomer($companyId, $type, $simproCustomerId);
        } catch (Exception $e) {
            $type = Customer::TYPE_INDIVIDUALS;
            $customer = $this->simproClient->getCustomer($companyId, $type, $simproCustomerId);
        }

        return $this->repository->create([
            'simpro_customer_id' => $simproCustomerId,
            'type' => $type,
            'name' => $this->getName($customer, $type),
            'email' => Arr::get($customer, 'Email'),
            'title' => Arr::get($customer, 'Title'),
            'address' => Arr::get($customer, 'Address.Address'),
            'city' => Arr::get($customer, 'Address.City'),
            'state' => Arr::get($customer, 'Address.State'),
            'postal_code' => Arr::get($customer, 'Address.PostalCode'),
        ]);
    }

    public function createOrUpdateBySimpro(SimproJob $webhook, string $type): void
    {
        $companyId = $webhook['data']['reference']['companyID'];
        $customerId = $this->getCustomerId(Arr::get($webhook, 'data.description'));

        $customer = $this->simproClient->getCustomer($companyId, $type, $customerId);

        $this->repository->updateOrCreate([
            'simpro_customer_id' => $customerId,
            'type' => $type,
        ], [
            'name' => $this->getName($customer, $type),
            'email' => Arr::get($customer, 'Email'),
            'title' => Arr::get($customer, 'Title'),
            'address' => Arr::get($customer, 'Address.Address'),
            'city' => Arr::get($customer, 'Address.City'),
            'state' => Arr::get($customer, 'Address.State'),
            'postal_code' => Arr::get($customer, 'Address.PostalCode'),
        ]);
    }

    public function deleteBySimpro(SimproJob $webhook, string $type): void
    {
        $customerId = $this->getCustomerId(Arr::get($webhook, 'data.description'));

        $this->repository->delete([
            'simpro_customer_id' => $customerId,
            'type' => $type,
        ]);
    }

    public function getCustomerId(string $description): string
    {
        preg_match('/(\d+)/', $description, $matches);

        return $matches[0];
    }

    protected function getName(array $customer, string $type): string
    {
        if ($type === Customer::TYPE_INDIVIDUALS) {
            return "{$customer['GivenName']} {$customer['FamilyName']}";
        }

        return $customer['CompanyName'];
    }
}
