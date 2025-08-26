<?php

namespace App\Tests\Notify;

use App\Modules\Notify\Jobs\WarehouseReportJob;
use App\Tests\Support\AssertPdfTrait;
use App\Tests\Support\MockHttpRequestServiceTrait;
use App\Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class WarehouseReportTest extends TestCase
{
    use AssertPdfTrait;
    use MockHttpRequestServiceTrait;

    protected array $requiredOriginStates = [
        'notify_reports',
    ];

    public function setUp(): void
    {
        parent::setUp();

        Queue::fake();
        Storage::fake('pdf_reports');
    }

    /*************************************
     *              CREATE               *
     *************************************/

    public function getDataTestCreate(): array
    {
        return [
            [
                'userId' => 101,
                'statusCode' => Response::HTTP_NO_CONTENT,
                'responseFixture' => null,
                'dbChanges' => [
                    'notify_reports' => 'success__db_changes__notify_reports.json',
                ],
                'testCase' => 'create__no_in_progress_reports',
            ],
            [
                'userId' => 102,
                'statusCode' => Response::HTTP_FORBIDDEN,
                'responseFixture' => 'not_admin__response.json',
                'dbChanges' => [
                    'notify_reports' => null,
                ],
                'testCase' => 'create__no_in_progress_reports',
            ],
            [
                'userId' => null,
                'statusCode' => Response::HTTP_UNAUTHORIZED,
                'responseFixture' => 'no_auth__response.json',
                'dbChanges' => [
                    'notify_reports' => null,
                ],
                'testCase' => 'create__no_in_progress_reports',
            ],
            [
                'userId' => 101,
                'statusCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'responseFixture' => 'response.json',
                'dbChanges' => [
                    'notify_reports' => null,
                ],
                'testCase' => 'create__warehouse_project_report_in_progress',
            ],
            [
                'userId' => 101,
                'statusCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'responseFixture' => 'response.json',
                'dbChanges' => [
                    'notify_reports' => null,
                ],
                'testCase' => 'create__warehouse_service_report_in_progress',
            ],
        ];
    }

    /**
     * @dataProvider getDataTestCreate
     * @providedTestCase
     */
    public function testCreate(?int $userId, int $statusCode, ?string $responseFixture, array $dbChanges): void
    {
        if (isset($userId)) {
            $this->actingAsById($userId);
        }

        $response = $this->json('post', '/notify/reports/warehouse', [
            'days_count' => 1,
        ]);

        if ($statusCode === Response::HTTP_NO_CONTENT) {
            $response->assertNoContent();

            Queue::assertPushed(WarehouseReportJob::class, 1);
            Queue::assertPushed(WarehouseReportJob::class, function (WarehouseReportJob $job) {
                $this->assertEquals(204, $job->getWarehouseProjectReportId());
                $this->assertEquals(205, $job->getWarehouseServiceReportId());
                $this->assertEquals(1, $job->getDaysCount());
                $this->assertEquals(Carbon::parse('2018-11-11 11:12:11'), $job->delay);

                return true;
            });
        } else {
            $response->assertStatus($statusCode);
            $this->assertEqualsFixture($responseFixture, $response->json());

            Queue::assertNothingPushed();
        }

        foreach ($dbChanges as $table => $fixture) {
            if (is_null($fixture)) {
                $this->assertNoChanges($table);
            } else {
                $this->assertChangesEqualsFixture($table, $fixture);
            }
        }
    }

    /*************************************
     *             GENERATE              *
     *************************************/

    public function getDataTestGenerate(): array
    {
        /**
         * Dates:
         *  2018-11-12
         *      Schedules Page 1
         *          Jobs:
         *              11 - skip - stage not pending or progress
         *              12 - skip - type not project or service
         *              13 - skip - empty sections
         *              14 - skip - empty cost centers
         *              15 - skip - empty cost centers total ex tax
         *              16 - OK - type project - stage pending accepted - in project pdf
         *                  CostCenters:
         *                      1611:
         *                          StockItems:
         *                              16111 - skip - Required < Assigned
         *                              16112 - skip - Required = Assigned
         *                              16113 - skip - Assigned != 0
         *                              16114 - OK
         *                      1612:
         *                          StockItems:
         *                              16121 - OK
         *                      1621
         *                          StockItems
         *                              NO DATA
         *              17 - OK - type project - two jobs on same pdf page (date) - stage progress accepted - in project pdf
         *                  CostCenters:
         *                      1711:
         *                          StockItems:
         *                              17111 - OK
         *              18 - OK - type service - in service pdf
         *      Schedules Page 2
         *          NO DATA
         *  2018-11-12
         *      Schedules Page 1
         *          NO DATA - empty page in pdf
         */
        return [

            [
                'requestsChainFixture' => 'success__exists_data__requests_chain.json',
                'projectReportPdfFixture' => 'success__exists_data__pdf_project.pdf',
                'serviceReportPdfFixture' => 'success__exists_data__pdf_service.pdf',
                'dbChanges' => [
                    'notify_reports' => 'success__exists_data__db_changes__notify_reports.json',
                ],
            ],
            [
                'requestsChainFixture' => 'success__no_data__requests_chain.json',
                'projectReportPdfFixture' => 'success__no_data__pdf_project.pdf',
                'serviceReportPdfFixture' => 'success__no_data__pdf_service.pdf',
                'dbChanges' => [
                    'notify_reports' => 'success__no_data__db_changes__notify_reports.json',
                ],
            ],
        ];
    }

    /**
     * @dataProvider getDataTestGenerate
     * @testCase generate
     */
    public function testGenerate(string $requestsChainFixture, string $projectReportPdfFixture, string $serviceReportPdfFixture, array $dbChanges): void
    {
        $this->mockHttpRequestService($this->getJsonFixture($requestsChainFixture));

        app(Dispatcher::class)->dispatchNow(new WarehouseReportJob(2, 204, 206));

        $this->assertEquals(['204.pdf', '206.pdf'], Storage::disk('pdf_reports')->allFiles());

        $this->assertPdfEquals($this->getFixture($projectReportPdfFixture), Storage::disk('pdf_reports')->get('204.pdf'));
        $this->assertPdfEquals($this->getFixture($serviceReportPdfFixture), Storage::disk('pdf_reports')->get('206.pdf'));

        foreach ($dbChanges as $table => $fixture) {
            $this->assertChangesEqualsFixture($table, $fixture);
        }
    }

    /**
     * @testCase generate
     */
    public function testGenerateFailed(): void
    {
        $job = new WarehouseReportJob(1, 204, 206);

        $job->failed();

        $this->assertChangesEqualsFixture('notify_reports', 'job_failed__db_changes__notify_reports.json');
    }
}
