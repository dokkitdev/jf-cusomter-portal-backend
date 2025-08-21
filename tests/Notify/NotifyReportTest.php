<?php

namespace App\Tests\Notify;

use App\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

class NotifyReportTest extends TestCase
{
    protected array $requiredOriginStates = [
    ];

    /*************************************
     *              SEARCH               *
     *************************************/

    public function getDataTestCreate(): array
    {
        return [
            [
                //admin - success
                'userId' => 101,
                'statusCode' => Response::HTTP_OK,
                'responseFixture' => null,
            ],
            [
                'userId' => 102,
                'statusCode' => Response::HTTP_FORBIDDEN,
                'responseFixture' => 'search_authorization__not_admin__response.json',
            ],
            [
                'userId' => null,
                'statusCode' => Response::HTTP_UNAUTHORIZED,
                'responseFixture' => 'search_authorization__no_auth__response.json',
            ],
        ];
    }

    /**
     * @dataProvider getDataTestCreate
     * @testCase search
     */
    public function testSearchAuthorization(?int $userId, int $statusCode, ?string $responseFixture): void
    {
        if (isset($userId)) {
            $this->actingAsById($userId);
        }

        $response = $this->json('get', '/notify/reports');

        $response->assertStatus($statusCode);

        if ($statusCode !== Response::HTTP_OK) {
            $this->assertEqualsFixture($responseFixture, $response->json());
        }
    }

    public function getDataTestSearch(): array
    {
        return [
            [
                'filters' => [
                ],
                'responseFixture' => 'search__no_filters__response.json',
            ],
            [
                'filters' => [
                    'all' => 1,
                ],
                'responseFixture' => 'search__all__response.json',
            ],
            [
                'filters' => [
                    'page' => 2,
                    'per_page' => 1,
                ],
                'responseFixture' => 'search__page_per_page__response.json',
            ],
            [
                'filters' => [
                    'order_by' => 'created_at',
                    'desc' => 1,
                ],
                'responseFixture' => 'search__order_by_desc__response.json',
            ],
        ];
    }

    /**
     * @dataProvider getDataTestSearch
     * @testCase search
     */
    public function testSearch(array $filters, string $responseFixture): void
    {
        $response = $this->actingAsById(101)->json('get', '/notify/reports', $filters);

        $response->assertOk();

        $this->assertEqualsFixture($responseFixture, $response->json());
    }

    /*************************************
     *       DOWNLOAD LETTERS PDF        *
     *************************************/

    public function getDataTestDownloadLettersPdf(): array
    {
        return [
            [
                'userId' => 101,
                'reportId' => 202,
                'statusCode' => Response::HTTP_OK,
                'responseFixture' => 'success__response.pdf',
            ],
            [
                'userId' => 102,
                'reportId' => 202,
                'statusCode' => Response::HTTP_FORBIDDEN,
                'responseFixture' => 'not_admin__response.json',
            ],
            [
                'userId' => 101,
                'reportId' => 999,
                'statusCode' => Response::HTTP_NOT_FOUND,
                'responseFixture' => 'report_not_found__response.pdf',
            ],
            [
                'userId' => 101,
                'reportId' => 203,
                'statusCode' => Response::HTTP_NOT_FOUND,
                'responseFixture' => 'report_not_finished__response.pdf',
            ],
            [
                'userId' => null,
                'reportId' => 202,
                'statusCode' => Response::HTTP_UNAUTHORIZED,
                'responseFixture' => 'no_auth__response.pdf',
            ],
        ];
    }

    /**
     * @dataProvider getDataTestDownloadLettersPdf
     * @testCase download_letters_pdf
     */
    public function testDownloadLettersPdf(?int $userId, int $reportId, int $statusCode, string $responseFixture): void
    {
        if (isset($userId)) {
            $this->actingAsById($userId);
        }

        $response = $this->json('get', "/notify/reports/{$reportId}/letters");

        $response->assertStatus($statusCode);

        if ($statusCode === Response::HTTP_OK) {
            $this->assertEqualsFixture($responseFixture, $response->getContent());
        } else {
            $this->assertEqualsFixture($responseFixture, $response->json());
        }
    }
}
