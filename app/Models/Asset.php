<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Asset extends BaseModel
{
    protected $fillable = [
        'simpro_asset_id',
        'site_id',
        'name',
        'customer_name',
        'location',
        'make',
        'model',
        'last_test_date',
        'next_service_date',
        'last_test_result',
        'service_level_name',
        'archived',
        'asset_type',
        'last_cp12_date',
        'custom_asset_type_value'
    ];

    protected $hidden = ['pivot'];

    public function scopeOnlyPermitted(Builder $query, int $userId): Builder
    {
        return $query->whereHas('site.customers.users', function ($query) use ($userId) {
            return $query->where('user_id', $userId);
        });
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function asset_custom_fields()
    {
        return $this->hasMany(AssetCustomField::class);
    }

    public function asset_attachments()
    {
        return $this->hasMany(AssetAttachment::class);
    }

    public function asset_test_records()
    {
        return $this->hasMany(AssetTestRecord::class);
    }
}