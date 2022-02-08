<?php

namespace App\Models;

class JobAttachment extends BaseModel
{
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