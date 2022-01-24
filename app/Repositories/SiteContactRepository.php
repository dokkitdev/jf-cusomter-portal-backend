<?php

namespace App\Repositories;

use App\Models\SiteContact;

/**
 * @property SiteContact $model
*/
class SiteContactRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(SiteContact::class);
    }
}
