<?php

namespace App\Tests;

use App\Services\UserService;
use App\Tests\Support\MockHttpRequestServiceTrait;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class JobTest extends TestCase
{
    use MockHttpRequestServiceTrait;

    public function getTestCreateRejectedData(): array
    {
        return [
            [
                'user_id' => 5,
                'site_id' => 1,
                'status_code' => Response::HTTP_NOT_FOUND,
                'response_fixture' => 'not_existed_site__response.json',
            ],
            [
                'user_id' => 5,
                'site_id' => 9,
                'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'response_fixture' => 'role_customer__has_permission__no_customer_in_site__response.json',
            ],
            [
                'user_id' => 6,
                'site_id' => 7,
                'status_code' => Response::HTTP_NOT_FOUND,
                'response_fixture' => 'role_customer__no_permission__response.json',
            ],
            [
                'user_id' => 7,
                'site_id' => 9,
                'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'response_fixture' => 'role_not_customer__no_customer_in_site__response.json',
            ],
            [
                'user_id' => null,
                'site_id' => 9,
                'status_code' => Response::HTTP_UNAUTHORIZED,
                'response_fixture' => 'no_auth__response.json',
            ],
        ];
    }

    /**
     * @dataProvider getTestCreateRejectedData
     * @testCase create_job_in_simpro__rejected
     */
    public function testCreateRejected($userId, $siteId, $statusCode, $responseFixture): void
    {
        if (isset($userId)) {
            $user = app(UserService::class)->find($userId);

            $this->actingAs($user);
        }

        $response = $this->json('post', '/jobs/create-in-simpro', [
            'site_id' => $siteId,
            'description' => 'Test job...',
            'files' => [
                UploadedFile::fake()->image('file1.png', 600, 600),
                UploadedFile::fake()->image('file2.png', 600, 600)
            ]
        ]);

        $response->assertStatus($statusCode);

        $this->assertEqualsFixture($responseFixture, $response->json());
    }

    public function getTestCreateAsCustomerData(): array
    {
        return [
            ['create__as_customer'],
            ['create__as_not_customer'],
        ];
    }

    /**
     * @dataProvider getTestCreateAsCustomerData
     * @providedTestCase
     */
    public function testCreate(): void
    {
        $this->mockHttpRequestService($this->getJsonFixture('requests_chain.json'));

        $user = app(UserService::class)->find(5);

        $response = $this->actingAs($user)->json('post', '/jobs/create-in-simpro', [
            'site_id' => 7,
            'description' => 'Test job...',
            'files' => [
                new UploadedFile($this->getFixturePath('file1.png'), 'file1.png', null, null, true),
                new UploadedFile($this->getFixturePath('file2.png'), 'file2.png', null, null, true),
            ]
        ]);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertEqualsFixture('response.json', $response->json());
    }
}
