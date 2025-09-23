<?php

namespace App\Console\Commands\Parser;

use App\Console\Commands\AbstractTimeoutCommand;
use App\Jobs\Parser\FullCompaniesParseJob;
use App\Services\SimproJobService;
use App\Services\SimProTeam\SimProTeamService;

class CompaniesParserCommand extends AbstractTimeoutCommand
{
    protected $signature = 'parser:companies {build_url}';

    protected $description = 'Parser Companies';

    public function handle(): void
    {
        $team = app(SimProTeamService::class)->getSimProTeam($this->argument('build_url'));

        try {
            FullCompaniesParseJob::dispatchSync($team);
            $this->info('Companies parsed');
        }catch (\Exception $e){
            $this->error($e->getMessage());
        }
    }
}
