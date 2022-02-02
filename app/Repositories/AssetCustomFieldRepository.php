<?php

namespace App\Repositories;

use App\Models\AssetCustomField;

/**
 * @property AssetCustomField $model
*/
class AssetCustomFieldRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(AssetCustomField::class);
    }
}
