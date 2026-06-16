<?php

namespace App\Console\Commands;

use App\ApiClients\SimproApiClient;
use App\Services\JobNoAccessDateService;
use App\Services\JobService;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Tmp extends Command
{
    protected $signature = 'tmp {instance} {from} {to}';

    public function handle(): void
    {
        $instance = (int) $this->argument('instance');
        $fromId = (int) $this->argument('from');
        $toId = (int) $this->argument('to');

        $fileName = "update_made_safe_dates/{$instance}.json";

        $processedIds = Storage::exists($fileName) ? json_decode(Storage::get($fileName), true) : [];

        $simproClient = app(SimproApiClient::class);
        $jobService = app(JobService::class);
        $noAccessDateService = app(JobNoAccessDateService::class);

        $count = DB::table('jobs')
            ->where('id', '>=', $fromId)
            ->where('id', '<', $toId)
            ->count();

        $progress = $this->output->createProgressBar($count);
        $progress->start();

        $jobs = DB::table('jobs')
            ->select(['id', 'simpro_job_id'])
            ->where('id', '>=', $fromId)
            ->where('id', '<', $toId)
            ->orderBy('id', 'desc')
            ->cursor();

        foreach ($jobs as $job) {
            $job = (array) $job;

            if (in_array($job['id'], $processedIds)) {
                continue;
            }

            $madeSafeJobLog = $simproClient->getMadeSafeJobLog(0, $job['simpro_job_id']);

            $jobService->update(['id' => $job['id']], [
                'made_safe_date' => Arr::get(Arr::first($madeSafeJobLog), 'DateLogged'),
            ]);

            $noAccessDateService->syncBySimpro(0, $job['simpro_job_id'], $job['id']);

            $processedIds[] = $job['id'];

            Storage::put($fileName, json_encode($processedIds));

            $progress->advance();
        }

        $this->output->newLine();
        $this->output->writeln('Done');
    }
}

