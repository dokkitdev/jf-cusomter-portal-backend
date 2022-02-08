<?php

namespace App\Models;

class Schedule extends BaseModel
{
    protected $fillable = [
        'job_id',
        'simpro_schedule_id',
        'name',
        'date',
        'start_time',
        'end_time'
    ];

    protected $hidden = ['pivot'];
}