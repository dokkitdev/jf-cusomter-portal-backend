<?php

namespace App\Tests;

use App\Models\Customer;
use App\Models\SimproJob;
use App\Models\User;
use App\Tests\Support\SimproTestTrait;
use Symfony\Component\HttpFoundation\Response;

class CustomerTest extends TestCase
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

    public function testCreateOrUpdateCustomerEvent()
    {
        $this->mockGetCustomer();

        $this->createSimproJob('simpro_webhook_company_customer_created_fixture.json');

        $this->artisan('simpro:handle-jobs')->assertExitCode(0);

        $simproJobs = SimproJob::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_jobs_fixture.json', $simproJobs);

        $customers = Customer::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('customers_create_or_update_event_fixture.json', $customers);
    }

    public function testDeleteCustomerEvent()
    {
        $this->createSimproJob('simpro_webhook_company_customer_deleted_fixture.json');

        $this->artisan('simpro:handle-jobs')->assertExitCode(0);

        $simproJobs = SimproJob::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_jobs_fixture.json', $simproJobs);

        $this->assertDatabaseMissing('customers', ['id' => 3]);
        $this->assertDatabaseMissing('customer_user', ['id' => 3]);
    }

    public function testSyncCustomersCommand()
    {
        $this->mockSyncCustomersCommand();

        $this->artisan('simpro:sync-customers')->assertExitCode(0);

        $customers = Customer::orderBy('id')->get()->toArray();

        $this->assertEqualsFixture('customers_fixture.json', $customers);
    }

    public function testGet()
    {
        $response = $this->actingAs($this->admin)->json('get', '/customers/1');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_customer_fixture.json', $response->json());
    }

    public function testGetNotExists()
    {
        $response = $this->actingAs($this->admin)->json('get', '/customers/0');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testGetNoPermission()
    {
        $response = $this->actingAs($this->user)->json('get', '/customers/1');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testGetNoAuth()
    {
        $response = $this->json('get', '/customers/1');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function getSearchFilters()
    {
        return [
            [
                'filter' => [
                    'all' => 1,
                    'with' => ['users']
                ],
                'result' => 'search_by_all_customers.json'
            ],
            [
                'filter' => [
                    'page' => 1,
                    'per_page' => 2,
                ],
                'result' => 'search_by_page_per_page_customers.json'
            ],
            [
                'filter' => ['query' => 'Simpro Customer'],
                'result' => 'search_customers_by_query.json'
            ]
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
        $response = $this->actingAs($this->customer)->json('get', '/customers', $filter);

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
        $response = $this->actingAs($this->admin)->json('get', '/customers', $filter);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture("admin_{$fixture}", $response->json());
    }
}
