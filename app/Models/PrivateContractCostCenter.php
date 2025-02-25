<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class PrivateContractCostCenter extends Model
{
    use ModelTrait;

    protected $fillable = [
        'private_contract_id',
        'simpro_section_id',
        'simpro_cost_center_id',
        'ex_tax',
        'tax',
        'inc_tax',
        'section_name',
        'name',
    ];

    protected $hidden = ['pivot'];

    public function private_contract()
    {
        return $this->belongsTo(PrivateContract::class);
    }
}
