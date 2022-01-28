<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use ModelTrait;

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