<?php

namespace App\Console\Commands\Parser;

use App\Console\Commands\AbstractTimeoutCommand;
use App\Jobs\Parser\FullCompaniesParseJob;
use App\Jobs\Parser\FullSiteParseJob;
use App\Services\SimproJobService;
use App\Services\SimProTeam\SimProTeamService;

class SiteParserCommand extends AbstractTimeoutCommand
{
    protected $signature = 'parser:sites {build_url}';

    protected $description = 'Parser Companies';

    public function handle(): void
    {
        $team = app(SimProTeamService::class)->getSimProTeam($this->argument('build_url'));

        try {
            FullSiteParseJob::dispatchSync($team);
            $this->info('Sites parsed');
        }catch (\Exception $e){
            $this->error($e->getMessage());
        }
    }
}
