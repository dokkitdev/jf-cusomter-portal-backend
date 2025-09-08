<?php

namespace App\Tests\Notify;

use App\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

class AssetReportValidationTest extends TestCase
{
    protected array $requiredOriginStates = [
    ];

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

        $response = $this->json('get', '/notify/asset-reports/validation');

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
                    'order_by' => 'uprn',
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
        $response = $this->actingAsById(101)->json('get', '/notify/asset-reports/validation', $filters);

        $response->assertOk();

        $this->assertEqualsFixture($responseFixture, $response->json());
    }

    /*************************************
     *        GET SEARCH FILTERS         *
     *************************************/

    public function getDataTestGetSearchFiltersAuthorization(): array
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
                'responseFixture' => 'authorization__not_admin__response.json',
            ],
            [
                'userId' => null,
                'statusCode' => Response::HTTP_UNAUTHORIZED,
                'responseFixture' => 'authorization__no_auth__response.json',
            ],
        ];
    }

    /**
     * @dataProvider getDataTestGetSearchFiltersAuthorization
     * @testCase get_search_filters
     */
    public function testGetSearchFiltersAuthorization(?int $userId, int $statusCode, ?string $responseFixture): void
    {
        if (isset($userId)) {
            $this->actingAsById($userId);
        }

        $response = $this->json('get', '/notify/asset-reports/validation/search-filters');

        $response->assertStatus($statusCode);

        if ($statusCode !== Response::HTTP_OK) {
            $this->assertEqualsFixture($responseFixture, $response->json());
        }
    }

    public function getDataTestGetSearchFilters(): array
    {
        return [
            [
                'request' => [],
                'response_fixture' => 'without_selected_site__response.json'
            ],
            [
                'request' => [
                    'site_id' => 11,
                ],
                'response_fixture' => 'with_selected_site__response.json'
            ],
        ];
    }

    /**
     * @dataProvider getDataTestGetSearchFilters
     * @testCase get_search_filters
     */
    public function testGetSearchFilters(array $request, string $responseFixture): void
    {
        $response = $this
            ->actingAsById(101)
            ->json('get', '/notify/asset-reports/validation/search-filters', $request);

        $response->assertOk();

        $this->assertEqualsFixture($responseFixture, $response->json());
    }
}
