<?php

namespace App\Jobs;

use App\Models\SimproJob;
use App\Services\SimproJobService;

class SimProHandleJob extends AbstractJob
{
    public $tries = 1;

    protected $simProJobService;
    public $data;

    public function __construct(
        SimproJob $data
    )
    {
        $this->data = $data;
        $this->simProJobService = app(SimproJobService::class);
    }

    public function handle()
    {
        $this->simProJobService->handleJob($this->data);

        $this->simProJobService->update($this->data->id, [
            'handle_status' => SimproJob::HANDLE_STATUS_COMPLETED,
        ]);
    }
}
