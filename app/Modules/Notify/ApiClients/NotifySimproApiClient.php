<?php

namespace App\Modules\Notify\ApiClients;

use App\Modules\Notify\Exceptions\ApiClientException;
use App\Services\HttpRequestService;
use DateTimeImmutable;
use Generator;
use Symfony\Component\HttpFoundation\Response;

class NotifySimproApiClient
{
    public const JOB_STAGE_PENDING = 'Pending';
    public const JOB_STAGE_PROGRESS = 'Progress';

    public const JOB_TYPE_PROJECT = 'Project';
    public const JOB_TYPE_SERVICE = 'Service';

    protected const MAX_PAGE_SIZE = 250;

    protected HttpRequestService $httpRequestService;

    public function __construct()
    {
        $this->httpRequestService = app(HttpRequestService::class);
    }

    public function getSchedulesByDate(DateTimeImmutable $date): Generator
    {
        $page = 1;

        do {
            $schedules = $this->apiCall('get', '/schedules/', [
                'Type' => 'job',
                'Date' => $date->format('Y-m-d'),
                'pageSize' => self::MAX_PAGE_SIZE,
                'page' => $page,
            ]);
            
            foreach ($schedules as $schedule) {
                yield $schedule;
            }

            $page++;
        } while (!empty($schedules));
    }

    public function getJob(int $jobId): array
    {
        return $this->apiCall('get', "/jobs/{$jobId}", [
            'display' => 'all',
        ]);
    }

    public function getCostCenterStock(int $jobId, int $sectionId, int $costCenterId): array
    {
        return $this->apiCall('get', "/jobs/{$jobId}/sections/{$sectionId}/costCenters/{$costCenterId}/stock/", [
            'pageSize' => self::MAX_PAGE_SIZE,
        ]);
    }

    public function getCatalog(int $catalogId): array
    {
        return $this->apiCall('get', "/catalogs/{$catalogId}");
    }

    protected function apiCall(string $method, string $endpoint, array $data = [], array $headers = []): array
    {
        $baseUrl = config('notify.simpro.base_url');
        $companyId = config('notify.simpro.company_id');
        $url = "{$baseUrl}/api/v1.0/companies/{$companyId}{$endpoint}";

        $headers = array_merge($this->getHeaders(), $headers);

        $response = $this->httpRequestService
            ->set('http_errors', false)
            ->{$method}($url, $data, $headers)
            ->getResponse();

        if ($response->getStatusCode() >= Response::HTTP_BAD_REQUEST) {
            throw new ApiClientException($url, $response->getStatusCode(), $response->getBody()->getContents());
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    protected function getHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . config('notify.simpro.token'),
        ];
    }
}