<?php

namespace App\Repositories;

use App\Models\SimproJob;
use Illuminate\Support\Collection;
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

    public function getForHandle(int $limit = 100): Collection
    {
        return $this
            ->getQuery(['handle_status' => SimproJob::HANDLE_STATUS_NEW])
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->get();
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
