<?php

namespace App\Repositories;

use App\Models\JobNoAccessDate;

/**
 * @property JobNoAccessDate $model
*/
class JobNoAccessDateRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(JobNoAccessDate::class);
    }
}
