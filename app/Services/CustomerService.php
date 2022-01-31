<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\Role;
use App\Models\Customer;
use App\Models\SimproJob;
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

    public function __construct()
    {
        parent::__construct();

        $this->setRepository(CustomerRepository::class);

        $this->simproClient = app(SimproApiClient::class);

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
                    'type' => Customer::TYPE_COMPANIES
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
                    'type' => Customer::TYPE_INDIVIDUALS
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
                if ($customer['name'] !== $customerFromSimpro['name']) {
                    $this->repository->update($customer['id'], [
                        'name' => $customerFromSimpro['name']
                    ]);
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

        if (!$customer) {
            try {
                $type = Customer::TYPE_COMPANIES;
                $customer = $this->simproClient->getCustomer($companyId, $type, $simproCustomerId);
            } catch (Exception $e) {
                $type = Customer::TYPE_INDIVIDUALS;
                $customer = $this->simproClient->getCustomer($companyId, $type, $simproCustomerId);
            }

            $customer = $this->repository->create([
                'simpro_customer_id' => $simproCustomerId,
                'type' => $type,
                'name' => $this->getName($customer, $type)
            ]);
        }

        return $customer;
    }

    public function createOrUpdateBySimpro(SimproJob $webhook, string $type): void
    {
        $companyId = $webhook['data']['reference']['companyID'];
        $customerId = $this->getCustomerId($webhook);

        $customer = $this->simproClient->getCustomer($companyId, $type, $customerId);

        $this->repository->updateOrCreate([
            'simpro_customer_id' => $customerId,
            'type' => $type
        ], [
            'name' => $this->getName($customer, $type)
        ]);
    }

    public function deleteBySimpro(SimproJob $webhook, string $type): void
    {
        $customerId = $this->getCustomerId($webhook);

        $this->repository->delete([
            'simpro_customer_id' => $customerId,
            'type' => $type
        ]);
    }

    protected function getName(array $customer, string $type): string
    {
        if ($type === Customer::TYPE_INDIVIDUALS) {
            return "{$customer['GivenName']} {$customer['FamilyName']}";
        }

        return $customer['CompanyName'];
    }

    protected function getCustomerId(SimproJob $webhook): string
    {
        preg_match('/(\d+)/', $webhook['data']['description'], $matches);

        return $matches[0];
    }
}
