<?php

namespace App\Repositories;

use App\Models\JobAttachment;

/**
 * @property JobAttachment $model
*/
class JobAttachmentRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(JobAttachment::class);
    }
}
