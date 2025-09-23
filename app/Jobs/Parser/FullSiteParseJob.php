<?php

namespace App\Jobs\Parser;

use App\ApiClients\SimproApiClient;
use App\Jobs\AbstractJob;
use App\Models\Team\SimProTeams;
use App\Services\SimProTeam\SimProTeamService;
use App\Services\SimproWebhookService;

class FullSiteParseJob extends AbstractJob
{
    protected SimproApiClient $apiClient;
    protected SimProTeams $team;

    public function __construct(
        SimProTeams $team
    )
    {
        $this->apiClient = new SimproApiClient($team);
        $this->team = $team;

    }
    public function handle()
    {
        $companies = app(SimProTeamService::class)
            ->getCompanies($this->team->id);

        foreach ($companies as $company) {
            $this->fetchSites($company->sim_pro_company_id);
        }
    }

    public function fetchSites($sim_pro_company_id, $page = 1)
    {
        $sites = $this->apiClient->getSites($sim_pro_company_id, $page);

        foreach ($sites as $site) {
            $webhook = [
                'name' => 'Site',
                'ID' => 'site.updated',
                'build' => $this->team->build_url,
                'reference' => [
                    'siteID' => $site['ID'],
                    'companyID' => $sim_pro_company_id,
                ]
            ];

            app(SimproWebhookService::class)->process($webhook);
        }

        if(count($sites) >= 249){
            $this->fetchSites($sim_pro_company_id, $page + 1);
        }
    }
}
