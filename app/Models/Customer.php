<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use ModelTrait;

    const TYPE_COMPANIES = 'companies';
    const TYPE_INDIVIDUALS = 'individuals';

    protected $fillable = [
        'customer_id',
        'name',
        'type'
    ];

    protected $hidden = ['pivot'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}