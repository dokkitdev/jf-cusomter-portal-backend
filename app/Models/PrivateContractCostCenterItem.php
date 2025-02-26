<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class PrivateContractCostCenterItem extends Model
{
    use ModelTrait;

    const TYPE_CATALOGS = 'Catalogs';
    const TYPE_ONEOFF = 'OneOffs';
    const TYPE_PREBUILDS = 'Prebuilds';
    const TYPE_DISCOUNT = 'Discount';

    const TYPES_FROM_SIMPRO = [
        self::TYPE_CATALOGS,
        self::TYPE_ONEOFF,
        self::TYPE_PREBUILDS,
    ];

    protected $fillable = [
        'simpro_recurring_invoice_cost_center_item_id',
        'qty',
        'private_contract_cost_center_id',
        'order',
        'ex_tax',
        'inc_tax',
        'type',
        'name',
    ];

    protected $hidden = ['pivot'];
}
