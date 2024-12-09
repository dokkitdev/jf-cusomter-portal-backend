<?php

namespace App\Repositories;

use App\Models\AuthenticationCode;

/**
 * @property AuthenticationCode $model
*/
class AuthenticationCodeRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(AuthenticationCode::class);
    }
}
