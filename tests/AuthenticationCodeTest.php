<?php

namespace App\Tests;

use App\Mails\AuthenticationCodeMail;
use App\Services\AuthenticationCodeService;
use Illuminate\Support\Facades\Mail;
use RonasIT\Support\Traits\MockClassTrait;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationCodeTest extends TestCase
{
    use MockClassTrait;

    protected array $requiredOriginStates = [
        'authentication_codes',
    ];

    public function testSend(): void
    {
        $this->mockClass(AuthenticationCodeService::class, [
            [
                'method' => 'generateRandomCode',
                'result' => '123456',
            ]
        ]);

        $email = 'user2@example.com';

        $response = $this->json('post', '/auth/2fa-codes', [
            'email' => $email,
        ]);

        $response->assertNoContent(Response::HTTP_ACCEPTED);

        Mail::assertSent(AuthenticationCodeMail::class, 1);
        $this->assertMailEquals(AuthenticationCodeMail::class, [
            [
                'emails' => $email,
                'fixture' => 'send__email.html'
            ]
        ]);

        $this->assertChangesEqualsFixture('authentication_codes', 'send__authentication_codes_changes.json');
    }

    public function testSendNotExistedEmail(): void
    {
        $response = $this->json('post', '/auth/2fa-codes', [
            'email' => 'not.existed@example.com',
        ]);

        $response->assertNoContent(Response::HTTP_ACCEPTED);

        Mail::assertNothingSent();
        $this->assertNoChanges('authentication_codes');
    }
}
