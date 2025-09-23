<?php

namespace App\Console\Commands\Parser;

use App\Console\Commands\AbstractTimeoutCommand;
use App\Jobs\Parser\FullJobsParseJob;
use App\Services\SimProTeam\SimProTeamService;

class JobParserCommand extends AbstractTimeoutCommand
{
    protected $signature = 'parser:jobs {build_url}';

    protected $description = 'Parser Jobs';

    public function handle(): void
    {
        $team = app(SimProTeamService::class)->getSimProTeam($this->argument('build_url'));

        try {
            FullJobsParseJob::dispatchSync($team);
            $this->info('Jobs parsed');
        }catch (\Exception $e){
            $this->error($e->getMessage());
        }
    }
}
