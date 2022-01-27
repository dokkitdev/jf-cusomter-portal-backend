<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class JobAttachment extends Model
{
    use ModelTrait;

    protected $fillable = [
        'job_id',
        'simpro_attachment_id',
        'name',
        'date_added'
    ];

    protected $hidden = ['pivot'];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}