<?php

namespace App\Tests;

use App\Models\User;
use App\Tests\Support\SimproTestTrait;
use Symfony\Component\HttpFoundation\Response;

class SiteContactTest extends TestCase
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

    public function testCreate()
    {
        $this->mockPostSiteContact();

        $data = $this->getJsonFixture('create_site_contact.json');

        $response = $this->actingAs($this->customer)->json('post', '/site-contacts', $data);

        $response->assertStatus(Response::HTTP_CREATED);

        $data['simpro_contact_id'] = 11034;

        $this->assertDatabaseHas('site_contacts', $data);
    }

    public function testCreateNoPermission()
    {
        $data = $this->getJsonFixture('create_site_contact.json');

        $data['site_id'] = 5;

        $response = $this->actingAs($this->customer)->json('post', '/site-contacts', $data);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testCreateByAdmin()
    {
        $this->mockPostSiteContact();

        $data = $this->getJsonFixture('create_site_contact.json');

        $data['site_id'] = 4;

        $response = $this->actingAs($this->admin)->json('post', '/site-contacts', $data);

        $response->assertStatus(Response::HTTP_CREATED);

        $data['simpro_contact_id'] = 11034;

        $this->assertDatabaseHas('site_contacts', $data);
    }

    public function testCreateNoAuth()
    {
        $data = $this->getJsonFixture('create_site_contact.json');

        $response = $this->json('post', '/site-contacts', $data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUpdate()
    {
        $this->mockPatchSiteContact();

        $data = $this->getJsonFixture('update_site_contact.json');

        $response = $this->actingAs($this->customer)->json('put', '/site-contacts/1', $data);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas('site_contacts', $data);
    }

    public function testUpdateByAdmin()
    {
        $this->mockPatchSiteContact();

        $data = $this->getJsonFixture('update_site_contact.json');

        $response = $this->actingAs($this->admin)->json('put', '/site-contacts/3', $data);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas('site_contacts', $data);
    }

    public function testUpdateNotExists()
    {
        $data = $this->getJsonFixture('update_site_contact.json');

        $response = $this->actingAs($this->admin)->json('put', '/site-contacts/0', $data);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testUpdateNoPermission()
    {
        $data = $this->getJsonFixture('update_site_contact.json');

        $response = $this->actingAs($this->customer)->json('put', '/site-contacts/5', $data);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testUpdateNoAuth()
    {
        $data = $this->getJsonFixture('update_site_contact.json');

        $response = $this->json('put', '/site-contacts/1', $data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testDelete()
    {
        $this->mockDeleteSiteContact();

        $response = $this->actingAs($this->customer)->json('delete', '/site-contacts/1');

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('site_contacts', [
            'id' => 1
        ]);
    }

    public function testDeleteByAdmin()
    {
        $this->mockDeleteSiteContact();

        $response = $this->actingAs($this->admin)->json('delete', '/site-contacts/3');

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('site_contacts', [
            'id' => 3
        ]);
    }

    public function testDeleteNotExists()
    {
        $response = $this->actingAs($this->admin)->json('delete', '/site-contacts/0');

        $response->assertStatus(Response::HTTP_NOT_FOUND);

        $this->assertDatabaseMissing('site_contacts', [
            'id' => 0
        ]);
    }

    public function testDeleteNoPermission()
    {
        $response = $this->actingAs($this->customer)->json('delete', '/site-contacts/5');

        $response->assertStatus(Response::HTTP_NOT_FOUND);

        $this->assertDatabaseHas('site_contacts', [
            'id' => 5
        ]);
    }

    public function testDeleteNoAuth()
    {
        $response = $this->json('delete', '/site-contacts/1');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertDatabaseHas('site_contacts', [
            'id' => 1
        ]);
    }

    public function testGet()
    {
        $response = $this->actingAs($this->customer)->json('get', '/site-contacts/1');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_site_contact_fixture.json', $response->json());
    }

    public function testGetByAdmin()
    {
        $response = $this->actingAs($this->admin)->json('get', '/site-contacts/4');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_site_contact_by_admin_fixture.json', $response->json());
    }

    public function testGetNotExists()
    {
        $response = $this->actingAs($this->admin)->json('get', '/site-contacts/0');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testGetNoPermission()
    {
        $response = $this->actingAs($this->customer)->json('get', '/site-contacts/5');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testGetNoAuth()
    {
        $response = $this->json('get', '/site-contacts/1');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
