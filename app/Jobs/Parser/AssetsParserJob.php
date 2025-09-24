<?php

namespace App\Jobs\Parser;

use App\ApiClients\SimproApiClient;
use App\Jobs\AbstractJob;
use App\Models\Team\SimProTeams;
use App\Services\SimProTeam\SimProTeamService;
use App\Services\SimproWebhookService;

class AssetsParserJob extends AbstractJob
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
            $this->fetchAssets($company->sim_pro_company_id);
        }
    }

    public function fetchAssets($sim_pro_company_id, $page = 1)
    {
        $assets = $this->apiClient->getAssets($sim_pro_company_id, $page);

        foreach ($assets as $asset) {
            $webhook = [
                'name' => 'Assets',
                'ID' => 'asset.updated',
                'build' => $this->team->build_url,
                'reference' => [
                    'assetID' => $asset['ID'],
                    'companyID' => $sim_pro_company_id,
                ]
            ];

            app(SimproWebhookService::class)->process($webhook);
        }

        if(count($assets) >= 249){
            $this->fetchAssets($sim_pro_company_id, $page + 1);
        }
    }
}
