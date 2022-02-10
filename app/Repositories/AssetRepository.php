<?php

namespace App\Repositories;

use App\Models\Asset;
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
        }

        return $this;
    }
}
