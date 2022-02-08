<?php

namespace App\Models;

class JobCatalog extends BaseModel
{
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