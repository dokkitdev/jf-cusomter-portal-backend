<?php

namespace App\Console\Commands\Simpro;

use App\Models\SimproJob;
use App\Services\SimproJobService;
use Illuminate\Console\Command;
use Exception;

class HandleSimproJobs extends Command
{
    protected $signature = 'simpro:handle-jobs {eventId?} {divider?} {mod?}';

    protected $description = 'Handle Simpro jobs';

    protected SimproJobService $simproJobService;

    public function handle(): void
    {
        $eventId = $this->argument('eventId');
        $divider = $this->argument('divider');
        $mod = $this->argument('mod');

        $this->simproJobService = app(SimproJobService::class);

        $this->simproJobService
            ->getForHandle(1000, $eventId, $divider, $mod)
            ->each(function ($job) {
                try {
                    $this->simproJobService->handleJob($job);

                    //$this->simproJobService->delete($job->id);

                    $this->simproJobService->update($job->id, [
                        'handle_status' => SimproJob::HANDLE_STATUS_COMPLETED,
                    ]);
                } catch (Exception $e) {
                    report($e);
                    $this->simproJobService->update($job->id, [
                        'handle_status' => SimproJob::HANDLE_STATUS_ERROR,
                        'handle_result' => [
                            'code' => $e->getCode(),
                            'message' => $e->getMessage()
                        ]
                    ]);
                }
            });
    }
}
