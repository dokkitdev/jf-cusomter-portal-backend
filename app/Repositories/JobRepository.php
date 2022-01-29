<?php

namespace App\Repositories;

use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * @property Job $model
*/
class JobRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(Job::class);
    }

    public function filterByOnlyPermitted(): self
    {
        if (Arr::has($this->filter, 'job_has_user')) {
            $this->query->onlyPermitted($this->filter['job_has_user']);
        }

        return $this;
    }

    public function filterByRecentSchedule(): self
    {
        if (Arr::has($this->filter, 'appointment_from') || Arr::has($this->filter, 'appointment_to') ||
            Arr::has($this->filter, 'start_time_from') || Arr::has($this->filter, 'start_time_to') ||
            Arr::has($this->filter, 'end_time_from') || Arr::has($this->filter, 'end_time_to')) {

            $this->query->whereHas('recent_schedule', function ($query) {
                if (Arr::has($this->filter, 'appointment_from')) {
                    $query->where('date', '>=', $this->filter['appointment_from']);
                }
                if (Arr::has($this->filter, 'appointment_to')) {
                    $query->where('date', '<=', $this->filter['appointment_to']);
                }
                if (Arr::has($this->filter, 'start_time_from')) {
                    $time = Carbon::createFromFormat('Y-m-d H:i:s', $this->filter['start_time_from'])->format('H:i');
                    $query->where(DB::raw('cast(start_time as time)'), '>=', $time);
                }
                if (Arr::has($this->filter, 'start_time_to')) {
                    $time = Carbon::createFromFormat('Y-m-d H:i:s', $this->filter['start_time_to'])->format('H:i');
                    $query->where(DB::raw('cast(start_time as time)'), '<=', $time);
                }
                if (Arr::has($this->filter, 'end_time_from')) {
                    $time = Carbon::createFromFormat('Y-m-d H:i:s', $this->filter['end_time_from'])->format('H:i');
                    $query->where(DB::raw('cast(end_time as time)'), '>=', $time);
                }
                if (Arr::has($this->filter, 'end_time_to')) {
                    $time = Carbon::createFromFormat('Y-m-d H:i:s', $this->filter['end_time_to'])->format('H:i');
                    $query->where(DB::raw('cast(end_time as time)'), '<=', $time);
                }
            });
        }

        return $this;
    }

    public function filterByPostalCode(): self
    {
        if (Arr::has($this->filter, 'postal_code')) {
            $postalCode = str_replace(' ', '', $this->filter['postal_code']);

            $this->query->whereHas('site', function ($query) use ($postalCode) {
                $query->where(DB::raw("REPLACE(postal_code, ' ', '')"), $postalCode);
            });
        }

        return $this;
    }

    public function filterByPriority(): self
    {
        if (Arr::has($this->filter, 'priority')) {
            $this->query->where(function ($query) {
                foreach ($this->filter['priority'] as $priority) {
                    $loweredQuery = mb_strtolower($priority);
                    $query->orWhere(DB::raw("lower(priority)"), 'like', "%{$loweredQuery}%");
                }
            });
        }

        return $this;
    }

    public function filterByOrderNo(): self
    {
        if (Arr::has($this->filter, 'order_no_query')) {
            $this->query->where($this->getQuerySearchCallbackWithValue('order_no', $this->filter['order_no_query']));
        }

        return $this;
    }

    public function filterByUprn(): self
    {
        if (Arr::has($this->filter, 'uprn_query')) {
            $this->query->whereHas('site', function ($query) {
                return $query->where($this->getQuerySearchCallbackWithValue('uprn', $this->filter['uprn_query']));
            });
        }

        return $this;
    }
}
