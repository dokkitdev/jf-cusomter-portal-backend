<?php

namespace App\Services;

use App\Repositories\AuthenticationCodeRepository;

/**
 * @property AuthenticationCodeRepository $repository
 * @mixin AuthenticationCodeRepository
 */
class AuthenticationCodeService extends BaseService
{
    public function __construct()
    {
        parent::__construct();

        $this->setRepository(AuthenticationCodeRepository::class);
    }
}
