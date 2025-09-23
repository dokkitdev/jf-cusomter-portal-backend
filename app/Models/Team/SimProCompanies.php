<?php

namespace App\Models\Team;

use App\Models\BaseModel;

class SimProCompanies extends BaseModel
{
    protected $fillable = [
        'sim_pro_team_id',
        'sim_pro_company_id',
        'name'
    ];
}
