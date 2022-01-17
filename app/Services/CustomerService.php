<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\Role;
use App\Models\Customer;
use App\Repositories\CustomerRepository;
use Exception;
use Illuminate\Support\Arr;

/**
 * @property CustomerRepository $repository
 * @mixin CustomerRepository
 */
class CustomerService extends BaseService
{
    protected SimproApiClient $simproClient;
    protected $companyId;

    public function __construct()
    {
        parent::__construct();

        $this->setRepository(CustomerRepository::class);

        $this->simproClient = app(SimproApiClient::class);

        $this->companyId = config('services.simpro.company_id');
    }

    public function search($filters)
    {
        $authUser = $this->getAuthUser();

        if ($authUser['role_id'] === Role::CUSTOMER) {
            $filters['customer_has_user'] = $authUser['id'];
        }

        return $this->repository
            ->searchQuery($filters)
            ->filterByNameOrId()
            ->filterByUserGroups()
            ->with(Arr::get($filters, 'with', []))
            ->getSearchResults();
    }

    public function syncCustomers()
    {
        $typeCompanies = Customer::TYPE_COMPANIES;
        $companiesPages = $this->simproClient->getAsGenerator("companies/{$this->companyId}/customers/{$typeCompanies}/");
        $typeIndividuals = Customer::TYPE_INDIVIDUALS;
        $individualPages = $this->simproClient->getAsGenerator("companies/{$this->companyId}/customers/{$typeIndividuals}/");

        $companiesMapped = [];
        foreach ($companiesPages as $companyPage) {
            $companies = array_map(function ($company) {
                return [
                    'customer_id' => $company['ID'],
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
                    'customer_id' => $individual['ID'],
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
                return ($customer['customer_id'] === $customerFromSimpro['customer_id']) && ($customer['type'] === $customerFromSimpro['type']);
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

    public function getOrCreateBySimpro($companyId, $customer)
    {
        $customerId = $customer['ID'];

        $customer = $this->repository->first(['customer_id' => $customerId]);

        if (!$customer) {
            try {
                $type = Customer::TYPE_COMPANIES;
                $customer = $this->simproClient->getCustomer($companyId, $type, $customerId);
            } catch (Exception $e) {
                $type = Customer::TYPE_INDIVIDUALS;
                $customer = $this->simproClient->getCustomer($companyId, $type, $customerId);
            }

            $customer = $this->repository->create([
                'customer_id' => $customerId,
                'type' => $type,
                'name' => $this->getName($customer, $type)
            ]);
        }

        return $customer;
    }

    public function createOrUpdateBySimpro($webhook, $type)
    {
        $companyId = $webhook['data']['reference']['companyID'];
        $customerId = $this->getCustomerId($webhook);

        $customer = $this->simproClient->getCustomer($companyId, $type, $customerId);

        $this->repository->updateOrCreate([
            'customer_id' => $customerId,
            'type' => $type
        ], [
            'name' => $this->getName($customer, $type)
        ]);
    }

    public function deleteBySimpro($webhook, $type)
    {
        $customerId = $this->getCustomerId($webhook);

        $this->repository->delete([
            'customer_id' => $customerId,
            'type' => $type
        ]);
    }

    protected function getName($customer, $type)
    {
        if ($type === Customer::TYPE_INDIVIDUALS) {
            return "{$customer['GivenName']} {$customer['FamilyName']}";
        }

        return $customer['CompanyName'];
    }

    protected function getCustomerId($webhook)
    {
        preg_match('/(\d+)/', $webhook['data']['description'], $matches);

        return $matches[0];
    }
}
