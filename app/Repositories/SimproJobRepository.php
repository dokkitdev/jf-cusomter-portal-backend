<?php

namespace App\Repositories;

use App\Models\SimproJob;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property SimproJob $model
*/
class SimproJobRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(SimproJob::class);
    }

    public function getForHandle(int $limit = 100, ?string $mod = null): Collection
    {
        $query = $this
            ->getQuery(['handle_status' => SimproJob::HANDLE_STATUS_NEW])
            ->orderBy('id')
            ->limit($limit);

        if ($mod !== null) {
            $query->where(DB::raw('id % 2'), $mod);
        }

        return $query->get();
    }

    public function deleteErrorJobs(string $date): int
    {
        return $this
            ->getQuery()
            ->where('handle_status', SimproJob::HANDLE_STATUS_ERROR)
            ->where('created_at', '<', $date)
            ->delete();
    }
}
