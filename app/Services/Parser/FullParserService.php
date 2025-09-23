<?php

namespace App\Services\Parser;

use App\Jobs\Parser\FullCompaniesParseJob;
use App\Jobs\Parser\FullJobsParseJob;
use App\Jobs\Parser\FullSiteParseJob;
use App\Repositories\AssetRepository;
use App\Services\BaseService;
use App\Services\SimProTeam\SimProTeamService;

/**
 * @property AssetRepository $repository
 * @mixin AssetRepository
 */
class FullParserService extends BaseService
{
    protected SimProTeamService $simProTeams;

    public function __construct(

    )
    {
        parent::__construct();
    }

    public function fullParse($team)
    {
        FullCompaniesParseJob::dispatchSync($team);
        FullSiteParseJob::dispatch($team);
        FullJobsParseJob::dispatch($team);
    }
}
