<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Repositories\JobNoAccessDateRepository;
use Illuminate\Support\Carbon;
use RonasIT\Support\Services\EntityService;

/**
 * @property JobNoAccessDateRepository $repository
 * @mixin JobNoAccessDateRepository
 */
class JobNoAccessDateService extends EntityService
{
    protected SimproApiClient $simproClient;
    protected int $companyId;

    public function __construct()
    {
        $this->setRepository(JobNoAccessDateRepository::class);

        $this->simproClient = app(SimproApiClient::class);
        $this->companyId = config('services.simpro.company_id');
    }

    public function syncBySimpro(int $companyId, int $simproJobId, int $jobId): void
    {
        $simproJobNoAccessDates = $this->simproClient->getNoAccessJobLog($companyId, $simproJobId);

        $jobNoAccessDates = $this->repository->get(['job_id' => $jobId]);

        $previousDate = null;

        foreach ($simproJobNoAccessDates as $simproJobNoAccessDate) {
            if (!$previousDate || (Carbon::parse($simproJobNoAccessDate['DateLogged'])->diffInHours($previousDate) > 4)) {
                $simproJobNoAccessDateId = $simproJobNoAccessDate['ID'];
                $data = [
                    'job_id' => $jobId,
                    'simpro_job_log_id' => $simproJobNoAccessDateId,
                    'date' => $simproJobNoAccessDate['DateLogged'],
                ];
                $jobNoAccessDate = $jobNoAccessDates->firstWhere('simpro_job_log_id', $simproJobNoAccessDateId);
                if ($jobNoAccessDate) {
                    $this->repository->update($jobNoAccessDate['id'], $data);
                    $jobNoAccessDates = $jobNoAccessDates->where('id', '!=', $jobNoAccessDate['id']);
                } else {
                    $this->repository->create($data);
                }

                $previousDate = $simproJobNoAccessDate['DateLogged'];
            }
        }

        if ($jobNoAccessDates->isNotEmpty()) {
            $ids = $jobNoAccessDates->pluck('id')->toArray();
            $this->repository->deleteByList($ids);
        }
    }
}
