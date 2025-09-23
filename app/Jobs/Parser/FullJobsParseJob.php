<?php

namespace App\Jobs\Parser;

use App\ApiClients\SimproApiClient;
use App\Jobs\AbstractJob;
use App\Models\Team\SimProTeams;
use App\Services\SimProTeam\SimProTeamService;
use App\Services\SimproWebhookService;

class FullJobsParseJob extends AbstractJob
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
            if($company->sim_pro_company_id !== 0){
                $this->fetchJobs($company->sim_pro_company_id);
            }
        }
    }

    public function fetchJobs($sim_pro_company_id, $page = 1)
    {
        $jobs = $this->apiClient->getJobs($sim_pro_company_id, $page);
        foreach ($jobs as $job) {
            $webhook = [
                'name' => 'Job',
                'ID' => 'job.updated',
                'build' => $this->team->build_url,
                'reference' => [
                    'jobID' => $job['ID'],
                    'companyID' => $sim_pro_company_id,
                ]
            ];

            app(SimproWebhookService::class)->process($webhook);
        }

        if(count($jobs) >= 249){
            $this->fetchJobs($sim_pro_company_id, $page + 1);
        }
    }
}
