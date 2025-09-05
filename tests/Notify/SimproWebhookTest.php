<?php

namespace App\Tests\Notify;

use App\Modules\Notify\Jobs\ProcessSimproWebhookJob;
use App\Tests\Support\MockHttpRequestServiceTrait;
use App\Tests\TestCase;
use Illuminate\Support\Facades\Queue;
use Symfony\Component\HttpFoundation\Response;

class SimproWebhookTest extends TestCase
{
    use MockHttpRequestServiceTrait;

    protected array $requiredOriginStates = [
        'notify_simpro_webhooks',
    ];

    public function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    /*************************************
     *             REGISTER              *
     *************************************/

    public function getDataTestRegister(): array
    {
        return [
            [
                'signature' => 'f0dcb140f905a8c3f84e7d4fe800d963e5dd7e77',
                'statusCode' => Response::HTTP_NO_CONTENT,
                'responseFixture' => null,
                'dbChanges' => [
                    'notify_simpro_webhooks' => 'success__db_changes__notify_simpro_webhook.json',
                ],
            ],
            [
                'signature' => 'incorrect_signature',
                'statusCode' => Response::HTTP_BAD_REQUEST,
                'responseFixture' => 'incorrect_signature__response.json',
                'dbChanges' => [
                    'notify_simpro_webhooks' => null,
                ],
            ],
        ];
    }

    /**
     * @dataProvider getDataTestRegister
     * @testCase register
     */
    public function testRegister(
        string $signature,
        int $statusCode,
        ?string $responseFixture,
        array $dbChanges
    ): void {
        $response = $this->json('post', '/notify/simpro-webhook', [
            'any_filed' => 'any_value'
        ], [
            'X-Response-Signature' => $signature,
        ]);

        $response->assertStatus($statusCode);

        if (isset($responseFixture)) {
            $this->assertEqualsFixture($responseFixture, $response->json());
        } else {
            $response->assertNoContent($statusCode);
        }

        foreach ($dbChanges as $table => $dbChangesFixture) {
            if (isset($dbChangesFixture)) {
                $this->assertChangesEqualsFixture($table, $dbChangesFixture);
            } else {
                $this->assertNoChanges($table);
            }
        }

        if ($statusCode === Response::HTTP_NO_CONTENT) {
            Queue::assertPushed(ProcessSimproWebhookJob::class, 1);
            Queue::assertPushed(ProcessSimproWebhookJob::class, function (ProcessSimproWebhookJob $job) {
                $this->assertEquals(1, $job->getNotifySimproWebhookId());
                return true;
            });
        } else {
            Queue::assertNothingPushed();
        }
    }
}
