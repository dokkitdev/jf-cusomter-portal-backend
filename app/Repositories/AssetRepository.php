<?php

namespace App\Repositories;

use App\Models\Asset;
use App\Models\Job;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

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
            $this->query->whereHas('job', function ($query) {
                return $query->whereIn('stage', Job::OPEN_STAGES);
            });

            if ($this->filter['cp12_status'] === Asset::CP12_STATUS_ON_TIME) {
                $this->query->where(function (Builder $query) {
                    return $query
                        ->where(function (Builder $query) {
                            return $query
                                ->whereNull('next_service_date');
                        })
                        ->orWhereRaw("next_service_date > DATE (CURRENT_DATE + INTERVAL '1 year 28 days')");
                });
            }

            if ($this->filter['cp12_status'] === Asset::CP12_STATUS_DUE) {
                $this->query->where(function (Builder $query) {
                    return $query
                        ->where(function (Builder $query) {
                            return $query
                                ->whereNotNull('next_service_date');
                        })
                        ->whereRaw("next_service_date > DATE (CURRENT_DATE + INTERVAL '1 year')")
                        ->WhereRaw("next_service_date <= DATE (CURRENT_DATE + INTERVAL '1 year 28 days')");
                });
            }

            if ($this->filter['cp12_status'] === Asset::CP12_STATUS_OVERDUE) {
                $this->query->where(function (Builder $query) {
                    return $query
                        ->where(function (Builder $query) {
                            return $query
                                ->whereNotNull('next_service_date');
                        })
                        ->whereRaw("next_service_date <= DATE (CURRENT_DATE + INTERVAL '1 year')");
                });
            }
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
            $this->query->whereHas('job', function (Builder $query) {
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
            $this->query->whereHas('job', function (Builder $query) {
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

    public function filterByReport($groupAsset = false)
    {
        if (Arr::get($this->filter, 'report')) {
            $this->query->where(function (Builder $query) use ($groupAsset) {
                $query->where('archived', false)
                    ->whereNotNull('job_id');

                if($groupAsset){
                    $query->whereIn('asset_type', [10,11]);
                }else{
                    $query->where('asset_type', 4);
                }

            });
        }

        return $this;
    }


    public function searchQuery(array $filter): self
    {
        $this->with(Arr::get($filter, 'with', []));

        $searchAssetGroup = false;
        if($filter['asset_type'] == 0){
            $searchAssetGroup = true;
            $filter['asset_type'] = [10, 11];
        }

        $res = parent::searchQuery($filter)
            ->filterByList('job.stage', 'job_stage');
        if($searchAssetGroup){
            $res = $res->filterByList('asset_type', 'asset_type');
        }else{
            $res = $res->filterBy('asset_type');
        }

        return $res->filterByList('custom_asset_type_value', 'custom_asset_type_value')
            ->filterByIntQuery('simpro_asset_id')
            ->filterBy('job_id')
            ->filterBy('site.customer_id')
            ->filterBy('customer_id', 'job_customer_id')
            ->filterBy('site_id')
            ->filterBy('archived')
            ->filterByList('name', 'names')
            ->filterByList('service_level_name', 'service_level_names')
            ->filterByQuery(['name'])
            ->filterByQueryWithValue('location', 'location_query')
            ->filterByQueryWithValue('customer_name', 'customer_name_query')
            ->filterByQueryWithValue('make', 'make_query')
            ->filterByQueryWithValue('model', 'model_query')
            ->filterBy('last_test_date')
            ->filterFrom('last_test_date', false, 'last_test_date_from')
            ->filterTo('last_test_date', false, 'last_test_date_to')
            ->filterBy('next_service_date')
            ->filterFrom('next_service_date', false, 'next_service_date_from')
            ->filterTo('next_service_date', false, 'next_service_date_to')
            ->filterByReport($searchAssetGroup)
            ->filterByLastTestResult()
            ->filterByOnlyPermitted()
            ->filterBySiteName()
            ->filterBySiteUprn()
            ->filterByCP12Status()
            ->filterByJobDueDate()
            ->filterByJobLoggedCompletionDate();
    }
}
