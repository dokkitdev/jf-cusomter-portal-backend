<?php

namespace App\ApiClients;

use Generator;
use RonasIT\Support\Services\HttpRequestService;

class SimproApiClient
{
    protected HttpRequestService $httpRequestService;

    public function __construct()
    {
        $this->httpRequestService = app(HttpRequestService::class);
    }

    public function getCustomerInvoice(int $companyId, int $invoiceId): array
    {
        $url = $this->getUrl("companies/{$companyId}/customerInvoices/{$invoiceId}");

        return $this->makeRequest('get', $url);
    }

    public function getWorkOrders(int $companyId, int $jobId, int $sectionId, int $costCenterId): array
    {
        $url = $this->getUrl("companies/{$companyId}/jobs/{$jobId}/sections/{$sectionId}/costCenters/{$costCenterId}/workOrders/");

        return $this->makeRequest('get', $url, [
            'columns' => 'ID,Staff,DescriptionNotes,WorkOrderDate',
            'pageSize' => 250
        ]);
    }

    public function downloadQuoteAttachment(int $companyId, int $quoteId, string $attachmentId): array
    {
        $url = $this->getUrl("companies/{$companyId}/quotes/{$quoteId}/attachments/files/{$attachmentId}");

        return $this->makeRequest('get', $url, [
            'display' => 'Base64'
        ]);
    }

    public function downloadAssetAttachment(int $companyId, int $siteId, int $assetId, string $attachmentId): array
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/assets/{$assetId}/attachments/files/{$attachmentId}");

        return $this->makeRequest('get', $url, [
            'display' => 'Base64'
        ]);
    }

    public function downloadJobAttachment(int $companyId, int $jobId, string $attachmentId): array
    {
        $url = $this->getUrl("companies/{$companyId}/jobs/{$jobId}/attachments/files/{$attachmentId}");

        return $this->makeRequest('get', $url, [
            'display' => 'Base64'
        ]);
    }

    public function postJobAttachment(int $companyId, int $jobId, array $data): array
    {
        $url = $this->getUrl("companies/{$companyId}/jobs/{$jobId}/attachments/files/");

        return $this->makeRequest('post', $url, $data);
    }

    public function postQuoteAttachment(int $companyId, int $quoteId, array $data): array
    {
        $url = $this->getUrl("companies/{$companyId}/quotes/{$quoteId}/attachments/files/");

        return $this->makeRequest('post', $url, $data);
    }

    public function getQuote(int $companyId, int $quoteId): array
    {
        $url = $this->getUrl("companies/{$companyId}/quotes/{$quoteId}");

        return $this->makeRequest('get', $url, [
            'display' => 'all'
        ]);
    }

    public function getAsset(int $companyId, int $assetId): array
    {
        $url = $this->getUrl("companies/{$companyId}/customerAssets/{$assetId}");

        return $this->makeRequest('get', $url);
    }

    public function getAssetServiceLevels(int $companyId, int $siteId, int $assetId): array
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/assets/{$assetId}/serviceLevels/");

        return $this->makeRequest('get', $url, [
            'pageSize' => 250
        ]);
    }

    public function getAssetTestHistories(int $companyId, int $siteId, int $assetId): array
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/assets/{$assetId}/testHistory/");

        return $this->makeRequest('get', $url, [
            'pageSize' => 250
        ]);
    }

    public function patchQuote(int $companyId, int $quoteId, array $data): array
    {
        $url = $this->getUrl("companies/{$companyId}/quotes/{$quoteId}");

        return $this->makeRequest('patch', $url, $data);
    }

    public function getQuoteAttachments(int $companyId, int $quoteId): array
    {
        $url = $this->getUrl("companies/{$companyId}/quotes/{$quoteId}/attachments/files/");

        return $this->makeRequest('get', $url, [
            'columns' => 'ID,Filename,DateAdded',
            'pageSize' => 250
        ]);
    }

    public function getQuoteNote(int $companyId, int $quoteId, int $noteId): array
    {
        $url = $this->getUrl("companies/{$companyId}/quotes/{$quoteId}/notes/{$noteId}");

        return $this->makeRequest('get', $url);
    }

    public function postQuoteNote(int $companyId, int $quoteId, array $data): array
    {
        $url = $this->getUrl("companies/{$companyId}/quotes/{$quoteId}/notes/");

        return $this->makeRequest('post', $url, $data);
    }

    public function getSchedule(int $companyId, int $scheduleId): array
    {
        $url = $this->getUrl("companies/{$companyId}/schedules/{$scheduleId}");

        return $this->makeRequest('get', $url);
    }

    public function getSite(int $companyId, int $siteId): array
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}");

        return $this->makeRequest('get', $url);
    }

    public function patchSite(int $companyId, int $siteId, array $data): array
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}");

        return $this->makeRequest('patch', $url, $data);
    }

    public function getSiteContacts(int $companyId, int $siteId): array
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/contacts/");

        return $this->makeRequest('get', $url, [
            'pageSize' => 250,
            'columns' => 'ID,Title,GivenName,FamilyName,Email,WorkPhone,CellPhone,Position,PrimaryContact'
        ]);
    }

    public function postSiteContact(int $companyId, int $siteId, array $data): array
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/contacts/");

        return $this->makeRequest('post', $url, $data);
    }

    public function patchSiteContact(int $companyId, int $siteId, int $contactId, array $data): array
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/contacts/{$contactId}");

        return $this->makeRequest('patch', $url, $data);
    }

    public function deleteSiteContact(int $companyId, int $siteId, int $contactId): array
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/contacts/{$contactId}");

        return $this->makeRequest('delete', $url);
    }

    public function patchSiteCustomField(int $companyId, int $siteId, int $customFieldId, array $data): array
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/customFields/{$customFieldId}");

        return $this->makeRequest('patch', $url, $data);
    }

    public function getJob(int $companyId, int $jobId): array
    {
        $url = $this->getUrl("companies/{$companyId}/jobs/{$jobId}");

        return $this->makeRequest('get', $url, [
            'display' => 'all'
        ]);
    }

    public function postJob(int $companyId, array $data): array
    {
        $url = $this->getUrl("companies/{$companyId}/jobs/");

        return $this->makeRequest('post', $url, $data);
    }

    public function postQuote(int $companyId, array $data): array
    {
        $url = $this->getUrl("companies/{$companyId}/quotes/");

        return $this->makeRequest('post', $url, $data);
    }

    public function getCustomer(int $companyId, string $type, int $customerId): array
    {
        $url = $this->getUrl("companies/{$companyId}/customers/{$type}/{$customerId}");

        return $this->makeRequest('get', $url);
    }

    public function getAsGenerator(string $url, array $additionalFilters = [], int $pageSize = 250, array $headers = []): Generator
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

    protected function makeRequest(string $method, string $url, ?array $data = null, array $headers = []): array
    {
        $headers = array_merge($this->getHeaders(), $headers);

        $requestData = ($method === 'delete') ? $headers : $data;
        $method = "send{$method}";

        $response = $this->httpRequestService->$method($url, $requestData, $headers);

        return $this->httpRequestService->parseJsonResponse($response);
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