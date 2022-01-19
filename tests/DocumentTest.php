<?php

namespace App\Tests;

use App\Models\User;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class DocumentTest extends TestCase
{
    protected $admin;
    protected $user;
    protected $data = ['media_id' => 5, 'title' => 'Docname', 'description' => "Docdesc..."];

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::find(1);
        $this->user = User::find(2);
    }

    public function testCreate()
    {
        $response = $this->actingAs($this->admin)->json('post', '/documents', $this->data);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertEqualsFixture('create_document_fixture.json', $response->json());

        $this->assertDatabaseHas('documents', $this->data);
    }

    public function testCreateNoPermission()
    {
        $response = $this->actingAs($this->user)->json('post', '/documents', $this->data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testCreateNoAuth()
    {
        $response = $this->json('post', '/documents', $this->data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUpdate()
    {
        $response = $this->actingAs($this->admin)->json('put', '/documents/1', $this->data);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas('documents', Arr::except($this->data, 'media_id'));
    }

    public function testUpdateNotExists()
    {
        $response = $this->actingAs($this->admin)->json('put', '/documents/0', $this->data);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testUpdateNoPermission()
    {
        $response = $this->actingAs($this->user)->json('put', '/documents/1', $this->data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testUpdateNoAuth()
    {
        $response = $this->json('put', '/documents/1', $this->data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testDelete()
    {
        $response = $this->actingAs($this->admin)->json('delete', '/documents/1');

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('documents', [
            'id' => 1
        ]);
    }

    public function testDeleteNotExists()
    {
        $response = $this->actingAs($this->admin)->json('delete', '/documents/0');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testDeleteNoPermission()
    {
        $response = $this->actingAs($this->user)->json('delete', '/documents/1');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testDeleteNoAuth()
    {
        $response = $this->json('delete', '/documents/1');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testGet()
    {
        $response = $this->actingAs($this->user)->json('get', '/documents/1');

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture('get_document_fixture.json', $response->json());
    }

    public function testGetNotExists()
    {
        $response = $this->actingAs($this->admin)->json('get', '/documents/0');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testGetNoAuth()
    {
        $response = $this->json('get', '/documents/1');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function getSearchFilters()
    {
        return [
            [
                'filter' => ['all' => 1],
                'result' => 'search_by_all_documents.json'
            ],
            [
                'filter' => [
                    'page' => 1,
                    'per_page' => 2,
                ],
                'result' => 'search_by_page_per_page_documents.json'
            ],
            [
                'filter' => [
                    'order_by' => 'created_at',
                    'desc' => true,
                    'created_at_from' => '2016-10-20',
                    'created_at_to' => '2016-10-21',
                    'title_query' => 'Docname 1',
                    'query' => 'Product main photo',
                ],
                'result' => 'search_documents_complex.json'
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
        $response = $this->actingAs($this->user)->json('get', '/documents', $filter);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEqualsFixture($fixture, $response->json());
    }
}
