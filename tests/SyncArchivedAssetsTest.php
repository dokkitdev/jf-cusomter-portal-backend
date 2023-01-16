<?php

namespace App\Tests;

use App\Jobs\SyncArchivedAssets\SyncArchivedAssetsInitJob;
use App\Jobs\SyncArchivedAssets\SyncArchivedAssetsProcessAssetJob;
use App\Jobs\SyncArchivedAssets\SyncArchivedAssetsProcessPageJob;
use App\Tests\Support\MockHttpRequestServiceTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;

class SyncArchivedAssetsTest extends TestCase
{
    use MockHttpRequestServiceTrait;

    public function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    public function testCronJobDispatched()
    {
        Carbon::setTestNow('2018-11-11 00:00:00');

        $this->artisan('schedule:run');

        Queue::assertPushed(SyncArchivedAssetsInitJob::class, 1);
    }

    public function testCronJobNotDispatched()
    {
        Carbon::setTestNow('2018-11-12 00:00:00');

        $this->artisan('schedule:run');

        Queue::assertNotPushed(SyncArchivedAssetsInitJob::class);
    }

    public function testInit()
    {
        $this->mockHttpRequestService($this->getJsonFixture('init__requests_chain.json'));

        $expectedPages = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11];

        (new SyncArchivedAssetsInitJob())->handle();

        Queue::assertPushed(SyncArchivedAssetsProcessPageJob::class, count($expectedPages));
        Queue::assertPushed(SyncArchivedAssetsProcessPageJob::class, function (SyncArchivedAssetsProcessPageJob $job) use (&$expectedPages) {
            $this->assertEquals(array_shift($expectedPages), $job->getPage());

            return true;
        });
    }

    public function testInitNoAssets()
    {
        $this->mockHttpRequestService($this->getJsonFixture('init__no_assets__requests_chain.json'));

        (new SyncArchivedAssetsInitJob())->handle();

        Queue::assertNothingPushed();
    }

    public function testProcessPage()
    {
        $this->mockHttpRequestService($this->getJsonFixture('process_page__requests_chain.json'));

        (new SyncArchivedAssetsProcessPageJob(7))->handle();

        $expectedSimproAssetIds = [333, 555];

        Queue::assertPushed(SyncArchivedAssetsProcessAssetJob::class, count($expectedSimproAssetIds));
        Queue::assertPushed(SyncArchivedAssetsProcessAssetJob::class, function (SyncArchivedAssetsProcessAssetJob $job) use (&$expectedSimproAssetIds) {
            $this->assertEquals(array_shift($expectedSimproAssetIds), $job->getSimproAssetId());

            return true;
        });
    }

    public function testProcessPageNoOutOfSyncAssets()
    {
        $this->mockHttpRequestService($this->getJsonFixture('process_page__no_out_of_sync_assets__requests_chain.json'));

        (new SyncArchivedAssetsProcessPageJob(7))->handle();

        Queue::assertNothingPushed();
    }
}
