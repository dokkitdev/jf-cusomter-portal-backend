<?php

namespace App\Tests;

use App\Models\Site;
use App\Models\SiteContact;
use App\Models\SimproLog;
use App\Models\User;
use App\Tests\Support\SimproTestTrait;

class SimproLogTest extends TestCase
{
    use SimproTestTrait;

    protected $admin;
    protected $user;
    protected $customer;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::find(1);
        $this->user = User::find(2);
        $this->customer = User::find(3);
    }

    public function testGetSitesToLogCommand()
    {
        $this->mockGetSites();

        $this->artisan('simpro:save-simpro-log sites')->assertExitCode(0);

        $simproLog = SimproLog::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_log_fixture.json', $simproLog);
    }

    public function testHandleSitesLogCommand()
    {
        $this->mockCreateOrUpdateSite();

        $this->artisan('simpro:handle-log sites')->assertExitCode(0);

        $simproLog = SimproLog::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('simpro_log_create_or_update_event_fixture.json', $simproLog);

        $sites = Site::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('site_create_or_update_event_fixture.json', $sites);

        $siteContacts = SiteContact::orderBy('id')->get()->toArray();
        $this->assertEqualsFixture('site_contacts_create_or_update_event_fixture.json', $siteContacts);
     }
}
