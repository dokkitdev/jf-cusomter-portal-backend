<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class JobCatalog extends Model
{
    use ModelTrait;

    protected $fillable = [
        'job_id',
        'simpro_section_id',
        'simpro_cost_center_id',
        'simpro_catalog_id',
        'simpro_original_catalog_id',
        'name',
        'part_no',
        'qty'
    ];

    protected $hidden = ['pivot'];

    protected $casts = [
        'qty' => 'float'
    ];
}