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

    public function getTestJobAssetTestedCheckLastTestDateValueData(): array
    {
        return [
            ['test_case' => 'job_asset_tested__check_last_test_date_value__last_test_completed'],
            ['test_case' => 'job_asset_tested__check_last_test_date_value__test_history_from_simpro_not_properly_ordered'],
            ['test_case' => 'job_asset_tested__check_last_test_date_value__no_last_test__cp12_date_exists'],
            ['test_case' => 'job_asset_tested__check_last_test_date_value__no_last_test_and_cp12__exists_date_in_asset'],
            ['test_case' => 'job_asset_tested__check_last_test_date_value__no_any_test_date'],
        ];
    }

    /**
     * @dataProvider getTestJobAssetTestedCheckLastTestDateValueData
     * @providedTestCase
     */
    public function testJobAssetTestedCheckLastTestDateValue(): void
    {
        $this->mockHttpRequestService($this->getJsonFixture('requests_chain.json'));

        $this->artisan('simpro:handle-jobs job.asset.tested 5 4')->assertExitCode(0);

        $this->assertChangesEqualsFixture('simpro_jobs');
        $this->assertChangesEqualsFixture('asset_test_records');
        $this->assertChangesEqualsFixture('asset_test_record_readings');
        $this->assertChangesEqualsFixture('assets');
    }
}
