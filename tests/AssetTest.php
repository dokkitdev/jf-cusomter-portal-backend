<?php

namespace App\Tests;

use App\Models\Asset;
use App\Models\AssetAttachment;
use App\Models\AssetCustomField;
use App\Models\AssetTestRecord;
use App\Models\Customer;
use App\Models\SimproJob;
use App\Models\Site;
use App\Models\User;
use App\Tests\Support\CreateSimproJobTrait;
use App\Tests\Support\MockHttpRequestServiceTrait;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class AssetTest extends TestCase
{
    use CreateSimproJobTrait;
    use MockHttpRequestServiceTrait;

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

    //referenced by: SyncArchivedAssetsTest::testProcessAsset()
    public function testUpdateAssetEvent()
    {
        $this->mockHttpRequestService($this->getJsonFixture('simpro_webhook_asset_updated_requests_chain.json'));

        $this->createSimproJob('simpro_webhook_asset_updated_fixture.json');

        $this->artisan('simpro:handle-jobs')->assertExitCode(0);

        $simproJob = SimproJob::orderBy('id')->where('handle_status', '!=', SimproJob::HANDLE_STATUS_COMPLETED)->get()->toArray();
        $this->assertEqualsFixture('simpro_jobs_fixture.json', $simproJob);

        $assets = Asset::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('asset_create_or_update_event_fixture.json', $assets);

        $simproCustomer = Customer::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_customer_create_or_update_event_fixture.json', $simproCustomer);

        $simproSites = Site::orderBy('id')->with(['site_contacts'])->get()->toArray();
        $this->assertEqualsFixture('simpro_site_create_or_update_event_fixture.json', $simproSites);

        $assetCustomFields = AssetCustomField::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('asset_custom_fields_create_or_update_event_fixture.json', $assetCustomFields);

        $assetAttachments = AssetAttachment::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('asset_attachments_create_or_update_event_fixture.json', $assetAttachments);

        $assetTestRecords = AssetTestRecord::with(['asset_test_record_readings'])->orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('asset_test_records_create_or_update_event_fixture.json', $assetTestRecords);
    }

    public function testDeleteAssetEvent()
    {
        $this->createSimproJob('simpro_webhook_asset_deleted_fixture.json');

        $this->artisan('simpro:handle-jobs')->assertExitCode(0);

        $simproJob = SimproJob::orderBy('id')->where('handle_status', '!=', SimproJob::HANDLE_STATUS_COMPLETED)->get()->toArray();
        $this->assertEqualsFixture('simpro_jobs_fixture.json', $simproJob);

        $assets = Asset::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('asset_delete_event_fixture.json', $assets);
    }

    public function testGet()
    {
        $response = $this->actingAs($this->customer)->json('get', '/assets/1');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_asset_fixture.json', $response->json());
    }

    public function testGetNoPermission()
    {
        $response = $this->actingAs($this->customer)->json('get', '/assets/7');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testGetByAdmin()
    {
        $response = $this->actingAs($this->admin)->json('get', '/assets/7');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_asset_by_admin_fixture.json', $response->json());
    }

    public function testGetNotExists()
    {
        $response = $this->actingAs($this->admin)->json('get', '/assets/0');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testGetNoAuth()
    {
        $response = $this->json('get', '/assets/1');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function getSearchFilters()
    {
        return [
            [
                'filter' => ['all' => 1],
                'result' => 'search_by_all_assets.json'
            ],
            [
                'filter' => [
                    'page' => 1,
                    'per_page' => 2,
                ],
                'result' => 'search_by_page_per_page_assets.json'
            ],
            [
                'filter' => [
                    'order_by' => 'site_id',
                    'desc' => false,
                    'query' => 'Name',
                    'archived' => false,
                    'customer_id' => 1,
                    'site_id' => 1,
                    'last_test_date' => '2016-10-20',
                    'next_service_date' => '2016-10-20',
                    'last_test_result_query' => 'Test result',
                    'service_level_names' => ['Service level...'],
                    'simpro_asset_id' => 1,
                    'site_name_query' => 'Name 1',
                    'site_uprn_query' => 'uprn 1'
                ],
                'result' => 'search_assets_complex.json'
            ],
            [
                'filter' => [
                    'order_by' => 'job_customer.name',
                    'desc' => true,
                    'report' => true,
                    'job_stage' => ['Progress', 'Complete'],
                    'cp12_status' => 'On Time',
                    'job_logged_completion_date_from' => '2022-02-22 11:11:11',
                    'job_logged_completion_date_to' => '2022-02-22 11:11:11',
                    'with' => [
                        'site.primary_site_contact',
                        'asset_test_record',
                        'job',
                        'job_customer',
                        'next_schedule'
                    ]
                ],
                'result' => 'search_assets_report.json'
            ],
        ];
    }

    /**
     * @dataProvider  getSearchFilters
     */
    public function testSearch($filter, $fixture)
    {
        $response = $this->actingAs($this->customer)->json('get', '/assets', $filter);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture($fixture, $response->json());
    }

    /**
     * @testCase search_assets_report__check_cp12_status
     */
    public function testSearchCheckCp12Status()
    {
        $response = $this->actingAs($this->customer)->json('get', '/assets', [
            'customer_id' => 4,
            'report' => true,
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('response.json', $response->json());
    }

    /**
     * @dataProvider  getSearchFilters
     *
     * @param  array $filter
     * @param  string $fixture
     */
    public function testSearchByAdmin($filter, $fixture)
    {
        $response = $this->actingAs($this->admin)->json('get', '/assets', $filter);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture("admin_{$fixture}", $response->json());
    }

    public function testExport()
    {
        $response = $this->actingAs($this->customer)->json('get', '/assets/export', [
            'all' => 1,
            'with' => ['site', 'site.customer'],
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertHeader('Content-Disposition', 'attachment; filename=assets.csv');
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $this->assertEqualsTextFixture('export__general__file.csv', $response->streamedContent());
    }

    public function testExportAsAdmin()
    {
        $response = $this->actingAs($this->admin)->json('get', '/assets/export', [
            'all' => 1,
            'with' => ['site', 'site.customer'],
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertHeader('Content-Disposition', 'attachment; filename=assets.csv');
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $this->assertEqualsTextFixture('export__as_admin__file.csv', $response->streamedContent());
    }

    public function testExportNoAuth()
    {
        $response = $this->json('get', '/assets/export', [
            'all' => 1,
            'with' => ['site'],
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testReportExport()
    {
        $response = $this->actingAs($this->customer)->json('get', '/assets/report/export', [
            'all' => 1,
            'order_by' => 'last_cp12_date',
            'desc' => true,
            'report' => true,
            'with' => [
                'site.primary_site_contact',
                'asset_test_record',
                'job',
                'job_customer',
                'next_schedule',
                'job.job_no_access_dates',
            ],
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertHeader('Content-Disposition', 'attachment; filename=assets_report.csv');
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $this->assertEqualsTextFixture('report_export__general__file.csv', $response->streamedContent());
    }

    public function testReportExportAsAdmin()
    {
        $response = $this->actingAs($this->admin)->json('get', '/assets/report/export', [
            'all' => 1,
            'order_by' => 'last_cp12_date',
            'desc' => true,
            'report' => true,
            'with' => [
                'site.primary_site_contact',
                'asset_test_record',
                'job',
                'job_customer',
                'next_schedule',
                'job.job_no_access_dates',
            ],
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertHeader('Content-Disposition', 'attachment; filename=assets_report.csv');
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $this->assertEqualsTextFixture('report_export__as_admin__file.csv', $response->streamedContent());
    }

    public function testReportExportNoAuth()
    {
        $response = $this->json('get', '/assets/report/export', [
            'all' => 1,
            'with' => ['site'],
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testDownloadAssetAttachment()
    {
        Storage::put('link1', 'content');

        $response = $this->actingAs($this->customer)->json('get', '/asset-attachments/1/download');

        $response->assertStatus(Response::HTTP_OK);

        Storage::delete('link1');
    }

    public function testDownloadAssetAttachmentNotExists()
    {
        $response = $this->actingAs($this->admin)->json('get', '/asset-attachments/0/download');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testDownloadAssetAttachmentNoAuth()
    {
        $response = $this->json('get', '/asset-attachments/1/download');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testGetAssetServiceLevels()
    {
        $this->mockHttpRequestService($this->getJsonFixture('get_asset_service_levels_requests_chain.json'));

        $response = $this->actingAs($this->customer)->json('get', '/assets/service-levels');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_asset_service_levels_fixture.json', $response->json());
    }

    public function testGetAssetServiceLevelsNoAuth()
    {
        $response = $this->json('get', '/assets/service-levels');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testGetAssetTypes()
    {
        $this->mockHttpRequestService($this->getJsonFixture('get_asset_types_requests_chain.json'));

        $response = $this->actingAs($this->customer)->json('get', '/assets/types');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_asset_types_fixture.json', $response->json());
    }

    public function testGetAssetTypesNoAuth()
    {
        $response = $this->json('get', '/assets/types');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testGetAssetNames()
    {
        $this->mockHttpRequestService($this->getJsonFixture('get_asset_names_requests_chain.json'));

        $response = $this->actingAs($this->customer)->json('get', '/assets/names');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_asset_names_fixture.json', $response->json());
    }

    public function testGetAssetNamesNoAuth()
    {
        $response = $this->json('get', '/assets/names');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUpdateAssetsCommand()
    {
        $this->artisan('simpro:update-assets')->assertExitCode(0);

        $assets = Asset::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('update_assets_command_fixture.json', $assets);
    }

    public function testUpdateAssetsDateCommand()
    {
        $this->artisan('simpro:update-assets-date')->assertExitCode(0);

        $assets = Asset::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('update_assets_date_command_fixture.json', $assets);
    }
}
