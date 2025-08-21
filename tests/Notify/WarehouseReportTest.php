<?php

namespace App\Tests\Notify;

use App\Modules\Notify\Jobs\WarehouseReportJob;
use App\Tests\TestCase;
use Illuminate\Support\Facades\Queue;
use Symfony\Component\HttpFoundation\Response;

class WarehouseReportTest extends TestCase
{
    protected array $requiredOriginStates = [
        'notify_reports',
    ];

    public function setUp(): void
    {
        parent::setUp();

        Queue::fake();
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
}
