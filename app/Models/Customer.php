<?php

namespace App\Models;

class Customer extends BaseModel
{
    const TYPE_COMPANIES = 'companies';
    const TYPE_INDIVIDUALS = 'individuals';

    protected $fillable = [
        'simpro_customer_id',
        'name',
        'type'
    ];

    protected $hidden = ['pivot'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function sites()
    {
        return $this->belongsToMany(Site::class);
    }
}