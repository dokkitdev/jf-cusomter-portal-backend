<?php

namespace App\Models;

class JobWorkOrder extends BaseModel
{
    protected $fillable = [
        'job_id',
        'simpro_section_id',
        'simpro_cost_center_id',
        'simpro_work_order_id',
        'name',
        'description',
        'date'
    ];

    protected $hidden = ['pivot'];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}