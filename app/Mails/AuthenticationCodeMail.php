<?php

namespace App\Mails;

class AuthenticationCodeMail extends BaseMail
{
    public function __construct(string $email, string $code)
    {
        parent::__construct(
            $email,
            [
                'code' => $code,
            ],
            'Verify Your Login Attempt',
            'emails.2fa_code'
        );
    }
}
