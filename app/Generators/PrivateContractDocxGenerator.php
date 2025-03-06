<?php

namespace App\Generators;

use App\Models\PrivateContract;
use App\Models\PrivateContractCostCenter;
use App\Models\PrivateContractCostCenterItem;
use Carbon\Carbon;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;

class PrivateContractDocxGenerator
{
    protected TemplateProcessor $processor;
    protected FilesystemAdapter $storage;
    protected FilesystemAdapter $storageDocs;

    public function __construct()
    {
        $this->storage = Storage::disk('templates');
        $this->storageDocs = Storage::disk('private_contracts_docs');
    }

    protected static function getFormattedString(?string $string): string
    {
        return htmlspecialchars((str_replace("\n", ', ', ucwords(strtolower($string)))));
    }

    protected static function getFormattedNumber($number): string
    {
        return number_format($number, 2);
    }

    protected static function getFilename(PrivateContract $contract): string
    {
        $name = Str::before($contract->payment_type, ' ');
        $date = Carbon::now()->toDateString();

        return "{$contract->customer_id}.{$name}.{$date}.{$contract->simpro_recurring_invoice_id}.docx";
    }

    public function generate(PrivateContract $privateContract): string
    {
        $templateType = $privateContract->payment_type === PrivateContract::PAYMENT_TYPE_ANNUAL_PAYMENT
            ? PrivateContract::TEMPLATE_TYPE_ANNUAL_PAYMENT
            : PrivateContract::TEMPLATE_TYPE_DIRECT_DEBIT;

        $template = new TemplateProcessor($this->storage->path(config("defaults.private_contract.templates.names.{$templateType}")));

        $isProject = $privateContract->recurring_type == PrivateContract::RECURRING_TYPE_PROJECT;

        $template = $this->prepareTemplate($template, $isProject);

        $this
            ->fillHeadCustomerInfo($template, $privateContract)
            ->fillDefaultValues($template, $privateContract)
            ->fillCostCenterTables(
                $template,
                $privateContract,
                $isProject,
            )->fillDebitInfo($template, $privateContract);

        return $this->saveContract($template, $privateContract);
    }

    protected function prepareTemplate(TemplateProcessor $template, bool $isProject): TemplateProcessor
    {
        if ($isProject) {
            $template->cloneBlock('SITE_BLOCK', 0);
            $template->cloneBlock('FINALS');
            $template->cloneBlock('PROJECT_TITLE_BLOCK');
        } else {
            $template->cloneBlock('PROJECT_TITLE_BLOCK', 0);
            $template->cloneBlock('FINALS', 0);
            $template->cloneBlock('SITE_BLOCK');
            $template->cloneBlock('PREBUILDS_BLOCK', 0);
        }

        return $template;
    }

    protected function fillHeadCustomerInfo(TemplateProcessor $template, PrivateContract $contract): self
    {
        $customer = $contract->loadMissing('customer')->customer;

        $address = self::getFormattedString($customer->address);
        $explodedAddress = explode(',', $address);

        $template->setValues([
            'ContactName' => self::getFormattedString($customer->name),
            'Address' => array_shift($explodedAddress) ?? '',
            'Address2' => trim(implode(', ', $explodedAddress)),
            'City' => self::getFormattedString($customer->city),
            'County' => self::getFormattedString($customer->state),
            'Postcode' => self::getFormattedString($customer->postal_code)
        ]);

        return $this;
    }

    protected function fillDefaultValues(TemplateProcessor $template, PrivateContract $contract): self
    {
        $template->setValue('TodayDate', Carbon::now()->format('jS F Y'));
        $template->setValue('CustomerID', $contract->customer_id);
        $template->setValue('NextRecurringDate', $contract->next_recurring_date);

        return $this;
    }

    protected function fillCostCenterTables(TemplateProcessor $template, PrivateContract $contract, bool $isProject): self
    {
        $costCenters = $contract->loadMissing('cost_centers.items')->cost_centers;

        $type = PrivateContractCostCenterItem::TYPE_DISCOUNT;
        $incTaxCoef = config("defaults.private_contract_cost_center_item.{$type}.inc_tax_coef");

        if ($costCenters->isEmpty()) {
            return $this;
        }

        $totalPrebuildsCount = $costCenters->count() + $costCenters->pluck('items')->flatten()->count();
        $template->cloneBlock('PREBUILDS_BLOCK', $totalPrebuildsCount, 1);

        foreach ($costCenters as $costCenter) {
            $this->fillPrebuildRaw($template, ['name' => self::getFormattedString($costCenter->name)]);

            foreach ($costCenter->items as $prebuild) {
                $isDiscount = $prebuild->type === PrivateContractCostCenterItem::TYPE_DISCOUNT;
                $tax = round($prebuild->ex_tax * $incTaxCoef, 2);

                $prebuildValues = [
                    'name' => self::getFormattedString($prebuild->name),
                    'qty' => $isDiscount ? null : self::getFormattedNumber($prebuild->qty),
                    'ex_tax' => self::getFormattedNumber($prebuild->ex_tax),
                    'inc_tax' => self::getFormattedNumber($tax + $prebuild->ex_tax),
                    'tax' => self::getFormattedNumber($tax),
                ];

                $this->fillPrebuildRaw($template, $prebuildValues);
            }

            $this
                ->fillSiteInfo($template, $contract)
                ->fillCostCenter($template, $costCenter);
        }

        $exTaxSum = $costCenters->sum('ex_tax');

        $this
            ->fillCostCenterFinal($template, $exTaxSum, $incTaxCoef)
            ->fillDdaAmount($template, $this->calcDdaAmount($contract, $exTaxSum, $incTaxCoef));

        return $this;
    }

    public function fillPrebuildRaw(TemplateProcessor $template, array $prebuildData): void
    {
        $replacements = [
            'PrebuildName' => Arr::get($prebuildData, 'name'),
            'PrebuildQty' => Arr::get($prebuildData, 'qty', ''),
            'PrebuildExTax' => Arr::get($prebuildData, 'ex_tax', ''),
            'PrebuildTax' => Arr::get($prebuildData, 'tax', ''),
            'PrebuildIncTax' => Arr::get($prebuildData, 'inc_tax', ''),
        ];

        foreach ($replacements as $key => $value) {
            $template->setValue($key, $value, 1);
        }
    }

    protected function fillSiteInfo(TemplateProcessor $template, PrivateContract $contract): self
    {
        $site = $contract->loadMissing('site')->site;

        $address = self::getFormattedString($site->address);
        $exploded = explode(',', $address);

        $variables = [
            'SiteAddress' => array_shift($exploded),
            'SiteAddress2' => trim(implode(', ', $exploded)),
            'SiteCity' => self::getFormattedString($site->city),
            'SiteCounty' => self::getFormattedString($site->country),
            'SitePostcode' => self::getFormattedString($site->postal_code),
        ];

        foreach ($variables as $key => $value) {
            $template->setValue($key, $value, 1);
        }

        return $this;
    }

    public function fillCostCenter(TemplateProcessor $template, PrivateContractCostCenter $costCenter): self
    {
        $variables = [
            'TotalExTax' => self::getFormattedNumber($costCenter->ex_tax),
            'TotalIncTax' => self::getFormattedNumber($costCenter->inc_tax),
            'TotalTax' => self::getFormattedNumber($costCenter->tax),
        ];

        foreach ($variables as $key => $value) {
            $template->setValue($key, $value, 1);
        }

        $template->setValue('CostCenterName', self::getFormattedString($costCenter->name), 1);

        return $this;
    }

    protected function fillCostCenterFinal(TemplateProcessor $template, float $exTaxSum, float $incTaxCoef): self
    {
        $tax = round($exTaxSum * $incTaxCoef, 2);

        $variables = [
            'FinalTotalExTax' => self::getFormattedNumber($exTaxSum),
            'FinalTotalTax' => self::getFormattedNumber($tax),
            'FinalTotalIncTax' =>  self::getFormattedNumber($exTaxSum + $tax),
        ];

        foreach ($variables as $key => $value) {
            $template->setValue($key, $value, 1);
        }

        return $this;
    }

    protected function fillDdaAmount(TemplateProcessor $template, $ddaAmount): self
    {
        $template->setValue('DDAmount', self::getFormattedNumber($ddaAmount));

        return $this;
    }

    protected function fillDebitInfo(TemplateProcessor $template, PrivateContract $contract): self
    {
        $variables = [
            'PayersName' => self::getFormattedString($contract->payer_account_name),
            'PayersReference' => self::getFormattedString($contract->payer_reference),
            'DDPaymentPeriod' => self::getFormattedString($contract->period),
            'DDPaymentDate' => $this->getDirectDate($contract),
        ];

        foreach ($variables as $key => $value) {
            $template->setValue($key, $value);
        }

        return $this;
    }

    protected function calcDdaAmount(PrivateContract $contract, float $exTaxSum, float $incTaxCoef): int
    {
        $period = (int) preg_replace('/\D/', '', $contract->period);

        return ($exTaxSum + round($exTaxSum * $incTaxCoef, 2)) / $period;
    }

    protected function getDirectDate(PrivateContract $contract): string
    {
        $nextDate = Carbon::parse($contract->next_recurring_date);
        $directDate = $contract->direct_date ?: config('defaults.private_contract.direct_date');

        if ($directMonth = $contract->direct_month) {
            $monthNumber = Carbon::parse($directMonth)->month;

            if ($monthNumber < $nextDate->month) {
                $nextDate->addYear();
            }

            return "{$contract->direct_date} {$contract->direct_month} {$nextDate->year}";
        }

        return "{$directDate} {$nextDate->addMonth()->format('F')} {$nextDate->year}";
    }

    protected function saveContract(TemplateProcessor $template, PrivateContract $contract): string
    {
        $filename = self::getFilename($contract);

        if ($contract->docx) {
            $this->storageDocs->delete($contract->docx);
        }

        $outputPath = $this->storageDocs->path($filename);

        $template->saveAs($outputPath);

        return $filename;
    }
}
