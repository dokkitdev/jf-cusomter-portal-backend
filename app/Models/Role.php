<?php

namespace App\Models;

class Role extends BaseModel
{
    const ADMIN = 1;
    const USER = 2;
    const CUSTOMER = 3;
    
    protected $fillable = [
        'name',
    ];

    protected $hidden = ['pivot'];
}