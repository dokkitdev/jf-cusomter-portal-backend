<?php

namespace App\Mails;

use App\Services\SettingService;
use Illuminate\Support\Arr;

class InvitationMail extends BaseMail
{
    public function __construct($to, array $data)
    {
        parent::__construct(
            $to,
            $data,
            'Invitation to the James Frew Portal',
            'emails.invitation'
        );

        $adminEmail = app(SettingService::class)->get('admin_email');

        if (Arr::get($adminEmail, 'email')) {
            $this->cc($adminEmail['email']);
        }
    }
}
