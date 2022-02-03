<?php

namespace App\Services;

use App\Jobs\SendMailJob;
use App\Mails\ForgotPasswordMail;
use App\Mails\InvitationMail;
use App\Models\Job;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * @property UserRepository $repository
 * @mixin UserRepository
 */
class UserService extends BaseService
{
    protected JobService $jobService;

    public function __construct()
    {
        parent::__construct();

        $this->setRepository(UserRepository::class);

        $this->jobService = app(JobService::class);
    }

    public function search(array $filters): LengthAwarePaginator
    {
        return $this->repository
            ->with(Arr::get($filters, 'with', []))
            ->searchQuery($filters)
            ->filterByList('role_id', 'role_ids')
            ->filterByList('customers.customer_id', 'customer_ids')
            ->filterByQuery(['name', 'email'])
            ->filterByQueryWithValue('name', 'name_query')
            ->filterByQueryWithValue('email', 'email_query')
            ->getSearchResults();
    }

    public function create(array $data): Model
    {
        $data['role_id'] = Arr::get($data, 'role_id', Role::CUSTOMER);
        $data['password'] = Hash::make($this->generateHash());
        $data['set_password_hash'] = $this->generateHash();
        $data['set_password_hash_created_at'] = Carbon::now();

        $user = DB::transaction(function () use ($data) {
            $user = $this->repository
                ->force()
                ->create($data);

            if (Arr::has($data, 'customer_ids')) {
                $user->customers()->sync($data['customer_ids']);
            }

            return $user;
        });

        if (Arr::get($data, 'is_send_email')) {
            $this->sendInvitationEmail($data['email'], $data['set_password_hash']);
        }

        return $user;
    }

    public function update($where, array $data): Model
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return DB::transaction(function () use ($where, $data) {
            $user = $this->repository
                ->force()
                ->update($where, $data);

            if (Arr::has($data, 'customer_ids')) {
                $user->customers()->sync($data['customer_ids']);
            }

            return $user;
        });
    }

    public function forgotPassword(string $email): void
    {
        $hash = $this->generateHash();

        $this->repository
            ->force()
            ->update([
                'email' => $email
            ], [
                'set_password_hash' => $hash,
                'set_password_hash_created_at' => Carbon::now()
            ]);

        $mail = new ForgotPasswordMail($email, ['hash' => $hash]);
        dispatch(new SendMailJob($mail));
    }

    public function restorePassword(string $token, string $password): void
    {
        $this->repository
            ->force()
            ->update([
                'set_password_hash' => $token
            ], [
                'password' => Hash::make($password),
                'set_password_hash' => null
            ]);
    }

    public function resendInvitation(int $id): void
    {
        $data = [
            'set_password_hash' => $this->generateHash(),
            'set_password_hash_created_at' => Carbon::now()
        ];

        $user = $this->repository
            ->force()
            ->update($id, $data);

        $this->sendInvitationEmail($user['email'], $data['set_password_hash']);
    }

    public function getDashboardCounters(): array
    {
        $data = [
            'per_page' => 0,
            'all' => 1,
        ];

        $todaysJobs = $this->jobService->search(array_merge($data, [
            'date_created' => now()->format('Y-m-d')
        ]));

        $outOfHoursJobs = $this->jobService->search(array_merge($data, [
            'out_of_hours' => true
        ]));

        $data['stage'] = [Job::PENDING_STAGE];
        $pendingJobs = $this->jobService->search($data);

        $data['stage'] = [Job::PROGRESS_STAGE];
        $progressJobs = $this->jobService->search($data);

        $data['stage'] = [Job::COMPLETE_STAGE];
        $completeJobs = $this->jobService->search($data);

        $data['stage'] = [Job::INVOICED_STAGE];
        $invoicedJobs = $this->jobService->search($data);

        $data['stage'] = [Job::ARCHIVED_STAGE];
        $archivedJobs = $this->jobService->search($data);

        return [
            'todays_jobs_total' => $todaysJobs->total(),
            'out_of_hours_jobs_total' => $outOfHoursJobs->total(),
            'pending_jobs_total' => $pendingJobs->total(),
            'progress_jobs_total' => $progressJobs->total(),
            'complete_jobs_total' => $completeJobs->total(),
            'invoiced_jobs_total' => $invoicedJobs->total(),
            'archived_jobs_total' => $archivedJobs->total(),
        ];
    }

    protected function sendInvitationEmail(string $email, string $hash): void
    {
        $mail = new InvitationMail($email, ['hash' => $hash]);
        dispatch(new SendMailJob($mail));
    }

    protected function generateHash(int $length = 32): string
    {
        $length /= 2;

        return bin2hex(openssl_random_pseudo_bytes($length));
    }
}
