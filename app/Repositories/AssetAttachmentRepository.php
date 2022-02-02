<?php

namespace App\Repositories;

use App\Models\AssetAttachment;

/**
 * @property AssetAttachment $model
*/
class AssetAttachmentRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(AssetAttachment::class);
    }
}
