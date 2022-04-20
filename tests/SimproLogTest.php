<?php

namespace App\Tests;

use App\Models\Asset;
use App\Models\Customer;
use App\Models\Job;
use App\Models\JobAttachment;
use App\Models\JobCatalog;
use App\Models\JobWorkOrder;
use App\Models\Schedule;
use App\Models\Site;
use App\Models\SiteContact;
use App\Models\SimproLog;
use App\Models\User;
use App\Tests\Support\SimproTestTrait;

class SimproLogTest extends TestCase
{
    use SimproTestTrait;

    protected $admin;
    protected $user;
    protected $customer;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::find(1);
        $this->user = User::find(2);
        $this->customer = User::find(3);
    }

    public function testGetSitesToLogCommand()
    {
        $this->mockGetSites();

        $this->artisan('simpro:save-simpro-log sites')->assertExitCode(0);

        $simproLog = SimproLog::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_log_sites_fixture.json', $simproLog);
    }

    public function testHandleSitesLogCommand()
    {
        $this->mockCreateOrUpdateSite();

        $this->artisan('simpro:handle-log sites')->assertExitCode(0);

        $simproLog = SimproLog::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_log_sites_create_or_update_event_fixture.json', $simproLog);

        $sites = Site::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('site_create_or_update_event_fixture.json', $sites);

        $siteContacts = SiteContact::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('site_contacts_create_or_update_event_fixture.json', $siteContacts);
     }

    public function testGetJobsToLogCommand()
    {
        $this->mockGetJobs();

        $this->artisan('simpro:save-simpro-log jobs')->assertExitCode(0);

        $simproLogs = SimproLog::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_log_jobs_fixture.json', $simproLogs);
    }

    public function testHandleJobLogCommand()
    {
        $this->mockUpdateJobMadeSafe();

        $this->artisan('simpro:handle-log jobs')->assertExitCode(0);

        $jobLogs = SimproLog::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_log_jobs_create_or_update_event_fixture.json', $jobLogs);

        $job = Job::orderBy('id')->get()->toArray();
        $this->exportJson('job_update_made_safe_fixture.json', $job);
    }

//    public function testHandleJobLogCommand()
//    {
//        $this->mockCreateOrUpdateJob();
//
//        $this->artisan('simpro:handle-log jobs')->assertExitCode(0);
//
//        $jobLogs = SimproLog::orderBy('id')->get()->toArray();
//        $this->assertEqualsFixture('simpro_log_jobs_create_or_update_event_fixture.json', $jobLogs);
//
//        $job = Job::orderBy('id')->get()->toArray();
//        $this->assertEqualsFixture('job_create_or_update_event_fixture.json', $job);
//
//        $simproCustomer = Customer::orderBy('id')->get()->toArray();
//        $this->assertEqualsFixture('customers_create_or_update_event_fixture.json', $simproCustomer);
//
//        $simproSite = Site::orderBy('id')->with(['site_contacts'])->get()->toArray();
//        $this->assertEqualsFixture('sites_create_or_update_event_fixture.json', $simproSite);
//
//        $schedules = Schedule::orderBy('id')->get()->toArray();
//        $this->assertEqualsFixture('schedules_create_or_update_event_fixture.json', $schedules);
//
//        $jobCatalogs = JobCatalog::orderBy('id')->get()->toArray();
//        $this->assertEqualsFixture('job_catalogs_create_or_update_event_fixture.json', $jobCatalogs);
//
//        $jobAttachments = JobAttachment::orderBy('id')->get()->toArray();
//        $this->assertEqualsFixture('job_attachments_create_or_update_event_fixture.json', $jobAttachments);
//
//        $jobWorkOrders = JobWorkOrder::orderBy('id')->get()->toArray();
//        $this->assertEqualsFixture('job_work_orders_create_or_update_event_fixture.json', $jobWorkOrders);
//    }

    public function testGetAssetsToLogCommand()
    {
        $this->mockGetAssets();

        $this->artisan('simpro:save-simpro-log assets')->assertExitCode(0);

        $assetLogs = SimproLog::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_log_assets_fixture.json', $assetLogs);

        $this->assertDatabaseHas('asset_log_histories', [
            'assets_pulled_at' => '2018-11-11 10:41:11.000000',
            'assets_count' => 250
        ]);
    }

    public function testHandleAssetsLogCommand()
    {
        $this->mockHandleAssetLog();

        $this->artisan('simpro:handle-log assets')->assertExitCode(0);

        $assetlogs = SimproLog::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_log_assets_create_or_update_event_fixture.json', $assetlogs);

        $assets = Asset::orderBy('id')->with([
            'site.customers',
            'asset_custom_fields',
            'asset_attachments',
            'asset_test_records.job',
            'asset_test_records.asset_test_record_readings'
        ])->get()->toArray();
        $this->assertEqualsFixture('asset_create_or_update_event_fixture.json', $assets);
    }
}
