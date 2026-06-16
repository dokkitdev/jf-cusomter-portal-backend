<?php

namespace App\ApiClients;

use Generator;
use App\Services\HttpRequestService;
use Illuminate\Support\Facades\Cache;

class SimproApiClient
{
    protected const MAX_PAGE_SIZE = 250;

    protected HttpRequestService $httpRequestService;

    public function __construct()
    {
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

    public function getJobs(int $companyId, array $data): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/jobs/");

        return $this->makeRequest('get', $url, $data);
    }

    public function getJobLog(int $companyId, int $jobId, string $message): ?array
    {
        $url = $this->getUrl("companies/{$companyId}/logs/jobs/");

        $data = [
            'JobID' => $jobId,
            'Message' => $message
        ];

        return $this->makeRequest('get', $url, $data);
    }

    public function getMadeSafeJobLog(int $companyId, int $jobId): ?array
    {
        return  $this->getJobLog($companyId, $jobId, 'Job status set to Job : Made Safe');
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

    public function getNoAccessJobLog(int $companyId, int $jobId): ?array
    {
        return $this->getJobLog(
            $companyId,
            $jobId,
            'in(Job status set to Job : No Access,Job status set to Job: No Gas)',
        );
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

        return $this->getAsGenerator($url);
    }

    public function getJobAttachments(int $companyId, int $jobId): Generator
    {
        $url = "companies/{$companyId}/jobs/{$jobId}/attachments/files/";

        return $this->getAsGenerator($url, [
            'columns' => 'ID,Filename,Public,DateAdded',
            'Public' => 'true'
        ]);
    }

    public function getCostCenters(int $companyId): Generator
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

    protected function getArchivedAssetsPageWithoutData(int $companyId): int
    {
        $pageWithoutData = 0;

        do {
            $pageWithoutData += 1000;

            $response = $this->getArchivedAssets($companyId, $pageWithoutData);
        } while (!empty($response));

        return $pageWithoutData;
    }

    protected function checkRateLimit(): void
    {
        $maxRequests = 7;
        $currentSecond = time();

        $cacheKey = "rate_limit:request_count:" . $currentSecond;

        $currentRequests = Cache::remember($cacheKey, 5, function () {
                return 0;
            }) + 1;

        Cache::put($cacheKey, $currentRequests, 5);

        if ($currentRequests > $maxRequests) {
            sleep(1);
            $this->checkRateLimit();
        }
    }

    protected function makeRequest(string $method, string $url, array $data = [], array $headers = []): ?array
    {
        $maxRetries = 10;
        $attempt = 0;

        do {
            $this->checkRateLimit();

            $headers = array_merge($this->getHeaders(), $headers);
            $requestData = ($method === 'delete') ? $headers : $data;

            $this->httpRequestService
                ->set('timeout', config('artisan.timeout_seconds'))
                ->$method($url, $requestData, $headers);

            $rawResponse = $this->httpRequestService->getResponse();

            if ($rawResponse && $rawResponse->getStatusCode() === 429) {
                $attempt++;

                if ($attempt < $maxRetries) {
                    sleep(1);
                    continue;
                }
            }
            break;

        } while ($attempt < $maxRetries);

        return $this->httpRequestService->jsonOrNull();
    }

    protected function getUrl(string $action): string
    {
        return config('services.simpro.api_url') . "api/v1.0/{$action}";
    }

    protected function getHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . config('services.simpro.token'),
        ];
    }
}

