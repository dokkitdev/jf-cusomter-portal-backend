<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Site extends BaseModel
{
    protected $fillable = [
        'simpro_site_id',
        'name',
        'uprn',
        'address',
        'postal_code',
        'city',
        'country',
        'county',
        'customer_id'
    ];

    protected $hidden = ['pivot'];

    public function scopeOnlyPermitted(Builder $query, int $userId): Builder
    {
        return $query->whereHas('customers.users', function ($query) use ($userId) {
            return $query->where('user_id', $userId);
        });
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function site_contacts()
    {
        return $this->hasMany(SiteContact::class);
    }

    public function primary_site_contact()
    {
        return $this->hasOne(SiteContact::class)->where('is_primary', true);
    }

    public function open_jobs()
    {
        return $this->hasMany(Job::class)->whereIn('stage', Job::OPEN_STAGES);
    }
}