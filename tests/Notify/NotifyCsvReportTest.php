<?php

namespace App\Tests\Notify;

use App\Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class NotifyCsvReportTest extends TestCase
{
    protected array $requiredOriginStates = [
    ];

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake('csv_reports');
    }

    /*************************************
     *              SEARCH               *
     *************************************/

    public function getDataTestSearchAuthorization(): array
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
     * @dataProvider getDataTestSearchAuthorization
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
                    'order_by' => 'file_name',
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
        $response = $this->actingAsById(101)->json('get', '/notify/csv-reports', $filters);

        $response->assertOk();

        $this->assertEqualsFixture($responseFixture, $response->json());
    }

    /*************************************
     *            DOWNLOAD CSV           *
     *************************************/

    public function getDataTestDownloadCsv(): array
    {
        return [
            [
                'userId' => 101,
                'reportId' => 202,
                'statusCode' => Response::HTTP_OK,
                'responseFixture' => 'success__response.csv',
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
                'responseFixture' => 'report_not_found__response.json',
            ],
            [
                'userId' => 101,
                'reportId' => 203,
                'statusCode' => Response::HTTP_NOT_FOUND,
                'responseFixture' => 'report_not_finished__response.json',
            ],
            [
                'userId' => null,
                'reportId' => 202,
                'statusCode' => Response::HTTP_UNAUTHORIZED,
                'responseFixture' => 'no_auth__response.json',
            ],
        ];
    }

    /**
     * @dataProvider getDataTestDownloadCsv
     * @testCase download_csv
     */
    public function testDownloadLCsv(?int $userId, int $reportId, int $statusCode, string $responseFixture): void
    {
        Storage::disk('csv_reports')->put("{$reportId}.csv", $this->getFixture('success__response.csv'));

        if (isset($userId)) {
            $this->actingAsById($userId);
        }

        $response = $this->json('get', "/notify/csv-reports/{$reportId}/download");

        $response->assertStatus($statusCode);

        if ($statusCode === Response::HTTP_OK) {
            $this->assertEqualsTextFixture($responseFixture, $response->baseResponse->getFile()->getContent());
            $response->assertHeader('Content-Type', 'text/csv');
            $response->assertHeader('Content-Disposition', 'attachment; filename="file_02.csv"');
        } else {
            $this->assertEqualsFixture($responseFixture, $response->json());
        }
    }
}
