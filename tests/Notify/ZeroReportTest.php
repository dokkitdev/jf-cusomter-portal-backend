<?php

namespace App\Tests\Notify;

use App\Modules\Notify\Jobs\ZeroReportJob;
use App\Tests\Support\MockHttpRequestServiceTrait;
use App\Tests\TestCase;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Dispatcher;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ZeroReportTest extends TestCase
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
        Carbon::setTestNow('2018-11-12T13:14:15');

        if (isset($userId)) {
            $this->actingAsById($userId);
        }

        $response = $this->json('post', '/notify/csv-reports/zero', [
            'date_from' => '2018-10-10',
            'date_to' => '2018-10-12',
        ]);

        if ($statusCode === Response::HTTP_NO_CONTENT) {
            $response->assertNoContent();

            Queue::assertPushed(ZeroReportJob::class, 1);
            Queue::assertPushed(ZeroReportJob::class, function (ZeroReportJob $job) {
                $this->assertEquals(CarbonImmutable::parse('2018-10-10'), $job->getDateFrom());
                $this->assertEquals(CarbonImmutable::parse('2018-10-12'), $job->getDateTo());
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

    public function getDataTestGenerate(): array
    {
        /**
         * Jobs:
         *  11 - skip - Total.ExTax != 0
         *  12 - check not archived status rendering
         *  13 - check archived status rendering
         *  14 - check Simpro API calls pagination
         */
        return [
            [
                'requestsChainFixture' => 'success__exists_data__requests_chain.json',
                'reportCsvFixture' => 'success__exists_data__report.csv',
                'dbChanges' => [
                    'notify_csv_reports' => 'success__exists_data__db_changes__notify_csv_reports.json',
                ],
            ],
            [
                'requestsChainFixture' => 'success__no_data__requests_chain.json',
                'reportCsvFixture' => 'success__no_data__report.csv',
                'dbChanges' => [
                    'notify_csv_reports' => 'success__no_data__db_changes__notify_csv_reports.json',
                ],
            ],
        ];
    }

    /**
     * @dataProvider getDataTestGenerate
     * @testCase generate
     */
    public function testGenerate(string $requestsChainFixture, string $reportCsvFixture, array $dbChanges): void
    {
        $this->mockHttpRequestService($this->getJsonFixture($requestsChainFixture));

        app(Dispatcher::class)->dispatchNow(new ZeroReportJob(CarbonImmutable::parse('2018-10-10'), CarbonImmutable::parse('2018-10-11'), 202));

        $this->assertEquals(['202.csv'], Storage::disk('csv_reports')->allFiles());

        $this->assertEqualsTextFixture($reportCsvFixture, Storage::disk('csv_reports')->get('202.csv'));

        foreach ($dbChanges as $table => $fixture) {
            $this->assertChangesEqualsFixture($table, $fixture);
        }
    }

    /**
     * @testCase generate
     */
    public function testGenerateFailed(): void
    {
        $job = new ZeroReportJob(CarbonImmutable::parse('2018-10-10'), CarbonImmutable::parse('2018-10-10'), 202);

        $job->failed();

        $this->assertChangesEqualsFixture('notify_csv_reports', 'job_failed__db_changes__notify_csv_reports.json');
    }
}
