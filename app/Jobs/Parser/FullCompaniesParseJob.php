<?php

namespace App\Jobs\Parser;

use App\ApiClients\SimproApiClient;
use App\Jobs\AbstractJob;
use App\Models\Team\SimProTeams;
use App\Services\SimProTeam\SimProTeamService;
use Illuminate\Support\Facades\Log;

class FullCompaniesParseJob extends AbstractJob
{
    protected SimproApiClient $apiClient;
    protected SimProTeams $team;

    public function __construct(
        SimProTeams $team
    )
    {
        $this->team = $team;
        $this->apiClient = new SimproApiClient($team);

    }
    public function handle()
    {
        $this->fetchCompanies();
    }

    public function fetchCompanies($page = 1)
    {
        $companies = $this->apiClient->getCompanies();

        foreach ($companies as $company) {
            app(SimProTeamService::class)
                ->updateCompany(
                    $this->team,
                    $company
                );
        }
    }
}
