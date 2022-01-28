<?php

namespace App\Repositories;

use App\Models\JobCatalog;

/**
 * @property JobCatalog $model
*/
class JobCatalogRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(JobCatalog::class);
    }
}
