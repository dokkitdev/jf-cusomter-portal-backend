<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\Customer;
use App\Models\PrivateContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use RonasIT\Support\Services\EntityService;
use App\Repositories\PrivateContractRepository;

/**
 * @mixin PrivateContractRepository
 * @property PrivateContractRepository $repository
 */
class PrivateContractService extends EntityService
{
    private SimproApiClient $simproClient;
    private CustomerService $customerService;
    private SiteService $siteService;
    private int $companyId;

    public function __construct()
    {
        $this->setRepository(PrivateContractRepository::class);

        $this->simproClient = app(SimproApiClient::class);
        $this->customerService = app(CustomerService::class);
        $this->siteService = app(SiteService::class);
        $this->companyId = config('services.simpro.company_id');
    }

    public function sync(Carbon $fromDate, Carbon $toDate): void
    {
        $recurringInvoicesPages = $this->simproClient->getRecurringInvoices($this->companyId, $fromDate, $toDate);

        foreach ($recurringInvoicesPages as $page) {
            foreach ($page as $invoice) {
                $customer = $this->customerService->firstOrCreateBySimpro($this->companyId, Arr::get($invoice, 'Customer.ID'));
                $site = $this->siteService->firstOrCreateBySimpro($this->companyId, Arr::get($invoice, 'Site.ID'));

                $data = [
                    'simpro_recurring_invoice_id' => $invoice['ID'],
                    'customer_id' => $customer->id,
                    'site_id' => $site->id,
                    'recurring_type' => $invoice['Type'],
                    'company_name' => Arr::get($invoice, 'Customer.CompanyName'),
                    'is_company' => $customer->type === Customer::TYPE_COMPANIES,
                    'next_recurring_date' => $invoice['NextRecurringDate'],
                ];

                if (!$this->isNeedSaveContract(
                    $invoice,
                    $dataByCustomFields = $this->getDataBySimproInvoiceCustomFields($invoice['CustomFields'])
                )) {
                    continue;
                }

                $this->deleteNotProcessedFromData($customer->id, $invoice['ID'], Carbon::now()->startOfYear());

                if (!$this->existsForYear($customer->id, $invoice['ID'], Carbon::now()->year)) {
                    $this->create(array_merge($data, $dataByCustomFields));
                }
            }
        }
    }

    protected function getDataBySimproInvoiceCustomFields(array $simproInvoiceCustomFields): array
    {
        $data = [];

        foreach ($simproInvoiceCustomFields as $customField) {
            $customFieldId = Arr::get($customField, 'CustomField.ID');
            $customFieldValue = Arr::get($customField, 'Value');

            switch ($customFieldId) {
                case PrivateContract::CF_PAYMENT_TYPE_ID:
                    $data['payment_type'] = $this->isValidPaymentType($customFieldValue)
                        ? $customFieldValue
                        : null;
                    break;
                case PrivateContract::CF_PERIOD_ID:
                    $data['period'] = $customFieldValue;
                    break;
                case PrivateContract::CF_PAYER_REFERENCE_ID:
                    $data['payer_reference'] = $customFieldValue;
                    break;
                case PrivateContract::CF_DIRECT_DATE_ID:
                    $data['direct_date'] = $customFieldValue;
                    break;
                case PrivateContract::CF_PAYER_ACCOUNT_NAME_ID:
                    $data['payer_account_name'] = $customFieldValue;
                    break;
                case PrivateContract::CF_DIRECT_MONTH_ID:
                    $data['direct_month'] = $customFieldValue;
                    break;
            }
        }

        return $data;
    }

    protected function isValidPaymentType(?string $value): bool
    {
        return !empty($value)
            && in_array($value, PrivateContract::PAYMENT_TYPES, true);
    }

    protected function isNeedSaveContract(array $invoice, array $dataByCustomFields): bool
    {
        return !empty($invoice['CustomFields'])
            && !empty($invoice['NextRecurringDate'])
            && !empty($dataByCustomFields['payment_type']);
    }
}
