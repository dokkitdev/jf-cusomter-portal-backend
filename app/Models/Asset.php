<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Asset extends BaseModel
{
    const CP12_STATUS_ON_TIME = 'On Time';
    const CP12_STATUS_DUE = 'Due';
    const CP12_STATUS_OVERDUE = 'Overdue';

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
        'custom_asset_type_value',
        'asset_test_record_id',
        'job_id',
        'customer_id',
        'next_schedule_id',
        'cp12_status',
        'no_access_date_1',
        'no_access_date_2',
        'no_access_date_3',
        'no_access_date_4',
        'no_access_date_5',
        'expiry_date',
        'sortable_date',
        'test_record_date'
    ];

    protected $hidden = ['pivot'];

    public function scopeOnlyPermitted(Builder $query, int $userId): Builder
    {
        return $query->whereHas('site.customers.users', function ($query) use ($userId) {
            return $query->where('user_id', $userId);
        });
    }

    public function getCp12StatusAttribute()
    {
        if (!in_array(Arr::get($this, 'job.stage'), Job::OPEN_STAGES)) {
            return null;
        }

        if (empty($this['next_service_date'])) {
            return self::CP12_STATUS_ON_TIME;
        }

        $nextServiceDate = Carbon::parse($this['next_service_date'])->startOfDay();

        if ($nextServiceDate->lte(Carbon::now()->addYear()->startOfDay())) {
            return self::CP12_STATUS_OVERDUE;
        }

        if ($nextServiceDate->gt(Carbon::now()->addYear()->addDays(28)->startOfDay())) {
            return self::CP12_STATUS_ON_TIME;
        }

        return self::CP12_STATUS_DUE;
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

    public function asset_test_record()
    {
        return $this->hasOne(AssetTestRecord::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function job_customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function next_schedule()
    {
        return $this->hasOne(Schedule::class, 'job_id', 'job_id')
            ->where(DB::raw('cast(date as date)'), '>=', now()->format('Y-m-d'))
            ->orderBy('date');
    }
}