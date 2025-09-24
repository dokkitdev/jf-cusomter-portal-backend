<?php

namespace App\ApiClients;

use App\Models\Customer;
use App\Models\Team\SimProTeams;
use Generator;
use App\Services\HttpRequestService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SimproApiClient
{
    protected const MAX_PAGE_SIZE = 250;

    protected const JOB_STATUS_NAME_MADE_SAFE = 'Job : Made Safe';
    protected const JOB_STATUS_NAME_NO_ACCESS = 'Job : No Access';
    protected const JOB_STATUS_NAME_NO_GAS = 'Job: No Gas';

    protected HttpRequestService $httpRequestService;
    protected SimProTeams $team;

    public function __construct(
        SimProTeams $team
    )
    {
        $this->team = $team;
        $this->httpRequestService = app(HttpRequestService::class);
    }

    public function getCustomerInvoice(int $companyId, int $invoiceId): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/customerInvoices/{$invoiceId}");

        return $this->makeRequest('get', $url);
    }

    public function getWorkOrders(int $companyId, int $jobId, int $sectionId, int $costCenterId): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/jobs/{$jobId}/sections/{$sectionId}/costCenters/{$costCenterId}/workOrders/");

        return $this->makeRequest('get', $url, [
            'columns' => 'ID,Staff,DescriptionNotes,WorkOrderDate',
            'pageSize' => self::MAX_PAGE_SIZE,
        ]);
    }

    public function downloadQuoteAttachment(int $companyId, int $quoteId, string $attachmentId): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/quotes/{$quoteId}/attachments/files/{$attachmentId}");

        return $this->makeRequest('get', $url, [
            'display' => 'Base64'
        ]);
    }

    public function downloadAssetAttachment(int $companyId, int $siteId, int $assetId, string $attachmentId): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/assets/{$assetId}/attachments/files/{$attachmentId}");

        return $this->makeRequest('get', $url, [
            'display' => 'Base64'
        ]);
    }

    public function downloadJobAttachment(int $companyId, int $jobId, string $attachmentId): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/jobs/{$jobId}/attachments/files/{$attachmentId}");

        return $this->makeRequest('get', $url, [
            'display' => 'Base64'
        ]);
    }

    public function postJobAttachment(int $companyId, int $jobId, array $data): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/jobs/{$jobId}/attachments/files/");

        return $this->makeRequest('post', $url, $data);
    }

    public function postQuoteAttachment(int $companyId, int $quoteId, array $data): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/quotes/{$quoteId}/attachments/files/");

        return $this->makeRequest('post', $url, $data);
    }

    public function getQuote(int $companyId, int $quoteId): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/quotes/{$quoteId}");

        return $this->makeRequest('get', $url, [
            'display' => 'all'
        ]);
    }

    public function getAsset(int $companyId, int $assetId): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/customerAssets/{$assetId}");

        return $this->makeRequest('get', $url);
    }

    public function getAssets(int $companyId, $page = 1): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/customerAssets/");

        return $this->makeRequest('get', $url,
            [
                'pageSize' => self::MAX_PAGE_SIZE,
                'page' => $page,
            ]
        );
    }

    public function getArchivedAssets(int $companyId, int $page): array
    {
        return $this->makeRequest(
            'get',
            $this->getUrl("companies/{$companyId}/customerAssets/"),
            [
                'Archived' => 'true',
                'orderby' => 'ID',
                'pageSize' => self::MAX_PAGE_SIZE,
                'page' => $page,
            ]
        );
    }

    public function getArchivedAssetPagesCount(int $companyId): int
    {
        $response = $this->getArchivedAssets($companyId, 1);

        if (empty($response)) {
            return 0;
        }

        $pageWithData = 1;
        $pageWithoutData = $this->getArchivedAssetsPageWithoutData($companyId);

        while ($pageWithData < $pageWithoutData - 1) {
            $middlePage = (int) floor(($pageWithData + $pageWithoutData) / 2);

            $response = $this->getArchivedAssets($companyId, $middlePage);

            if (empty($response)) {
                $pageWithoutData = $middlePage;
            } else {
                $pageWithData = $middlePage;
            }
        }

        return $pageWithData;
    }

    public function getAssetServiceLevels(int $companyId, int $siteId, int $assetId): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/assets/{$assetId}/serviceLevels/");

        return $this->makeRequest('get', $url, [
            'pageSize' => self::MAX_PAGE_SIZE,
        ]);
    }

    public function getAssetTestHistories(int $companyId, int $siteId, int $assetId): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/assets/{$assetId}/testHistory/");

        return $this->makeRequest('get', $url, [
            'pageSize' => self::MAX_PAGE_SIZE,
        ]);
    }

    public function patchQuote(int $companyId, int $quoteId, array $data): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/quotes/{$quoteId}");

        return $this->makeRequest('patch', $url, $data);
    }

    public function getQuoteAttachments(int $companyId, int $quoteId): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/quotes/{$quoteId}/attachments/files/");

        return $this->makeRequest('get', $url, [
            'columns' => 'ID,Filename,DateAdded',
            'pageSize' => self::MAX_PAGE_SIZE,
        ]);
    }

    public function getQuoteNote(int $companyId, int $quoteId, int $noteId): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/quotes/{$quoteId}/notes/{$noteId}");

        return $this->makeRequest('get', $url);
    }

    public function postQuoteNote(int $companyId, int $quoteId, array $data): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/quotes/{$quoteId}/notes/");

        return $this->makeRequest('post', $url, $data);
    }

    public function getSchedule(int $companyId, int $scheduleId): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/schedules/{$scheduleId}");

        return $this->makeRequest('get', $url);
    }

    public function getSites(int $companyId, $page, $pageSize = 250): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/sites/");
        return $this->makeRequest('get', $url, ['pageSize' => $pageSize, 'page' => $page]);
    }

    public function getSite(int $companyId, int $siteId): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}");

        return $this->makeRequest('get', $url);
    }

    public function patchSite(int $companyId, int $siteId, array $data): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}");

        return $this->makeRequest('patch', $url, $data);
    }

    public function getSiteContacts(int $companyId, int $siteId): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/contacts/");

        return $this->makeRequest('get', $url, [
            'pageSize' => self::MAX_PAGE_SIZE,
            'columns' => 'ID,Title,GivenName,FamilyName,Email,WorkPhone,CellPhone,Position,PrimaryContact'
        ]);
    }

    public function postSiteContact(int $companyId, int $siteId, array $data): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/contacts/");

        return $this->makeRequest('post', $url, $data);
    }

    public function patchSiteContact(int $companyId, int $siteId, int $contactId, array $data): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/contacts/{$contactId}");

        return $this->makeRequest('patch', $url, $data);
    }

    public function deleteSiteContact(int $companyId, int $siteId, int $contactId): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/contacts/{$contactId}");

        return $this->makeRequest('delete', $url);
    }

    public function patchSiteCustomField(int $companyId, int $siteId, int $customFieldId, array $data): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/customFields/{$customFieldId}");

        return $this->makeRequest('patch', $url, $data);
    }

    public function getJob(int $companyId, int $jobId): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/jobs/{$jobId}");

        return $this->makeRequest('get', $url, [
            'display' => 'all'
        ]);
    }

    public function getJobs(int $companyId, $page = 1, $pageSize = 250): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/jobs/");

        return $this->makeRequest('get', $url, ['pageSize' => $pageSize, 'page' => $page]);
    }

    public function getMadeSafeJobLog(int $companyId, int $jobId): array
    {
        return $this->searchJobLogForUpdateStatusRecords($companyId, $jobId, [
            self::JOB_STATUS_NAME_MADE_SAFE,
        ]);
    }

    public function getNoAccessJobLog(int $companyId, int $jobId): array
    {
        return $this->searchJobLogForUpdateStatusRecords($companyId, $jobId, [
            self::JOB_STATUS_NAME_NO_ACCESS,
            self::JOB_STATUS_NAME_NO_GAS,
        ]);
    }

    public function getCreatedJobLog(int $companyId, int $jobId): ?array
    {
        return  $this->getJobLog($companyId, $jobId, 'Created Job%');
    }

    public function getCompletedJobLog(int $companyId, int $jobId): ?array
    {
        $response = $this->getJobLog($companyId, $jobId, '%Completed Pending%');

        usort($response, function ($rowA, $rowB) {
            if ($rowA['ID'] === $rowB['ID']) {
                return 0;
            }

            return ($rowA['ID'] > $rowB['ID']) ? 1 : -1;
        });

        return $response;
    }

    public function postJob(int $companyId, array $data): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/jobs/");

        return $this->makeRequest('post', $url, $data);
    }

    public function postQuote(int $companyId, array $data): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/quotes/");

        return $this->makeRequest('post', $url, $data);
    }

    public function getCustomer(int $companyId, string $type, int $customerId): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/customers/{$type}/{$customerId}");

        return $this->makeRequest('get', $url);
    }

    public function getAssetTypeSetup(int $companyId): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/setup/assetTypes/4/customFields/15");

        return $this->makeRequest('get', $url);
    }

    public function getAssetNames(int $companyId): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/setup/assetTypes/");

        return $this->makeRequest('get', $url);
    }

    public function getCustomers(int $companyId, string $type): Generator
    {
        $url = "companies/{$companyId}/customers/{$type}/";

        $additionalFilters = $type === Customer::TYPE_INDIVIDUALS
            ? ['columns' => 'ID,GivenName,FamilyName,Email,Address,Title']
            : ['columns' => 'ID,CompanyName,Email,Address'];

        return $this->getAsGenerator($url, $additionalFilters);
    }

    public function getRecurringInvoices(int $companyId, Carbon $fromDate, Carbon $toDate): Generator
    {
        $url = "companies/{$companyId}/recurringInvoices/";

        return $this->getAsGenerator($url, [
            'NextRecurringDate' => "between({$fromDate->format('Y-m-d')},{$toDate->format('Y-m-d')})",
            'columns' => 'ID,CustomFields,Customer,Site,NextRecurringDate,Type',
        ]);
    }

    public function getRecurringInvoiceCostCenters(int $companyId, int $recurringInvoiceId): Generator
    {
        $url = "companies/{$companyId}/recurringInvoiceCostCenters/";

        return $this->getAsGenerator($url, [
            'RecurringInvoice' => $recurringInvoiceId,
        ]);
    }

    public function getRecurringInvoiceCostCenter(
        int $companyId,
        int $recurringInvoiceId,
        int $sectionId,
        int $costCenterId
    ): array {
        $url = $this->getUrl("companies/{$companyId}/recurringInvoices/{$recurringInvoiceId}/sections/{$sectionId}/costCenters/{$costCenterId}");

        return $this->makeRequest('get', $url, [
            'display' => 'all',
        ]);
    }

    public function getJobAttachments(int $companyId, int $jobId): Generator
    {
        $url = "companies/{$companyId}/jobs/{$jobId}/attachments/files/";

        return $this->getAsGenerator($url, [
            'columns' => 'ID,Filename,Public,DateAdded',
            'Public' => 'true'
        ]);
    }

    public function getCostCenters(int $companyId = 0): Generator
    {
        $url = "companies/{$companyId}/setup/accounts/costCenters/";

        return $this->getAsGenerator($url);
    }

    public function getJobStatuses(int $companyId): Generator
    {
        $url = "companies/{$companyId}/setup/statusCodes/projects/";

        return $this->getAsGenerator($url);
    }

    public function getSchedules(int $companyId, int $jobId): Generator
    {
        $url = "companies/{$companyId}/schedules/";

        return $this->getAsGenerator($url, ['Reference' => "{$jobId}%"]);
    }

    public function getAssetAttachments(int $companyId, int $siteId, int $assetId): Generator
    {
        $url = "companies/{$companyId}/sites/{$siteId}/assets/{$assetId}/attachments/files/";

        return $this->getAsGenerator($url);
    }

    public function getAssetServiceLevelSetup(int $companyId): Generator
    {
        $url = "companies/{$companyId}/setup/assets/serviceLevels/";

        return $this->getAsGenerator($url);
    }

    public function getAsGenerator(string $url, array $additionalFilters = [], int $pageSize = self::MAX_PAGE_SIZE, array $headers = []): Generator
    {
        $page = 1;
        $url = $this->getUrl($url);

        do {
            $result = $this->makeRequest('get', $url, array_merge($additionalFilters, [
                'page' => $page,
                'pageSize' => $pageSize,
            ]), $headers);

            $page++;

            yield $result;
        } while (!empty($result));
    }

    public function searchJobLogForUpdateStatusRecords(int $companyId, int $jobId, array $newStatuses): array
    {
        $fullLog = $this->getJobLog($companyId, $jobId);

        if (empty($fullLog)) {
            return [];
        }

        $foundLogRecords = array_filter($fullLog, function (array $logEntry) use ($newStatuses) {
            foreach ($newStatuses as $newStatus) {
                if (
                    preg_match("/^Job status set to {$newStatus}$/i", $logEntry['Message'])
                    || preg_match("/^Updated Status from \".*\" to \"{$newStatus}\"$/i", $logEntry['Message'])
                ) {
                    return true;
                }
            }

            return false;
        });

        usort($foundLogRecords, function (array $recordA, array $recordB) {
            return Carbon::parse($recordA['DateLogged']) <=> Carbon::parse($recordB['DateLogged']);
        });

        return $foundLogRecords;
    }

    protected function getJobLog(int $companyId, int $jobId, ?string $message = null): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/logs/jobs/");

        $data = [
            'JobID' => $jobId,
            'pageSize' => self::MAX_PAGE_SIZE,
        ];

        if (isset($message)) {
            $data['Message'] = $message;
        }

        return $this->makeRequest('get', $url, $data);
    }

    public function getCompanies()
    {
        $url = $this->getUrl("companies/");

        Log::debug($url);

        return $this->makeRequest('get', $url);
    }

    protected function getArchivedAssetsPageWithoutData(int $companyId): int
    {
        $pageWithoutData = 0;

        do {
            $pageWithoutData += 1000;

            $response = $this->getArchivedAssets($companyId, $pageWithoutData);
        } while (!empty($response));

        return $pageWithoutData;
    }

    protected function makeRequest(string $method, string $url, array $data = [], array $headers = []): ?array
    {
        $headers = array_merge($this->getHeaders(), $headers);

        $requestData = ($method === 'delete') ? $headers : $data;

        $response = $this->httpRequestService
            ->set('timeout', config('artisan.timeout_seconds'))
            ->$method($url, $requestData, $headers);

        return $response->jsonOrNull();
    }

    protected function getUrl(string $action): string
    {
        return "https://". $this->team->build_url . ".simprosuite.com/api/v1.0/{$action}";
    }

    protected function getHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->team->token
        ];
    }
}
