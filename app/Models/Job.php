<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Job extends BaseModel
{
    const PENDING_STAGE = 'Pending';
    const PROGRESS_STAGE = 'Progress';
    const COMPLETE_STAGE = 'Complete';
    const ARCHIVED_STAGE = 'Archived';
    const INVOICED_STAGE = 'Invoiced';

    const OPEN_STAGES = [
        self::PENDING_STAGE,
        self::PROGRESS_STAGE
    ];

    protected $fillable = [
        'simpro_job_id',
        'customer_id',
        'site_id',
        'order_no',
        'description',
        'priority',
        'cost_center_name',
        'stage',
        'job_status',
        'date_created',
        'made_safe_date',
        'completion_date',
        'due_date',
        'recent_schedule_id',
        'logged_create_date',
        'logged_completion_date',
        'next_schedule_id',
        'is_repair',
        'name'
    ];

    protected $hidden = ['pivot'];

    public function scopeOnlyPermitted(Builder $query, int $userId): Builder
    {
        return $query->whereHas('customer.users', function ($query) use ($userId) {
            return $query->where('user_id', $userId);
        });
    }

    public function scopeOutOfHours(Builder $query): Builder
    {
        $yesterday = now()->subDay()->format('Y-m-d');
        $today = now()->format('Y-m-d');

        return $query
            ->where(function (Builder $query) use ($today, $yesterday) {
                return $query
                    ->where(function (Builder $query) use ($today) {
                        return $query
                            ->where('date_created', $today)
                            ->where('logged_create_date', '>=', "{$today} 00:00:00")
                            ->where('logged_create_date', '<', "{$today} 07:00:00");
                    })
                    ->orWhere(function (Builder $query) use ($yesterday) {
                        return $query
                            ->where('date_created', $yesterday)
                            ->where('logged_create_date', '>=', "{$yesterday} 17:00:00")
                            ->where('logged_create_date', '<=', "{$yesterday} 23:59:59");
                    });
            });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function recent_schedule()
    {
        return $this->belongsTo(Schedule::class, 'recent_schedule_id', 'id');
    }

    public function next_schedule()
    {
        return $this->belongsTo(Schedule::class, 'next_schedule_id', 'id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function job_catalogs()
    {
        return $this->hasMany(JobCatalog::class);
    }

    public function job_attachments()
    {
        return $this->hasMany(JobAttachment::class);
    }

    public function job_work_orders()
    {
        return $this->hasMany(JobWorkOrder::class);
    }

    public function job_no_access_dates()
    {
        return $this->hasMany(JobNoAccessDate::class);
    }
}