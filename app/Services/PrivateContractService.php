<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Generators\PdfGenerator;
use App\Generators\PrivateContractDocxGenerator;
use App\Models\Customer;
use App\Models\PrivateContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use RonasIT\Support\Services\EntityService;
use App\Repositories\PrivateContractRepository;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @mixin PrivateContractRepository
 * @property PrivateContractRepository $repository
 */
class PrivateContractService extends EntityService
{
    protected SimproApiClient $simproClient;
    protected CustomerService $customerService;
    protected PrivateContractCostCenterService $privateContractCostCenterService;
    protected SiteService $siteService;
    protected int $companyId;
    protected FilesystemAdapter $docsStorage;
    protected FilesystemAdapter $templatesStorage;

    public function __construct()
    {
        $this->setRepository(PrivateContractRepository::class);

        $this->simproClient = app(SimproApiClient::class);
        $this->customerService = app(CustomerService::class);
        $this->privateContractCostCenterService = app(PrivateContractCostCenterService::class);
        $this->siteService = app(SiteService::class);
        $this->companyId = config('services.simpro.company_id');
        $this->docsStorage = Storage::disk('private_contracts_docs');
        $this->templatesStorage = Storage::disk('templates');
    }

    public function downloadDocFile(string $filename): StreamedResponse
    {
        return $this->docsStorage->download($filename);
    }

    public function sync(Carbon $fromDate, Carbon $toDate): void
    {
        $recurringInvoicesPages = $this->simproClient->getRecurringInvoices($this->companyId, $fromDate, $toDate);

        foreach ($recurringInvoicesPages as $page) {
            foreach ($page as $invoice) {
                $customer = $this->customerService->firstOrCreateBySimpro($this->companyId, Arr::get($invoice, 'Customer.ID'));
                $site = $this->siteService->firstOrCreateBySimpro($this->companyId, Arr::get($invoice, 'Site.ID'));

                $data = array_merge([
                    'simpro_recurring_invoice_id' => $invoice['ID'],
                    'customer_id' => $customer->id,
                    'site_id' => $site->id,
                    'recurring_type' => $invoice['Type'],
                    'company_name' => Arr::get($invoice, 'Customer.CompanyName'),
                    'is_company' => $customer->type === Customer::TYPE_COMPANIES,
                    'next_recurring_date' => $invoice['NextRecurringDate'],
                ], $this->getDataBySimproInvoiceCustomFields($invoice['CustomFields']));

                if (!$this->isNeedSaveContract($data)) {
                    continue;
                }

                $this->deleteNotProcessedFromDate($customer->id, $invoice['ID'], Carbon::now()->startOfYear());

                if (!$this->existsForYear($customer->id, $invoice['ID'], Carbon::now()->year)) {
                    $privateContract = $this->create($data);

                    $this->privateContractCostCenterService->createByPrivateContract($privateContract);
                }
            }
        }
    }

    public function generateLetters(array $privateContractIds): void
    {
        $docxGenerator = new PrivateContractDocxGenerator();
        $pdfGenerator = new PdfGenerator();

        $this->repository
            ->getByList($privateContractIds)
            ->each(function ($contract) use ($docxGenerator, $pdfGenerator) {
                $filenameDocx = $docxGenerator->generate($contract);
                $filenamePdf = $pdfGenerator->docxToPdf($filenameDocx);

                $this->repository->update($contract->id, [
                    'docx' => $filenameDocx,
                    'pdf' => $filenamePdf,
                ]);
            });
    }

    public function downloadTemplate(string $type): StreamedResponse
    {
        $filename = config("defaults.private_contract.templates.names.{$type}");

        return $this->templatesStorage->download($filename);
    }

    public function uploadTemplate(string $type, string $content): void
    {
        $this->templatesStorage->put(
            config("defaults.private_contract.templates.names.{$type}"),
            $content,
        );
    }

    public function search(array $filters): LengthAwarePaginator
    {
        return $this->repository
            ->searchQuery($filters)
            ->getSearchResults();
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

    protected function isNeedSaveContract(array $privateContractData): bool
    {
        return !empty($privateContractData['next_recurring_date'])
            && !empty($privateContractData['payment_type']);
    }
}
