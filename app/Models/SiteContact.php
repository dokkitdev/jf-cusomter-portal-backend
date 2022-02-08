<?php

namespace App\Models;

class SiteContact extends BaseModel
{
    protected $fillable = [
        'site_id',
        'simpro_contact_id',
        'title',
        'name',
        'given_name',
        'family_name',
        'email',
        'work_phone',
        'cell_phone',
        'position',
        'is_primary'
    ];

    protected $hidden = ['pivot'];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}