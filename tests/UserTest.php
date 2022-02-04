<?php

namespace App\Tests;

use App\Mails\InvitationMail;
use App\Models\User;
use App\Tests\Support\AuthTestTrait;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class UserTest extends TestCase
{
    use AuthTestTrait;

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

    public function testCreate()
    {
        $this->mockUniqueTokenGeneration('some_token');

        $data = $this->getJsonFixture('create_user.json');

        $response = $this->actingAs($this->admin)->json('post', '/users', $data);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('user_created.json', $response->json());

        $this->assertDatabaseHas('users', $this->getJsonFixture('user_created_database.json'));

        $this->assertDatabaseHas('customer_user', ['user_id' => 4, 'customer_id' => 1]);
        $this->assertDatabaseHas('customer_user', ['user_id' => 4, 'customer_id' => 2]);

        $this->assertMailEquals(InvitationMail::class, [
            [
                'emails' => $data['email'],
                'fixture' => 'invitation_email.html'
            ]
        ]);
    }

    public function testCreateNoAuth()
    {
        $data = $this->getJsonFixture('create_user.json');

        $response = $this->json('post', '/users', $data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertDatabaseMissing('users', Arr::except($data, ['password', 'is_send_email', 'customer_ids']));
    }

    public function testCreateNoPermission()
    {
        $data = $this->getJsonFixture('create_user.json');

        $response = $this->actingAs($this->user)->json('post', '/users', $data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing('users', Arr::except($data, ['password', 'is_send_email', 'customer_ids']));
    }

    public function testCreateUserExists()
    {
        $response = $this->actingAs($this->admin)->json('post', '/users', $this->user->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdate()
    {
        $data = $this->getJsonFixture('update_user.json');

        $response = $this->actingAs($this->admin)->json('put', '/users/3', $data);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas('users', Arr::except($data, 'customer_ids'));

        $this->assertDatabaseMissing('customer_user', ['user_id' => 3, 'customer_id' => 1]);
        $this->assertDatabaseHas('customer_user', ['user_id' => 3, 'customer_id' => 2]);
        $this->assertDatabaseHas('customer_user', ['user_id' => 3, 'customer_id' => 3]);
    }

    public function testUpdateNoPermission()
    {
        $data = $this->getJsonFixture('update_user.json');

        $response = $this->actingAs($this->user)->json('put', '/users/1', $data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'name' => $data['name'],
            'email' => $data['email']
        ]);
    }

    public function testUpdateWithEmailOfAnotherUser()
    {
        $response = $this->actingAs($this->admin)->json('put', '/users/2', [
            'email' => 'admin@example.com'
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertDatabaseMissing('users', [
            'id' => 2,
            'email' => 'admin@example.com'
        ]);
    }

    public function testUpdateNotExists()
    {
        $data = $this->getJsonFixture('update_user.json');

        $response = $this->actingAs($this->admin)->json('put', '/users/0', $data);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testUpdateNoAuth()
    {
        $data = $this->getJsonFixture('update_user.json');

        $response = $this->json('put', '/users/1', $data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertDatabaseMissing('users', Arr::except($data, 'customer_ids'));
    }

    public function testUpdateProfile()
    {
        $data = $this->getJsonFixture('update_user.json');

        $response = $this->actingAs($this->user)->json('put', '/profile', $data);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas('users', Arr::except($data, ['customer_ids', 'role_id']));
    }

    public function testUpdateProfileWithPassword()
    {
        $data = $this->getJsonFixture('update_profile_with_password.json');

        $response = $this->actingAs($this->user)->json('put', '/profile', $data);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testUpdateProfileWithPasswordEmptyOldPassword()
    {
        $data = $this->getJsonFixture('update_profile_with_password_without_old.json');

        $response = $this->actingAs($this->user)->json('put', '/profile', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateProfileWithPasswordWrongOldPassword()
    {
        $data = $this->getJsonFixture('update_profile_with_password_with_wrong_old.json');

        $response = $this->actingAs($this->user)->json('put', '/profile', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateProfileNoAuth()
    {
        $data = $this->getJsonFixture('update_user.json');

        $response = $this->json('put', '/profile', $data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertDatabaseMissing('users', Arr::except($data, 'customer_ids'));
    }

    public function testDelete()
    {
        $response = $this->actingAs($this->admin)->json('delete', '/users/1');

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('users', [
            'id' => 1
        ]);
    }

    public function testDeleteNotExists()
    {
        $response = $this->actingAs($this->admin)->json('delete', '/users/0');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testDeleteNoAuth()
    {
        $response = $this->json('delete', '/users/1');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertDatabaseHas('users', [
            'id' => 1
        ]);
    }

    public function testGetProfile()
    {
        $response = $this->actingAs($this->admin)->json('get', '/profile');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_user.json', $response->json());
    }

    public function testGet()
    {
        $response = $this->actingAs($this->admin)->json('get', '/users/1');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_user.json', $response->json());
    }

    public function testGetNotExists()
    {
        $response = $this->actingAs($this->admin)->json('get', '/users/0');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function getSearchFilters()
    {
        return [
            [
                'filter' => ['all' => 1],
                'result' => 'search_by_all_user.json'
            ],
            [
                'filter' => [
                    'page' => 1,
                    'per_page' => 2,
                ],
                'result' => 'search_by_page_per_page_user.json'
            ],
            [
                'filter' => ['query' => 'Another User'],
                'result' => 'get_users_by_name.json'
            ],
            [
                'filter' => ['query' => 'admin@example.com'],
                'result' => 'get_users_by_email.json'
            ],
            [
                'filter' => ['query' => 'Admin'],
                'result' => 'get_users_by_query.json'
            ],
            [
                'filter' => [
                    'name_query' => 'Mr Admin',
                    'email_query' => 'admin@example.com',
                    'query' => 'Admin',
                    'order_by' => 'created_at',
                    'desc' => false,
                    'role_ids' => [1, 2, 3],
                    'customer_ids' => [1, 2, 3],
                    'with' => ['customers']
                ],
                'result' => 'get_users_complex.json'
            ],
            [
                'filter' => [
                    'desc' => false,
                    'order_by' => 'name'
                ],
                'result' => 'get_users_check_order.json'
            ]
        ];
    }

    /**
     * @dataProvider  getSearchFilters
     *
     * @param array $filter
     * @param string $fixture
     */
    public function testSearch($filter, $fixture)
    {
        $response = $this->actingAs($this->admin)->json('get', '/users', $filter);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture($fixture, $response->json());
    }

    public function testResendInvitation()
    {
        $this->mockUniqueTokenGeneration('some_token');

        $response = $this->actingAs($this->admin)->json('post', '/users/1/resend-invitation');

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'set_password_hash' => 'some_token',
            'set_password_hash_created_at' => Carbon::now()
        ]);

        $this->assertMailEquals(InvitationMail::class, [
            [
                'emails' => 'admin@example.com',
                'fixture' => 'invitation_email.html'
            ]
        ]);
    }

    public function testResendInvitationNotExists()
    {
        $response = $this->actingAs($this->admin)->json('post', '/users/0/resend-invitation');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testResendInvitationNoPermissions()
    {
        $response = $this->actingAs($this->user)->json('post', '/users/1/resend-invitation');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testResendInvitationNoAuth()
    {
        $response = $this->json('post', '/users/1/resend-invitation');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testGetDashboard()
    {
        $response = $this->actingAs($this->customer)->json('get', '/dashboard');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_dashboard_by_user.json', $response->json());
    }

    public function testGetDashboardByAdmin()
    {
        $response = $this->actingAs($this->admin)->json('get', '/dashboard');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_dashboard_by_admin.json', $response->json());
    }

    public function testGetDashboardNoAuth()
    {
        $response = $this->json('get', '/dashboard');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
