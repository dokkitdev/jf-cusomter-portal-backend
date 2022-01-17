<?php
namespace App\Services;

use Illuminate\Support\Facades\Auth;
use RonasIT\Support\Services\EntityService;

class BaseService extends EntityService
{
    protected $authUser;

    public function __construct()
    {
        $this->authUser = Auth::user();
    }

    public function getAuthUser()
    {
        return $this->authUser;
    }
}