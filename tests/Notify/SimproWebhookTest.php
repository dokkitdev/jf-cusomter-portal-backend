<?php

namespace App\Tests\Notify;

use App\Modules\Notify\Jobs\ProcessSimproWebhookJob;
use App\Modules\Notify\Services\AssetReport\AssetReportGenerator;
use App\Modules\Notify\Services\SimproWebhooks\ProcessSimproWebhookAction;
use App\Tests\Support\MockClassTrait;
use App\Tests\Support\MockHttpRequestServiceTrait;
use App\Tests\TestCase;
use Exception;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Queue;
use Symfony\Component\HttpFoundation\Response;

class SimproWebhookTest extends TestCase
{
    use MockClassTrait;
    use MockHttpRequestServiceTrait;

    protected array $requiredOriginStates = [
        'notify_simpro_webhooks',
        'notify_asset_reports',
        'notify_asset_report_validations',
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

    /*************************************
     *          PROCESS COMMON           *
     *************************************/

    /**
     * @testCase process__common
     */
    public function testProcessCommonSuccess(): void
    {
        $this->mockClass(ProcessSimproWebhookAction::class, [
            [
                'method' => 'processWebhookData',
                'arguments' => [['key2' => 'value2']],
            ],
        ]);

        app(Dispatcher::class)->dispatchNow(new ProcessSimproWebhookJob(2));

        $this->assertChangesEqualsFixture('notify_simpro_webhooks', 'success__db_changes__notify_simpro_webhooks.json');
    }

    /**
     * @testCase process__common
     */
    public function testProcessCommonException(): void
    {
        $this->mockClass(ProcessSimproWebhookAction::class, [
            [
                'method' => 'processWebhookData',
                'arguments' => [['key2' => 'value2']],
                'exception' => true
            ],
        ]);

        try {
            app(Dispatcher::class)->dispatchNow(new ProcessSimproWebhookJob(2));
        } catch (Exception $exception) {
        }

        $this->assertChangesEqualsFixture('notify_simpro_webhooks', 'exception__db_changes__notify_simpro_webhooks.json');
    }

    /**
     * @testCase process__common
     */
    public function testProcessCommonFailed(): void
    {
        $job = new ProcessSimproWebhookJob(2);
        $job->failed();

        $this->assertChangesEqualsFixture('notify_simpro_webhooks', 'failed__db_changes__notify_simpro_webhooks.json');
    }

    /*************************************
     *              PROCESS              *
     *           asset.deleted           *
     *************************************/

    /**
     * @testCase process__asset_deleted
     */
    public function testProcessAssetDeleted(): void
    {
        app(Dispatcher::class)->dispatchNow(new ProcessSimproWebhookJob(1));

        $this->assertChangesEqualsFixture('notify_simpro_webhooks', 'db_changes__notify_simpro_webhooks.json');
        $this->assertChangesEqualsFixture('notify_asset_reports', 'db_changes__notify_asset_reports.json');
        $this->assertChangesEqualsFixture('notify_asset_report_validations', 'db_changes__notify_asset_report_validations.json');
    }

    /*************************************
     *              PROCESS              *
     *    asset.created, asset.updated   *
     *************************************/

    public function getDataTestProcessAssetCreatedOrAssetUpdatedCheckTheSameCodeProcesses(): array
    {
        return [
            [
                //asset.created
                'notify_simpro_webhook_id' => 1,
                'db_changes' => [
                    'notify_simpro_webhooks' => 'process__asset_created__db_changes__notify_simpro_webhooks.json',
                ],
            ],
            [
                //asset.updated
                'notify_simpro_webhook_id' => 2,
                'db_changes' => [
                    'notify_simpro_webhooks' => 'process__asset_updated__db_changes__notify_simpro_webhooks.json',
                ],
            ],
        ];
    }

    /**
     * @dataProvider getDataTestProcessAssetCreatedOrAssetUpdatedCheckTheSameCodeProcesses
     * @testCase process__asset_created_or_updated__check_the_same_code_processes
     */
    public function testProcessAssetCreatedOrAssetUpdatedCheckTheSameCodeProcesses(int $notifySimproWebhookId, array $dbChanges): void
    {
        $this->mockClass(AssetReportGenerator::class, [
            [
                'method' => 'createOrUpdateAssetReport',
                'arguments' => [11, 111],
            ],
        ]);

        app(Dispatcher::class)->dispatchNow(new ProcessSimproWebhookJob($notifySimproWebhookId));

        foreach ($dbChanges as $table => $dbChangesFixture) {
            $this->assertChangesEqualsFixture($table, $dbChangesFixture);
        }
    }

    public function getDataTestProcessAssetCreatedOrAssetUpdated(): array
    {
        return [
            ['testCase' => 'process__asset_created_or_updated/wrong_customer'],
            ['testCase' => 'process__asset_created_or_updated/asset_archived'],
            ['testCase' => 'process__asset_created_or_updated/simple'],
            ['testCase' => 'process__asset_created_or_updated/no_latest_service_level'],
            ['testCase' => 'process__asset_created_or_updated/exist_latest_service_level__job_due_date_lt_now'],
            ['testCase' => 'process__asset_created_or_updated/exist_latest_service_level__job_due_date_gte_now'],
            ['testCase' => 'process__asset_created_or_updated/empty_test_history'],
            ['testCase' => 'process__asset_created_or_updated/no_latest_service_level__empty_test_history'],
            ['testCase' => 'process__asset_created_or_updated/no_latest_schedule'],
            ['testCase' => 'process__asset_created_or_updated/empty_latest_schedule_blocks'],
            ['testCase' => 'process__asset_created_or_updated/job_tag_no_access_3'],
            ['testCase' => 'process__asset_created_or_updated/job_tag_no_access_2'],
            ['testCase' => 'process__asset_created_or_updated/job_tag_no_access_1'],
            ['testCase' => 'process__asset_created_or_updated/no_no_access_job_tags'],
            ['testCase' => 'process__asset_created_or_updated/empty_site_custom_fields'],
            ['testCase' => 'process__asset_created_or_updated/empty_asset_custom_fields'],
            ['testCase' => 'process__asset_created_or_updated/empty_job_custom_fields'],
            ['testCase' => 'process__asset_created_or_updated/test_history_result_pass'],
            ['testCase' => 'process__asset_created_or_updated/test_history_result_fail'],
            ['testCase' => 'process__asset_created_or_updated/test_history_no_suitable_results'],
        ];
    }

    /**
     * @dataProvider getDataTestProcessAssetCreatedOrAssetUpdated
     * @providedTestCase
     */
    public function testProcessAssetCreatedOrAssetUpdated(): void
    {
        $this->mockHttpRequestService($this->getJsonFixture('requests_chain.json'));

        app(AssetReportGenerator::class)->createOrUpdateAssetReport(11, 111);

        $this->assertChangesEqualsFixture('notify_asset_reports', 'db_changes__notify_asset_reports.json');
        $this->assertChangesEqualsFixture('notify_asset_report_validations', 'db_changes__notify_asset_report_validations.json');
    }
}
