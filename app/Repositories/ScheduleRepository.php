<?php

namespace App\Repositories;

use App\Models\Schedule;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Schedule $model
*/
class ScheduleRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(Schedule::class);
    }

    public function getRecentSchedule(int $jobId): ?Model
    {
        return $this
            ->getQuery(['job_id' => $jobId])
            ->orderBy('date', 'desc')
            ->first();
    }
}
