<?php

namespace App\Tests;

use App\Tests\Support\MockHttpRequestServiceTrait;

class AssetWebhooksTest extends TestCase
{
    use MockHttpRequestServiceTrait;

    protected array $requiredOriginStates = [
        'simpro_jobs',
        'assets',
        'asset_test_records',
        'asset_test_record_readings',
    ];

    /** @testCase job_asset_tested__no_test_histories */
    public function testJobAssetTestedNoTestHistories()
    {
        $this->mockHttpRequestService($this->getJsonFixture('requests_chain.json'));

        $this->artisan('simpro:handle-jobs job.asset.tested 5 4')->assertExitCode(0);

        $this->assertChangesEqualsFixture('simpro_jobs');
        $this->assertChangesEqualsFixture('asset_test_records');
        $this->assertChangesEqualsFixture('asset_test_record_readings');
        $this->assertChangesEqualsFixture('assets');
    }

    /** @testCase job_asset_tested__no_test_record_readings */
    public function testJobAssetTestedNoTestRecordReadings()
    {
        $this->mockHttpRequestService($this->getJsonFixture('requests_chain.json'));

        $this->artisan('simpro:handle-jobs job.asset.tested 5 4')->assertExitCode(0);

        $this->assertChangesEqualsFixture('simpro_jobs');
        $this->assertChangesEqualsFixture('asset_test_records');
        $this->assertChangesEqualsFixture('asset_test_record_readings');
        $this->assertChangesEqualsFixture('assets');
    }

    /** @testCase job_asset_tested__general */
    public function testJobAssetTestedGeneral()
    {
        $this->mockHttpRequestService($this->getJsonFixture('requests_chain.json'));

        $this->artisan('simpro:handle-jobs job.asset.tested 5 4')->assertExitCode(0);

        $this->assertChangesEqualsFixture('simpro_jobs');
        $this->assertChangesEqualsFixture('asset_test_records');
        $this->assertChangesEqualsFixture('asset_test_record_readings');
        $this->assertChangesEqualsFixture('assets');
    }
}
