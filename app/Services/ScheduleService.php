<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\SimproJob;
use App\Repositories\ScheduleRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use RonasIT\Support\Services\EntityService;

/**
 * @property ScheduleRepository $repository
 * @mixin ScheduleRepository
 */
class ScheduleService extends EntityService
{
    protected JobService $jobService;
    protected SimproApiClient $simproClient;

    public function __construct()
    {
        $this->setRepository(ScheduleRepository::class);

        $this->jobService = app(JobService::class);
        $this->simproClient = app(SimproApiClient::class);
    }

    public function createOrUpdateManyBySimpro(int $companyId, int $simproJobId, int $jobId): void
    {
        $schedulePages = $this->simproClient->getSchedules($companyId, $simproJobId);

        foreach ($schedulePages as $schedulePage) {
            if ($schedulePage) {
                foreach ($schedulePage as $simproSchedule) {
                    $this->createOrUpdate($simproSchedule, $jobId);
                }
            }
        }

        $this->setRecentScheduleToJob($jobId);

        $this->setNextScheduleToJob($jobId);
    }

    public function updateOrCreateBySimpro(SimproJob $webhook): ?Model
    {
        $companyId = $webhook['data']['reference']['companyID'];
        $simproJobId = $webhook['data']['reference']['jobID'];
        $scheduleId = $webhook['data']['reference']['scheduleID'];

        $job = $this->jobService->firstOrCreateBySimpro($companyId, $simproJobId);

        $simproSchedule = $this->simproClient->getSchedule($companyId, $scheduleId);

        $schedule = null;

        if ($simproSchedule) {
            $schedule = $this->createOrUpdate($simproSchedule, $job['id']);
        }

        $this->setRecentScheduleToJob($job['id']);

        $this->setNextScheduleToJob($job['id']);

        return $schedule;
    }

    public function deleteBySimpro(SimproJob $webhook): int
    {
        $simproJobId = $webhook['data']['reference']['jobID'];
        $scheduleId = $webhook['data']['reference']['scheduleID'];

        $job = $this->jobService->findBy('simpro_job_id', $simproJobId);

        if (!$job) {
            return true;
        }

        $result = $this->repository->delete([
            'job_id' => $job['id'],
            'simpro_schedule_id' => $scheduleId
        ]);

        $this->setRecentScheduleToJob($job['id']);

        $this->setNextScheduleToJob($job['id']);

        return $result;
    }

    protected function createOrUpdate(array $schedule, int $jobId): Model
    {
        $block = $this->findBlock(Arr::get($schedule, 'Blocks', []));

        return $this->repository->updateOrCreate([
            'job_id' => $jobId,
            'simpro_schedule_id' => $schedule['ID']
        ], [
            'name' => Arr::get($schedule, 'Staff.Name'),
            'date' => $this->prepareDate($schedule, $block),
            'start_time' => Arr::get($block, 'ISO8601StartTime'),
            'end_time' => Arr::get($block, 'ISO8601EndTime')
        ]);
    }

    protected function setRecentScheduleToJob(int $jobId): void
    {
        $recentSchedule = $this->repository->getRecentSchedule($jobId);

        $this->jobService->update($jobId, [
            'recent_schedule_id' => Arr::get($recentSchedule, 'id')
        ]);
    }

    protected function setNextScheduleToJob(int $jobId): void
    {
        $nextSchedule = $this->repository->getNextSchedule($jobId);

        $this->jobService->update($jobId, [
            'next_schedule_id' => Arr::get($nextSchedule, 'id')
        ]);
    }

    protected function prepareDate(array $schedule, array $block): string
    {
        $date = Arr::get($schedule, 'Date');
        $startTime = Arr::get($block, 'StartTime');

        if ($startTime) {
            $date = "{$date} {$startTime}:00";
        }

        return $date;
    }

    protected function findBlock(array $blocks): array
    {
        return collect($blocks)->sortBy('StartTime')->first(null, []);
    }
}
