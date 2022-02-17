<?php

namespace App\Tests;

use App\Models\SimproJob;
use App\Models\Site;
use App\Models\SiteContact;
use App\Models\User;
use App\Tests\Support\SimproTestTrait;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class SiteTest extends TestCase
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

    public function testUpdateSiteEvent()
    {
        $this->mockCreateOrUpdateSite();

        $this->createSimproJob('simpro_webhook_site_updated_fixture.json');

        $this->artisan('simpro:handle-jobs')->assertExitCode(0);

        $simproJob = SimproJob::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_jobs_fixture.json', $simproJob);

        $site = Site::with(['customers'])->orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('site_create_or_update_event_fixture.json', $site);

        $siteContacts = SiteContact::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('site_contacts_create_or_update_event_fixture.json', $siteContacts);
    }

    public function testDeleteSiteEvent()
    {
        $this->createSimproJob('simpro_webhook_site_deleted_fixture.json');

        $this->artisan('simpro:handle-jobs')->assertExitCode(0);

        $simproJobs = SimproJob::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_jobs_fixture.json', $simproJobs);

        $this->assertDatabaseMissing('sites', ['id' => 1]);

        $this->assertDatabaseMissing('site_contacts', ['site_id' => 1]);
    }

    public function testGet()
    {
        $response = $this->actingAs($this->customer)->json('get', '/sites/1', [
            'with' => ['site_contacts']
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_site_fixture.json', $response->json());
    }

    public function testGetNoPermission()
    {
        $response = $this->actingAs($this->customer)->json('get', '/sites/5');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testGetByAdmin()
    {
        $response = $this->actingAs($this->admin)->json('get', '/sites/5');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_site_by_admin_fixture.json', $response->json());
    }

    public function testGetNotExists()
    {
        $response = $this->actingAs($this->admin)->json('get', '/sites/0');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testGetNoAuth()
    {
        $response = $this->json('get', '/sites/1');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function getSearchFilters()
    {
        return [
            [
                'filter' => ['all' => 1],
                'result' => 'search_by_all_sites.json'
            ],
            [
                'filter' => [
                    'page' => 1,
                    'per_page' => 2,
                ],
                'result' => 'search_by_page_per_page_sites.json'
            ],
            [
                'filter' => ['query' => 'Charter'],
                'result' => 'search_sites_by_query.json'
            ],
            [
                'filter' => [
                    'simpro_site_id' => 1,
                    'name_query' => 'name',
                    'order_by' => 'primary_site_contact.name'
                ],
                'result' => 'search_sites_by_complex.json'
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
        $response = $this->actingAs($this->customer)->json('get', '/sites', $filter);

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
        $response = $this->actingAs($this->admin)->json('get', '/sites', $filter);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture("admin_$fixture", $response->json());
    }

    public function testExport()
    {
        Excel::fake();

        $response = $this->actingAs($this->customer)->json('get', '/sites/export', [
            'with' => ['customer', 'primary_site_contact'],
            'with_count' => ['open_jobs']
        ]);

        $response->assertStatus(Response::HTTP_OK);

        Excel::assertDownloaded('sites.csv');
    }

    public function testExportAsAdmin()
    {
        Excel::fake();

        $response = $this->actingAs($this->admin)->json('get', '/sites/export', [
            'with' => ['customer', 'primary_site_contact'],
            'with_count' => ['open_jobs']
        ]);

        $response->assertStatus(Response::HTTP_OK);

        Excel::assertDownloaded('sites.csv');
    }

    public function testExportNoAuth()
    {
        $response = $this->json('get', '/sites/export', [
            'with' => ['customer', 'primary_site_contact'],
            'with_count' => ['open_jobs']
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUpdate()
    {
        $this->mockUpdateSite();

        $data = $this->getJsonFixture('update_site.json');

        $response = $this->actingAs($this->user)->json('put', '/sites/1', $data);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas('sites', Arr::except($data, ['primary_site_contact_id']));

        $siteContacts = SiteContact::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('update_primary_site_contact_fixture.json', $siteContacts);
    }

    public function testUpdateNoPermission()
    {
        $data = $this->getJsonFixture('update_site.json');

        $response = $this->actingAs($this->customer)->json('put', '/sites/5', $data);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testUpdatePrimarySiteContactNotExists()
    {
        $data = $this->getJsonFixture('update_site.json');

        $data['primary_site_contact_id'] = 2;

        $response = $this->actingAs($this->customer)->json('put', '/sites/1', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateByAdmin()
    {
        $this->mockUpdateSiteByAdmin();

        $data = $this->getJsonFixture('update_site.json');

        $data['primary_site_contact_id'] = 4;

        $response = $this->actingAs($this->admin)->json('put', '/sites/4', $data);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas('sites', Arr::except($data, ['primary_site_contact_id']));

        $siteContacts = SiteContact::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('update_primary_site_contact_by_admin_fixture.json', $siteContacts);
    }

    public function testUpdateNotExists()
    {
        $data = $this->getJsonFixture('update_site.json');

        $response = $this->actingAs($this->admin)->json('put', '/sites/0', $data);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testUpdateNoAuth()
    {
        $data = $this->getJsonFixture('update_site.json');

        $response = $this->json('put', '/sites/1', $data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
