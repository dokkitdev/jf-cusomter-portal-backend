<?php

namespace App\Tests\Notify;

use App\Modules\Notify\Jobs\AssetReportJob;
use App\Tests\Support\MockHttpRequestServiceTrait;
use App\Tests\TestCase;
use Illuminate\Bus\Dispatcher;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class AssetReportTest extends TestCase
{
    use MockHttpRequestServiceTrait;

    protected array $requiredOriginStates = [
        'notify_csv_reports',
    ];

    public function setUp(): void
    {
        parent::setUp();

        Queue::fake();
        Storage::fake('csv_reports');
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
                    'notify_csv_reports' => 'success__db_changes__notify_csv_reports.json',
                ],
            ],
            [
                'userId' => 102,
                'statusCode' => Response::HTTP_FORBIDDEN,
                'responseFixture' => 'not_admin__response.json',
                'dbChanges' => [
                    'notify_csv_reports' => null,
                ],
            ],
            [
                'userId' => null,
                'statusCode' => Response::HTTP_UNAUTHORIZED,
                'responseFixture' => 'no_auth__response.json',
                'dbChanges' => [
                    'notify_csv_reports' => null,
                ],
            ],
        ];
    }

    /**
     * @dataProvider getDataTestCreate
     * @testCase create
     */
    public function testCreate(?int $userId, int $statusCode, ?string $responseFixture, array $dbChanges): void
    {
        if (isset($userId)) {
            $this->actingAsById($userId);
        }

        $response = $this->json('post', '/notify/csv-reports/asset');

        if ($statusCode === Response::HTTP_NO_CONTENT) {
            $response->assertNoContent();

            Queue::assertPushed(AssetReportJob::class, 1);
            Queue::assertPushed(AssetReportJob::class, function (AssetReportJob $job) {
                $this->assertEquals(1, $job->getCsvReportId());

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

    /**
     * Rows:
     * 1. common
     * 2. job stage not Progress or Pending
     * 3. job_due_date not set
     * 4. job_stage Archived
     * 5. next_scheduled_appointment_date set, date < now
     * 6. job_due_date < now + 1 day
     * 7. job_due_date not set, next_service_date not set
     * 8. last_service_date not set, service_level_start_date not set, next_service_date not set
     * 9. all fields not set
     *
     * @testCase generate
     */
    public function testGenerate(): void
    {
        app(Dispatcher::class)->dispatchNow(new AssetReportJob(202));

        $this->assertEquals(['202.csv'], Storage::disk('csv_reports')->allFiles());

        $this->assertEqualsTextFixture('success__report.csv', Storage::disk('csv_reports')->get('202.csv'));

        $this->assertChangesEqualsFixture('notify_csv_reports', 'success__db_changes__notify_csv_reports.json');
    }

    /**
     * @testCase generate
     */
    public function testGenerateFailed(): void
    {
        $job = new AssetReportJob(202);

        $job->failed();

        $this->assertChangesEqualsFixture('notify_csv_reports', 'failed__db_changes__notify_csv_reports.json');
    }
}
