<?php

namespace App\ApiClients;

use Illuminate\Support\Facades\Storage;
use RonasIT\Support\Services\HttpRequestService;

class SimproApiClient
{
    protected HttpRequestService $httpRequestService;

    public function __construct()
    {
        $this->httpRequestService = app(HttpRequestService::class);
    }

    public function getCustomerInvoice($companyId, $invoiceId)
    {
        $url = $this->getUrl("companies/{$companyId}/customerInvoices/{$invoiceId}");

        return $this->makeRequest('get', $url);
    }

    public function getWorkOrders($companyId, $jobId, $sectionId, $costCenterId)
    {
        $url = $this->getUrl("companies/{$companyId}/jobs/{$jobId}/sections/{$sectionId}/costCenters/{$costCenterId}/workOrders/");

        return $this->makeRequest('get', $url, [
            'columns' => 'ID,Staff,DescriptionNotes,WorkOrderDate',
            'pageSize' => 250
        ]);
    }

    public function downloadQuoteAttachment($companyId, $quoteId, $attachmentId)
    {
        $url = $this->getUrl("companies/{$companyId}/quotes/{$quoteId}/attachments/files/{$attachmentId}");

        return $this->makeRequest('get', $url, [
            'display' => 'Base64'
        ]);
    }

    public function downloadAssetAttachment($companyId, $siteId, $assetId, $attachmentId)
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/assets/{$assetId}/attachments/files/{$attachmentId}");

        return $this->makeRequest('get', $url, [
            'display' => 'Base64'
        ]);
    }

    public function downloadJobAttachment($companyId, $jobId, $attachmentId)
    {
        $url = $this->getUrl("companies/{$companyId}/jobs/{$jobId}/attachments/files/{$attachmentId}/view/");

        return $this->downloadAttachment($url, $attachmentId);
    }

    public function postJobAttachment($companyId, $jobId, $data)
    {
        $url = $this->getUrl("companies/{$companyId}/jobs/{$jobId}/attachments/files/");

        return $this->makeRequest('post', $url, $data);
    }

    public function postQuoteAttachment($companyId, $quoteId, $data)
    {
        $url = $this->getUrl("companies/{$companyId}/quotes/{$quoteId}/attachments/files/");

        return $this->makeRequest('post', $url, $data);
    }

    public function getQuote($companyId, $quoteId)
    {
        $url = $this->getUrl("companies/{$companyId}/quotes/{$quoteId}");

        return $this->makeRequest('get', $url, [
            'display' => 'all'
        ]);
    }

    public function getAsset($companyId, $assetId)
    {
        $url = $this->getUrl("companies/{$companyId}/customerAssets/{$assetId}");

        return $this->makeRequest('get', $url);
    }

    public function getAssetServiceLevels($companyId, $siteId, $assetId)
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/assets/{$assetId}/serviceLevels/");

        return $this->makeRequest('get', $url, [
            'pageSize' => 250
        ]);
    }

    public function getAssetTestHistories($companyId, $siteId, $assetId)
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/assets/{$assetId}/testHistory/");

        return $this->makeRequest('get', $url, [
            'pageSize' => 250
        ]);
    }

    public function patchQuote($companyId, $quoteId, $data)
    {
        $url = $this->getUrl("companies/{$companyId}/quotes/{$quoteId}");

        return $this->makeRequest('patch', $url, $data);
    }

    public function getQuoteAttachments($companyId, $quoteId)
    {
        $url = $this->getUrl("companies/{$companyId}/quotes/{$quoteId}/attachments/files/");

        return $this->makeRequest('get', $url, [
            'columns' => 'ID,Filename,DateAdded',
            'pageSize' => 250
        ]);
    }

    public function getQuoteNote($companyId, $quoteId, $noteId)
    {
        $url = $this->getUrl("companies/{$companyId}/quotes/{$quoteId}/notes/{$noteId}");

        return $this->makeRequest('get', $url);
    }

    public function postQuoteNote($companyId, $quoteId, $data)
    {
        $url = $this->getUrl("companies/{$companyId}/quotes/{$quoteId}/notes/");

        return $this->makeRequest('post', $url, $data);
    }

    public function getSchedule($companyId, $scheduleId)
    {
        $url = $this->getUrl("companies/{$companyId}/schedules/{$scheduleId}");

        return $this->makeRequest('get', $url);
    }

    public function getSite($companyId, $siteId)
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}");

        return $this->makeRequest('get', $url);
    }

    public function patchSite($companyId, $siteId, $data)
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}");

        return $this->makeRequest('patch', $url, $data);
    }

    public function getSiteContacts($companyId, $siteId)
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/contacts/");

        return $this->makeRequest('get', $url, [
            'pageSize' => 250,
            'columns' => 'ID,Title,GivenName,FamilyName,Email,WorkPhone,CellPhone,Position,PrimaryContact'
        ]);
    }

    public function postSiteContact($companyId, $siteId, $data)
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/contacts/");

        return $this->makeRequest('post', $url, $data);
    }

    public function patchSiteContact($companyId, $siteId, $contactId, $data)
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/contacts/{$contactId}");

        return $this->makeRequest('patch', $url, $data);
    }

    public function deleteSiteContact($companyId, $siteId, $contactId)
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/contacts/{$contactId}");

        return $this->makeRequest('delete', $url);
    }

    public function patchSiteCustomField($companyId, $siteId, $customFieldId, $data)
    {
        $url = $this->getUrl("companies/{$companyId}/sites/{$siteId}/customFields/{$customFieldId}");

        return $this->makeRequest('patch', $url, $data);
    }

    public function getJob($companyId, $jobId)
    {
        $url = $this->getUrl("companies/{$companyId}/jobs/{$jobId}");

        return $this->makeRequest('get', $url, [
            'display' => 'all'
        ]);
    }

    public function postJob($companyId, $data)
    {
        $url = $this->getUrl("companies/{$companyId}/jobs/");

        return $this->makeRequest('post', $url, $data);
    }

    public function postQuote($companyId, $data)
    {
        $url = $this->getUrl("companies/{$companyId}/quotes/");

        return $this->makeRequest('post', $url, $data);
    }

    public function getCustomer($companyId, $type, $customerId)
    {
        $url = $this->getUrl("companies/{$companyId}/customers/{$type}/{$customerId}");

        return $this->makeRequest('get', $url);
    }

    public function getAsGenerator($url, $additionalFilters = [], $callback = null, $pageSize = 250, $headers = [])
    {
        $page = 1;
        $url = $this->getUrl($url);

        do {
            $result = $this->makeRequest('get', $url, array_merge($additionalFilters, [
                'page' => $page,
                'pageSize' => $pageSize,
            ]), array_merge($this->getHeaders(), $headers));

            $page++;

            if (!empty($callback)) {
                $result = $callback($result);
            }

            yield $result;
        } while (!empty($result));
    }

    protected function makeRequest($method, $url, $data = null, $headers = null)
    {
        $headers = empty($headers) ? $this->getHeaders() : $headers;

        $requestData = ($method === 'delete') ? $headers : $data;
        $method = "send{$method}";

        $response = $this->httpRequestService->$method($url, $requestData, $headers);

        return $this->httpRequestService->parseJsonResponse($response);
    }

    protected function downloadAttachment($url, $attachmentId)
    {
        $this->httpRequestService->set('sink', Storage::path($attachmentId));
        $response = $this->httpRequestService->sendGet($url, null, $this->getHeaders());

        return $this->httpRequestService->parseJsonResponse($response);
    }

    protected function getUrl($action)
    {
        return config('services.simpro.api_url') . "api/v1.0/{$action}";
    }

    protected function getHeaders()
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . config('services.simpro.token'),
        ];
    }
}