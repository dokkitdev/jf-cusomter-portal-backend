<?php

namespace App\Services;

use App\Jobs\SendMailJob;
use App\Mails\AuthenticationCodeMail;
use App\Repositories\AuthenticationCodeRepository;
use Illuminate\Support\Carbon;

/**
 * @property AuthenticationCodeRepository $repository
 * @mixin AuthenticationCodeRepository
 */
class AuthenticationCodeService extends BaseService
{
    protected static UserService $userService;

    public function __construct()
    {
        parent::__construct();

        $this->setRepository(AuthenticationCodeRepository::class);

        self::$userService = self::$userService ?? app(UserService::class);
    }

    public function send(string $email): void
    {
        $user = self::$userService->getByEmailInsensitively($email);

        if (empty($user)) {
            return;
        }

        $code = $this->generateRandomCode();

        $this->delete(['user_id' => $user->id]);

        $this->create([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(config('defaults.2fa_code_ttl_minutes')),
        ]);

        $mail = new AuthenticationCodeMail($email, $code);
        dispatch(new SendMailJob($mail));
    }

    public function check(int $userId, string $code): bool
    {
        $isSuccess = $this->repository->check($userId, $code, Carbon::now());

        if ($isSuccess) {
            $this->delete([
                'user_id' => $userId,
            ]);
        }

        return $isSuccess;
    }

    protected function generateRandomCode(): string
    {
        return (string) rand(100000, 999999);
    }
}
