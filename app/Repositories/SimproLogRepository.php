<?php

namespace App\Repositories;

use App\Models\SimproLog;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * @property SimproLog $model
*/
class SimproLogRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(SimproLog::class);
    }

    public function filterByMod(): self
    {
        if (Arr::get($this->filter, 'divider')) {
            $divider = Arr::get($this->filter, 'divider');
            $mod = Arr::get($this->filter, 'mod');

            $this->query->where(DB::raw("id % {$divider}"), $mod);
        }

        return $this;
    }
}
