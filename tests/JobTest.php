<?php

namespace App\Tests;

use App\Models\Job;
use App\Models\JobAttachment;
use App\Models\JobCatalog;
use App\Models\JobWorkOrder;
use App\Models\Schedule;
use App\Models\Customer;
use App\Models\SimproJob;
use App\Models\Site;
use App\Models\User;
use App\Tests\Support\SimproTestTrait;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class JobTest extends TestCase
{
    use SimproTestTrait;

    protected $admin;
    protected $user;
    protected $customer;
    protected $files;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::find(1);
        $this->user = User::find(2);
        $this->customer = User::find(3);
        $this->files = [
            UploadedFile::fake()->image('file1.png', 600, 600),
            UploadedFile::fake()->image('file2.png', 600, 600)
        ];
    }

    public function testCreateJobEvent()
    {
        $this->mockCreateOrUpdateJob();

        $this->createSimproJob('simpro_webhook_job_created_fixture.json');

        $this->artisan('simpro:handle-jobs')->assertExitCode(0);

        $simproJob = SimproJob::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_jobs_fixture.json', $simproJob);

        $job = Job::with(['job_no_access_dates'])->orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('job_create_or_update_event_fixture.json', $job);

        $customer = Customer::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('customers_create_or_update_event_fixture.json', $customer);

        $site = Site::orderBy('id')->with(['site_contacts'])->get()->toArray();
        $this->assertEqualsFixture('sites_create_or_update_event_fixture.json', $site);

        $schedules = Schedule::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('schedules_create_or_update_event_fixture.json', $schedules);

        $jobCatalogs = JobCatalog::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('job_catalogs_create_or_update_event_fixture.json', $jobCatalogs);

        $jobAttachments = JobAttachment::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('job_attachments_create_or_update_event_fixture.json', $jobAttachments);

        $jobWorkOrders = JobWorkOrder::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('job_work_orders_create_or_update_event_fixture.json', $jobWorkOrders);
    }

    public function testDeleteJobEvent()
    {
        $this->createSimproJob('simpro_webhook_job_deleted_fixture.json');

        $this->artisan('simpro:handle-jobs')->assertExitCode(0);

        $simproJobs = SimproJob::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_jobs_fixture.json', $simproJobs);

        $this->assertDatabaseMissing('jobs', ['id' => 1]);

        $this->assertDatabaseMissing('schedules', ['job_id' => 1]);

        $this->assertDatabaseMissing('job_catalogs', ['job_id' => 1]);

        $this->assertDatabaseMissing('job_attachments', ['job_id' => 1]);

        $this->assertDatabaseMissing('job_work_orders', ['job_id' => 1]);
    }

    public function testGet()
    {
        $response = $this->actingAs($this->customer)->json('get', '/jobs/1');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_job_fixture.json', $response->json());
    }

    public function testGetNoPermission()
    {
        $response = $this->actingAs($this->customer)->json('get', '/jobs/9');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testGetByAdmin()
    {
        $response = $this->actingAs($this->admin)->json('get', '/jobs/9');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_job_by_admin_fixture.json', $response->json());
    }

    public function testGetNotExists()
    {
        $response = $this->actingAs($this->admin)->json('get', '/jobs/0');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testGetNoAuth()
    {
        $response = $this->json('get', '/jobs/1');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function getSearchFilters()
    {
        return [
            [
                'filter' => ['all' => 1],
                'result' => 'search_by_all_jobs.json'
            ],
            [
                'filter' => [
                    'page' => 1,
                    'per_page' => 2,
                ],
                'result' => 'search_by_page_per_page_jobs.json'
            ],
            [
                'filter' => [
                    'query' => 'Name 1',
                ],
                'result' => 'search_by_query_jobs.json'
            ],
            [
                'filter' => [
                    'simpro_job_id' => 1,
                    'site_name' => 'Name 1',
                    'stage' => ['Progress'],
                ],
                'result' => 'search_by_complex_jobs.json'
            ],
            [
                'filter' => [
                    'postal_code' => 'UB8 1JG',
                ],
                'result' => 'search_by_postal_code_jobs.json'
            ],
            [
                'filter' => [
                    'priority' => ['Fire Alarm - Standard', 'Intruder Alarm - Standard'],
                ],
                'result' => 'search_by_priority_jobs.json'
            ],
            [
                'filter' => [
                    'out_of_hours' => true,
                ],
                'result' => 'search_by_out_of_hours_jobs.json'
            ],
        ];
    }

    /**
     * @dataProvider  getSearchFilters
     *
     * @param  array $filter
     * @param  string $fixture
     */
    public function testSearch($filter, $fixture)
    {
        $response = $this->actingAs($this->customer)->json('get', '/jobs', $filter);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture($fixture, $response->json());
    }

    /**
     * @dataProvider  getSearchFilters
     *
     * @param  array $filter
     * @param  string $fixture
     */
    public function testSearchByAdmin($filter, $fixture)
    {
        $response = $this->actingAs($this->admin)->json('get', '/jobs', $filter);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture("admin_{$fixture}", $response->json());
    }

    public function testExport()
    {
        Excel::fake();

        $response = $this->actingAs($this->customer)->json('get', '/jobs/export', [
            'all' => 1,
            'with' => ['recent_schedule', 'customer', 'site'],
            'with_count' => ['job_attachments']
        ]);

        $response->assertStatus(Response::HTTP_OK);

        Excel::assertDownloaded('jobs.csv');
    }

    public function testExportAsAdmin()
    {
        Excel::fake();

        $response = $this->actingAs($this->admin)->json('get', '/jobs/export', [
            'all' => 1,
            'with' => ['recent_schedule', 'customer', 'site'],
            'with_count' => ['job_attachments']
        ]);

        $response->assertStatus(Response::HTTP_OK);

        Excel::assertDownloaded('jobs.csv');
    }

    public function testExportNoAuth()
    {
        $response = $this->json('get', '/jobs/export', [
            'all' => 1,
            'with' => ['recent_schedule', 'customer', 'site'],
            'with_count' => ['job_attachments']
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testReportExport()
    {
        Excel::fake();

        $response = $this->actingAs($this->customer)->json('get', '/jobs/report/export', [
            'all' => 1,
            'with' => ['site'],
        ]);

        $response->assertStatus(Response::HTTP_OK);

        Excel::assertDownloaded('jobs_report.csv');
    }

    public function testReportExportAsAdmin()
    {
        Excel::fake();

        $response = $this->actingAs($this->admin)->json('get', '/jobs/report/export', [
            'all' => 1,
            'with' => ['site'],
        ]);

        $response->assertStatus(Response::HTTP_OK);

        Excel::assertDownloaded('jobs_report.csv');
    }

    public function testReportExportNoAuth()
    {
        $response = $this->json('get', '/jobs/report/export', [
            'all' => 1,
            'with' => ['recent_schedule', 'customer', 'site'],
            'with_count' => ['job_attachments']
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testGetCostCenters()
    {
        $this->mockGetCostCenters();

        $response = $this->actingAs($this->customer)->json('get', '/jobs/cost-centers');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_cost_centers_fixture.json', $response->json());
    }

    public function testGetCostCentersNoAuth()
    {
        $response = $this->json('get', '/jobs/cost-centers');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testGetStatuses()
    {
        $this->mockGetCostCenters();

        $response = $this->actingAs($this->customer)->json('get', '/jobs/statuses');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_statuses_fixture.json', $response->json());
    }

    public function testGetStatusesNoAuth()
    {
        $response = $this->json('get', '/jobs/statuses');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testCreateRequest()
    {
        $this->mockCreatejobRequest();

        $response = $this->actingAs($this->customer)->json('post', '/jobs/create-in-simpro', [
            'site_id' => 1,
            'description' => 'Test job...',
            'files' => $this->files
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testCreateRequestByAdmin()
    {
        $this->mockCreatejobRequest();

        $response = $this->actingAs($this->admin)->json('post', '/jobs/create-in-simpro', [
            'site_id' => 2,
            'description' => 'Test job...',
            'files' => $this->files
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testCreateRequestNoPermissions()
    {
        $response = $this->actingAs($this->customer)->json('post', '/jobs/create-in-simpro', [
            'site_id' => 3,
            'description' => 'Test job...',
            'files' => $this->files
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testCreateRequestSiteNotExists()
    {
        $response = $this->actingAs($this->admin)->json('post', '/jobs/create-in-simpro', [
            'site_id' => 0,
            'description' => 'Test job...',
            'files' => $this->files
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testCreateRequestCustomerNotExists()
    {
        $response = $this->actingAs($this->admin)->json('post', '/jobs/create-in-simpro', [
            'site_id' => 5,
            'description' => 'Test job...',
            'files' => $this->files
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateRequestNoAuth()
    {
        $response = $this->json('post', '/jobs/create-in-simpro', [
            'site_id' => 1,
            'description' => 'Test job...',
            'files' => $this->files
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
