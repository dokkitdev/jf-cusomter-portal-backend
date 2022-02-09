<?php

namespace App\Models;

class JobNoAccessDate extends BaseModel
{
    protected $fillable = [
        'job_id',
        'simpro_job_log_id',
        'date'
    ];

    protected $hidden = ['pivot'];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}