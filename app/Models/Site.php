<?php

namespace App\Models;

use RonasIT\Support\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use ModelTrait;

    protected $fillable = [
        'simpro_site_id',
        'name',
        'uprn',
        'address',
        'postal_code',
        'city',
        'country',
        'county'
    ];

    protected $hidden = ['pivot'];

    public function scopeOnlyPermitted($query, $userId)
    {
        return $query->whereHas('customers.users', function ($query) use ($userId) {
            return $query->where('user_id', $userId);
        });
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class);
    }

    public function site_contacts()
    {
        return $this->hasMany(SiteContact::class);
    }

    public function primary_site_contact()
    {
        return $this->hasOne(SiteContact::class)->where('is_primary', true);
    }
}