<?php

namespace App\Repositories;

use App\Models\AuthenticationCode;
use Illuminate\Support\Carbon;

/**
 * @property AuthenticationCode $model
*/
class AuthenticationCodeRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(AuthenticationCode::class);
    }

    public function check(int $userId, string $code, Carbon $now): bool
    {
        return $this
            ->getQuery()
            ->where('user_id', $userId)
            ->where('code', $code)
            ->where('expires_at', '>', $now)
            ->exists();
    }
}
