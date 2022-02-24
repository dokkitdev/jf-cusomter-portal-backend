<?php

namespace App\Repositories;

use App\Models\Asset;
use App\Models\Job;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * @property Asset $model
*/
class AssetRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(Asset::class);
    }

    public function filterByOnlyPermitted(): self
    {
        if (Arr::has($this->filter, 'asset_has_user')) {
            $this->query->onlyPermitted($this->filter['asset_has_user']);
        }

        return $this;
    }

    public function filterByLastTestResult(): self
    {
        if (Arr::has($this->filter, 'last_test_result_query')) {
            $this->query->where($this->getQuerySearchCallbackWithValue('last_test_result', $this->filter['last_test_result_query']));
        }

        return $this;
    }

    public function filterByCP12Status(): self
    {
        if (Arr::has($this->filter, 'cp12_status')) {
            if ($this->filter['cp12_status'] === Asset::CP12_STATUS_ON_TIME) {
                $this->query->where(DB::raw("last_cp12_date + interval '1 year'"), '>', now()->addDays(28)->format('Y-m-d'));
            }

            if ($this->filter['cp12_status'] === Asset::CP12_STATUS_DUE) {
                $this->query
                    ->where(function (Builder $query) {
                        return $query
                            ->where(DB::raw("last_cp12_date + interval '1 year'"), '>', now()->format('Y-m-d'))
                            ->where(DB::raw("last_cp12_date + interval '1 year'"), '<', now()->addDays(28)->format('Y-m-d'));
                    });
            }

            if ($this->filter['cp12_status'] === Asset::CP12_STATUS_OVERDUE) {
                $this->query->where(DB::raw("last_cp12_date + interval '1 year'"), '<=', now()->format('Y-m-d'));
            }

            $this->query->whereDoesntHave('asset_test_record.job', function (Builder $query) {
                return $query->where('stage', Job::COMPLETE_STAGE);
            });
        }

        return $this;
    }

    public function filterBySiteName(): self
    {
        if (Arr::has($this->filter, 'site_name_query')) {
            $this->query->whereHas('site', function (Builder $query) {
                return $query->where($this->getQuerySearchCallbackWithValue('name', $this->filter['site_name_query']));
            });
        }

        return $this;
    }

    public function filterBySiteUprn(): self
    {
        if (Arr::has($this->filter, 'site_uprn_query')) {
            $this->query->whereHas('site', function (Builder $query) {
                return $query->where($this->getQuerySearchCallbackWithValue('uprn', $this->filter['site_uprn_query']));
            });
        }

        return $this;
    }

    public function filterByJobLoggedCompletionDate(): self
    {
        if (Arr::get($this->filter, 'job_logged_completion_date_from') || Arr::get($this->filter, 'job_logged_completion_date_to')) {
            $this->query->whereHas('asset_test_record.job', function (Builder $query) {
                $query->where(function (Builder $query) {
                    if (Arr::get($this->filter, 'job_logged_completion_date_from')) {
                        $query->where('logged_completion_date', '>=', $this->filter['job_logged_completion_date_from']);
                    }

                    if (Arr::get($this->filter, 'job_logged_completion_date_to')) {
                        $query->where('logged_completion_date', '<=', $this->filter['job_logged_completion_date_to']);
                    }
                });
            });
        }

        return $this;
    }

    public function filterByJobDueDate(): self
    {
        if (Arr::get($this->filter, 'job_due_date_from') || Arr::get($this->filter, 'job_due_date_to')) {
            $this->query->whereHas('asset_test_record.job', function (Builder $query) {
                $query->where(function (Builder $query) {
                    if (Arr::get($this->filter, 'job_due_date_from')) {
                        $query->where('due_date', '>=', $this->filter['job_due_date_from']);
                    }

                    if (Arr::get($this->filter, 'job_due_date_to')) {
                        $query->where('due_date', '<=', $this->filter['job_due_date_to']);
                    }
                });
            });
        }

        return $this;
    }
}
