<?php

namespace App\Services\SimProTeam;

use App\Models\Team\SimProCompanies;
use App\Models\Team\SimProTeams;
use App\Repositories\AssetRepository;
use App\Services\BaseService;

/**
 * @property AssetRepository $repository
 * @mixin AssetRepository
 */
class SimProTeamService extends BaseService
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getSimProTeam($buildUrl)
    {
        return SimProTeams::where('build_url', $buildUrl)->first();
    }

    public function getCompanies($team_id)
    {
        return SimProCompanies::where('sim_pro_team_id', $team_id)->get();
    }

    public function addSimProTeam($buildUrl)
    {
        return SimProTeams::updateOrCreate([
            'build_url' => $buildUrl
        ],[
            'name' => $buildUrl,
        ]);
    }

    public function updateCompany($team, $company)
    {
        SimProCompanies::updateOrCreate([
            'sim_pro_team_id' => $team->id,
            'sim_pro_company_id' => $company['ID'],
        ], [
            'name' => $company['Name'],
        ]);
    }

    public function updateToken($team, $token)
    {
        $team->update([
            'token' => $token
        ]);
    }

    public function updateWithBuildUrl($build_url, $token)
    {
        return SimProTeams::where('build_url', $build_url)->update([
            'token' => $token
        ]);
    }

    public function fetchCompanyBySimProId($team_id, $companyId)
    {
       return SimProCompanies::where('sim_pro_team_id', $team_id)
            ->where('sim_pro_company_id', $companyId)->first();
    }

}
