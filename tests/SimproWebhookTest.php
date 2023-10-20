<?php

namespace App\Tests;

use App\Models\SimproJob;
use App\Tests\Support\SimproWebhookTestTrait;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

class SimproWebhookTest extends TestCase
{
    use SimproWebhookTestTrait;

    protected static Collection $originSimproJobsState;

    public function setUp(): void
    {
        parent::setUp();

        self::$originSimproJobsState = self::$originSimproJobsState ?? $this->getDataSet('simpro_jobs');
    }

    public function testWebhookSuccess()
    {
        $this->mockRequestVerification(true);

        $response = $this->json('post', '/simpro-webhook', [
            'ID' => 'test.updated',
            'build' => 'pfsgroup.simprosuite.com',
            'description' => 'Test #1 has been updated.',
            'name' => 'Test',
            'action' => 'updated',
            'reference' => [
                'testID' => 1
            ],
            'date_triggered' => '2019-12-18T11:52:29+00:00'
        ], [
            'X-Response-Signature' => 'f728460348453437dd9b70a16567a45ba15fe141'
        ]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $webhooks = SimproJob::orderBy('id')->get()->toArray();

        $this->assertEqualsFixture('test_webhook_success.json', $webhooks);
    }

    public function testWebhookIsNotVerify()
    {
        $this->mockRequestVerification(false);

        $response = $this->json('post', '/simpro-webhook', [
            'ID' => 'test.updated',
            'build' => 'pfsgroup.simprosuite.com',
            'description' => 'Test #1 has been updated.',
            'name' => 'Test',
            'action' => 'updated',
            'reference' => [
                'testID' => 1
            ],
            'date_triggered' => '2019-12-18T11:52:29+00:00'
        ], [
            'X-Response-Signature' => ''
        ]);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testCreateWebhookSuccess()
    {
        $this->mockRequestVerification(true);

        $response = $this->json('post', '/simpro-webhook', [
            'ID' => 'job.created',
            'build' => 'pfsgroup.simprosuite.com',
            'description' => 'Job #2406 has been created.',
            'name' => 'Job',
            'action' => 'created',
            'reference' => [
                'companyID' => 0,
                'jobID' => 2406
            ],
            'date_triggered' => '2019-12-18T12:35:47+00:00'
        ], [
            'X-Response-Signature' => '28770c3cc7ffaf1adda20d8aa650c0e6738fcceb'
        ]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $webhooks = SimproJob::orderBy('id')->get()->toArray();

        $this->assertEqualsFixture('create_webhook_success.json', $webhooks);
    }

    /**
     * @testCase job_asset_tested_webhook
     */
    public function testJobAssetTestedWebhook()
    {
        $this->mockRequestVerification(true);

        $requestData = $this->getJsonFixture('request.json');

        $response = $this->json('post', '/simpro-webhook', $requestData);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertChangesEqualsFixture('simpro_jobs', 'simpro_jobs__state.json', self::$originSimproJobsState);
    }

    public function testDeleteErrorJobs()
    {
        $this->artisan('simpro:delete-error-jobs')->assertExitCode(0);

        $jobs = SimproJob::orderBy('id')->get()->toArray();

        $this->assertEqualsFixture('delete_error_jobs.json', $jobs);
    }
}
