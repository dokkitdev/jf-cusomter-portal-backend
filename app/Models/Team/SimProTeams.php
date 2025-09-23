<?php

namespace App\Models\Team;

use App\Models\BaseModel;

class SimProTeams extends BaseModel
{
    protected $fillable = [
        'name',
        'build_url',
        'token',
        'api_key',
    ];

    public function companies()
    {
        return $this->hasMany(SimProCompanies::class, 'sim_pro_team_id', 'id');

    }
}
