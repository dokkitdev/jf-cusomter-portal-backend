<?php

namespace App\Console\Commands\Parser;

use App\Console\Commands\AbstractTimeoutCommand;
use App\Jobs\Parser\AssetsParserJob;
use App\Services\SimProTeam\SimProTeamService;

class AssetsParserCommand extends AbstractTimeoutCommand
{
    protected $signature = 'parser:assets {build_url}';

    protected $description = 'Parser Assets';

    public function handle(): void
    {
        $team = app(SimProTeamService::class)->getSimProTeam($this->argument('build_url'));

        try {
            AssetsParserJob::dispatchSync($team);
            $this->info('Assets parsed');
        }catch (\Exception $e){
            $this->error($e->getMessage());
        }
    }
}
