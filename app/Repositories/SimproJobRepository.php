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

    public function getForHandle(int $limit = 100, ?string $eventId = null, ?string $divider = null, ?string $mod = null): Collection
    {
        $query = $this
            ->getQuery(['handle_status' => SimproJob::HANDLE_STATUS_NEW])
            ->orderBy('id')
            ->limit($limit);

        if ($eventId) {
            $query->where('data->ID', 'LIKE', "{$eventId}.%");
        }

        if ($divider) {
            $query->where(DB::raw("simpro_entity_id % {$divider}"), $mod);
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
